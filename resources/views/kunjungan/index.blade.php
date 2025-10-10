@extends('layouts.utama')
@section('konten')
    <div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">Dashboard</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Depan</a></li>
                <li class="breadcrumb-item active">Kunjungan</li>
            </ol>
            @if (Auth::user() or Generate::CekAkses(\Request::getClientIp(true)))
                <a href="{{route('kunjungan.tambah')}}" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Kunjungan Baru</a>
            @endif
        </div>
    </div>
</div>
<!----pesan error--->
<div class="row">
    <div class="col-lg-12 col-sm-12">
        @if (Session::has('message'))
        <div class="alert alert-{{ Session::get('message_type') }}" id="waktu2" style="margin-top:10px;">
            @if (Session::has('message_header'))
                <h4 class="alert-heading">{!! Session::get('message_header') !!}</h4>
            @endif
            {!! Session::get('message') !!}
        </div>
        @endif
    </div>
</div>
<!----batas pesan error-->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            Petugas Hari {{Tanggal::HariPanjang(\Carbon\Carbon::now())}} :
                            <br />
                            <span class="badge badge-success">
                                @if ($PetugasJaga->tanggal_petugas1_uid)
                                    {{$PetugasJaga->Petugas1->name}}
                                @else
                                    -
                                @endif
                            </span>
                            <span class="badge badge-info">
                                @if ($PetugasJaga->tanggal_petugas2_uid)
                                    {{$PetugasJaga->Petugas2->name}}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="col-lg-4 text-right">
                            @if (Auth::User()->user_level == 'admin')
                                <a href="#" class="btn btn-info kirimnotifjaga">Kirim Notif</a>
                                <a href="#" class="btn btn-danger sinkronpetugas">Sinkron Petugas</a>
                            @endif
                        </div>
                    </div>
                    <!--batas-->
                    <center id="preloading">
                        <button class="btn btn-success" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading, Memproses data kunjungan...
                        </button>
                    </center>
                    <center id="pesanerror">
                        <div class="alert alert-success m-5"><span id="tekserror"></span></div>
                    </center>
                    <div class="m-t-40">
                        <h4 class="card-title text-center">
                            Data Kunjungan
                        </h4>
                        <table id="dTabel" class="tabeldata display table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>UID</th>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Keperluan</th>
                                    <th>Tindak Lanjut</th>
                                    <th>Antrian</th>
                                    <th>Mulai</th>
                                    <th>Akhir</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('kunjungan.modal-kunjungan')
    @include('kunjungan.modal-feedback')
    @include('kunjungan.modal-tindaklanjut')
    @include('kunjungan.modal-tujuan')
    @include('kunjungan.modal-jenis')
    @include('kunjungan.modal-whatsapp')
    @include('kunjungan.modal-petugas')
    @include('kunjungan.modal-flagantrian')
@endsection

@section('css')
    <!-- Date picker plugins css -->
    <link href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--alerts CSS -->
    <link href="{{ asset('assets/node_modules/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/node_modules/Magnific-Popup-master/dist/magnific-popup.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css') }}">
    <link href="{{asset('assets/node_modules/select2/dist/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        #preloading,
        #pesanerror,
        #moreTeks {
            display: none;
        }
    </style>
@stop
@section('js')
    <script src="{{ asset('dist/js/pages/jasny-bootstrap.js') }}"></script>
    <!-- Date Picker Plugin JavaScript -->
    <script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- This is data table -->
    <script src="{{ asset('assets/node_modules/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js') }}"></script>
    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <!-- end - This is for export functionality only -->
    <!---moment.js url--->
    <script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <!-- Sweet-Alert  -->
    <script src="{{asset('assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <!-- Magnific popup JavaScript -->
    <script src="{{asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}"></script>
    <script src="{{asset('assets/node_modules/select2/dist/js/select2.full.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            // DataTable
            $('#dTabel').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kunjungan.pagelist') }}",
                columns: [
                    {data: 'kunjungan_uid',orderable: false},
                    {data: 'pengunjung_nama'},
                    {data: 'kunjungan_tanggal'},
                    {data: 'kunjungan_keperluan'},
                    {data: 'kunjungan_tindak_lanjut'},
                    {data: 'kunjungan_teks_antrian'},
                    {data: 'kunjungan_jam_datang'},
                    {data: 'kunjungan_jam_pulang'},
                    {data: 'kunjungan_petugas_uid'},
                    {data: 'aksi', orderable: false},
                ],
                order: [2,'desc'],
                dom: 'Bfrtip',
                iDisplayLength: 20,
                buttons: [
                    'copy', 'excel', 'print'
                ],
                responsive: false,
                "fnDrawCallback": function() {
                    $('.image-popup').magnificPopup({
                        type: 'image',
                        closeOnContentClick: true,
                        closeBtnInside: false,
                        fixedContentPos: true,

                        image: {
                            verticalFit: true
                        },
                        zoom: {
                            enabled: true,
                            duration: 300 // don't foget to change the duration also in CSS
                        },

                    });
                    $('.tabeldata').on('click','.kirimnomorantrian',function(e) {
                        e.preventDefault();
                        var uid = $(this).data('uid');
                        var id = $(this).data('id');
                        var nama = $(this).data('nama');
                        var email = $(this).data('email');
                        Swal.fire({
                            title: 'Kirim nomor antrian?',
                            text: "Nomor Antrian an. " + nama +
                                " dikirim ke alamat email " + email +
                                " sekarang?",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Kirim'
                        }).then((result) => {
                            if (result.value) {
                                //response ajax disini
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                            'meta[name="csrf-token"]').attr(
                                            'content')
                                    }
                                });
                                $.ajax({
                                    url: '{{ route("kunjungan.kirimnomor") }}',
                                    method: 'post',
                                    data: {
                                        uid: uid,
                                        id: id,
                                        nama: nama,
                                        email: email
                                    },
                                    cache: false,
                                    dataType: 'json',
                                    beforeSend: function() {
                                        Swal.fire({
                                            title: "Processing...",
                                            html: "Silakan tunggu sampai proses selesai.",
                                            allowEscapeKey: false,
                                            allowOutsideClick: false,
                                            onOpen: () => {
                                            swal.showLoading();
                                            }
                                        });
                                    },
                                    success: function(data) {
                                        if (data.status == true) {
                                            Swal.hideLoading(); // Hide the loading spinner
                                            Swal.fire(
                                                'Berhasil!',
                                                '' + data.message + '',
                                                'success'
                                            ).then(function() {
                                                $('#dTabel')
                                                    .DataTable()
                                                    .ajax.reload(
                                                        null, false
                                                    );
                                            });
                                        } else {
                                            Swal.hideLoading(); // Hide the loading spinner
                                            Swal.fire(
                                                'Error!',
                                                '' + data.message + '',
                                                'error'
                                            );
                                        }

                                    },
                                    error: function() {
                                        Swal.hideLoading(); // Hide the loading spinner
                                        Swal.fire(
                                            'Error',
                                            'Koneksi Error',
                                            'error'
                                        );
                                    }

                                });

                            }
                        })
                    });
                    $('.tabeldata').on('click','.kirimlinkfeedback',function(e) {
                        e.preventDefault();
                        var uid = $(this).data('uid');
                        var id = $(this).data('id');
                        var nama = $(this).data('nama');
                        var email = $(this).data('email');
                        Swal.fire({
                            title: 'Kirim Link Feedback?',
                            text: "Link feedback kunjungan an. " + nama +
                                " dikirim ke alamat email " + email +
                                " sekarang?",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Kirim'
                        }).then((result) => {
                            if (result.value) {
                                //response ajax disini
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                            'meta[name="csrf-token"]').attr(
                                            'content')
                                    }
                                });
                                $.ajax({
                                    url: '{{ route("kunjungan.kirimlinkfeedback") }}',
                                    method: 'post',
                                    data: {
                                        uid: uid,
                                        id: id,
                                        nama: nama,
                                        email: email
                                    },
                                    cache: false,
                                    dataType: 'json',
                                    beforeSend: function() {
                                        Swal.fire({
                                            title: "Processing...",
                                            html: "Silakan tunggu sampai proses selesai.",
                                            allowEscapeKey: false,
                                            allowOutsideClick: false,
                                            onOpen: () => {
                                            swal.showLoading();
                                            }
                                        });
                                    },
                                    success: function(data) {
                                        if (data.status == true) {
                                            Swal.hideLoading();
                                            Swal.fire(
                                                'Berhasil!',
                                                '' + data.message + '',
                                                'success'
                                            ).then(function() {
                                                $('#dTabel')
                                                    .DataTable()
                                                    .ajax.reload(
                                                        null, false
                                                    );
                                            });
                                        } else {
                                            Swal.hideLoading();
                                            Swal.fire(
                                                'Error!',
                                                '' + data.message + '',
                                                'error'
                                            );
                                        }

                                    },
                                    error: function() {
                                        Swal.hideLoading();
                                        Swal.fire(
                                            'Error',
                                            'Koneksi Error',
                                            'error'
                                        );
                                    }

                                });

                            }
                        })
                    });
                    $('.tabeldata').on('click','.kirimlinkskd',function(e) {
                        e.preventDefault();
                        var pengunjung_uid = $(this).data('puid');
                        var kunjungan_uid = $(this).data('uid');
                        var nama = $(this).data('nama');
                        var email = $(this).data('email');
                        Swal.fire({
                            title: 'Kirim Link SKD?',
                            text: "Link SKD akan dikirim kan ke alamat email "+email+" ("+nama+") sekarang?",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Kirim'
                        }).then((result) => {
                            if (result.value) {
                                //response ajax disini
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                            'meta[name="csrf-token"]').attr(
                                            'content')
                                    }
                                });
                                $.ajax({
                                    url: '{{ route("pengunjung.kirimlinkskd") }}',
                                    method: 'post',
                                    data: {
                                        pengunjung_uid: pengunjung_uid,
                                        kunjungan_uid: kunjungan_uid,
                                        nama: nama,
                                        email: email
                                    },
                                    cache: false,
                                    dataType: 'json',
                                    beforeSend: function() {
                                        Swal.fire({
                                            title: "Processing...",
                                            html: "Silakan tunggu sampai proses selesai.",
                                            allowEscapeKey: false,
                                            allowOutsideClick: false,
                                            onOpen: () => {
                                            swal.showLoading();
                                            }
                                        });
                                    },
                                    success: function(data) {
                                        if (data.status == true) {
                                            Swal.hideLoading();
                                            Swal.fire(
                                                'Berhasil!',
                                                '' + data.message + '',
                                                'success'
                                            ).then(function() {
                                                $('#dTabel')
                                                    .DataTable()
                                                    .ajax.reload(
                                                        null, false
                                                    );
                                            });
                                        } else {
                                            Swal.hideLoading();
                                            Swal.fire(
                                                'Error!',
                                                '' + data.message + '',
                                                'error'
                                            );
                                        }

                                    },
                                    error: function() {
                                        Swal.hideLoading();
                                        Swal.fire(
                                            'Error',
                                            'Koneksi Error',
                                            'error'
                                        );
                                    }

                                });

                            }
                        })
                    });
                    $('.tabeldata').on('click','.mulailayanan',function(e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        var uid = $(this).data('uid');
                        var nama = $(this).data('nama');
                        var tanggal = $(this).data('tanggal');
                        Swal.fire({
                            title: 'Mulai layanan?',
                            text: "Data kunjungan " + nama + " tanggal " + tanggal +
                                " akan mulai dilayani",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Mulai'
                        }).then((result) => {
                            if (result.value) {
                                //response ajax disini
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                            'meta[name="csrf-token"]').attr(
                                            'content')
                                    }
                                });
                                $.ajax({
                                    url: '{{ route("kunjungan.mulai") }}',
                                    method: 'post',
                                    data: {
                                        id: id,
                                        uid: uid,
                                        nama: nama,
                                        tanggal: tanggal
                                    },
                                    cache: false,
                                    dataType: 'json',
                                    success: function(data) {
                                        if (data.status == true) {
                                            Swal.fire(
                                                'Berhasil!',
                                                '' + data.message + '',
                                                'success'
                                            ).then(function() {
                                                $('#dTabel')
                                                    .DataTable()
                                                    .ajax.reload(
                                                        null, false
                                                    );
                                            });
                                        } else {
                                            Swal.fire(
                                                'Error!',
                                                '' + data.message + '',
                                                'error'
                                            );
                                        }

                                    },
                                    error: function() {
                                        Swal.fire(
                                            'Error',
                                            'Koneksi Error',
                                            'error'
                                        );
                                    }

                                });

                            }
                        })
                    });
                    //batas mulai
                    //mulai layanan klik
                    $('.tabeldata').on('click','.akhirlayanan',function(e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        var uid = $(this).data('uid');
                        var nama = $(this).data('nama');
                        var tanggal = $(this).data('tanggal');
                        Swal.fire({
                            title: 'Akhir layanan?',
                            text: "Data kunjungan " + nama + " tanggal " + tanggal +
                                " layanan akan diakhiri",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Akhiri'
                        }).then((result) => {
                            if (result.value) {
                                //response ajax disini
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                $.ajax({
                                    url: '{{ route("kunjungan.akhir") }}',
                                    method: 'post',
                                    data: {
                                        id: id,
                                        uid: uid,
                                        nama: nama,
                                        tanggal: tanggal
                                    },
                                    cache: false,
                                    dataType: 'json',
                                    beforeSend: function() {
                                        Swal.fire({
                                            title: "Processing...",
                                            html: "Silakan tunggu sampai proses selesai.",
                                            allowEscapeKey: false,
                                            allowOutsideClick: false,
                                            onOpen: () => {
                                            swal.showLoading();
                                            }
                                        });
                                    },
                                    success: function(data) {
                                        if (data.status == true) {
                                            Swal.hideLoading();
                                            Swal.fire(
                                                'Berhasil!',
                                                '' + data.message + '',
                                                'success'
                                            ).then(function() {
                                                $('#dTabel')
                                                    .DataTable()
                                                    .ajax.reload(
                                                        null, false
                                                    );
                                            });
                                        } else {
                                            Swal.hideLoading();
                                            Swal.fire(
                                                'Error!',
                                                '' + data.message + '',
                                                'error'
                                            );
                                        }

                                    },
                                    error: function() {
                                        Swal.hideLoading();
                                        Swal.fire(
                                            'Error',
                                            'Koneksi Error',
                                            'error'
                                        );
                                    }

                                });

                            }
                        })
                    });
                    //batas mulai
                    //klik more
                    $('.tabeldata').on('click','.btnMore',function(e) {
                        let td = $(this).closest('td');
                        let display =  td.children('#dots').css("display");
                        if (display == "none") {
                            td.children("#dots").show();
                            td.children('.btnMore').text('more');
                            td.children('.btnMore').removeClass("btn-danger").addClass("btn-info");
                            td.children("#moreTeks").hide();
                        }
                        else {
                            td.children("#dots").hide();
                            td.children('.btnMore').text('less');
                            td.children('.btnMore').removeClass("btn-info").addClass("btn-danger");
                            td.children("#moreTeks").show();
                        }
                        e.stopImmediatePropagation();
                    });
                    //hapus kunjungan
                    //copy link feedback
                    $('.tabeldata').on('click','.copyurlfeedback',function(e) {
                        e.preventDefault();

                        var copyText = $(this).attr('href');
                        document.addEventListener('copy', function(e) {
                            e.clipboardData.setData('text/plain', copyText);
                            e.preventDefault();
                        }, true);

                        document.execCommand('copy');

                        Swal.fire(
                            'Berhasil',
                            'Link url feedback sudah tercopy',
                            'success'
                        );

                    });
                    //batas
                    $('.tabeldata').on('click','.hapuskunjungan',function(e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        var uid = $(this).data('uid');
                        var nama = $(this).data('nama');
                        var tanggal = $(this).data('tanggal');
                        Swal.fire({
                            title: 'Akan dihapus?',
                            text: "Data kunjungan an. " + nama + " tanggal "+ tanggal +" akan dihapus permanen",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Hapus'
                        }).then((result) => {
                            if (result.value) {
                                //response ajax disini
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                $.ajax({
                                    url: '{{ route("kunjungan.hapus") }}',
                                    method: 'post',
                                    data: {
                                        uid: uid,
                                        id: id,
                                        nama: nama,
                                        tanggal: tanggal
                                    },
                                    cache: false,
                                    dataType: 'json',
                                    success: function(data) {
                                        if (data.status == true) {
                                            Swal.fire(
                                                'Berhasil!',
                                                '' + data.message + '',
                                                'success'
                                            ).then(function() {
                                                $('#dTabel')
                                                    .DataTable()
                                                    .ajax.reload(
                                                        null, false
                                                    );
                                            });
                                        } else {
                                            Swal.fire(
                                                'Error!',
                                                '' + data.message + '',
                                                'error'
                                            );
                                        }

                                    },
                                    error: function() {
                                        Swal.fire(
                                            'Error',
                                            'Koneksi Error',
                                            'error'
                                        );
                                    }

                                });

                            }
                        })
                    });
                    //batas hapus
                }
            });
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
        });
        //klik notif
        $('.kirimnotifjaga').click(function(e){
                e.preventDefault();
                Swal.fire({
                    title: 'Kirim Notif WA?',
                    text: "akan mengirim notifikasi ke WhatsApp Petugas Jaga PST Hari ini",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Kirim'
                }).then((result) => {
                    if (result.value) {
                        //response ajax disini
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $(
                                    'meta[name="csrf-token"]').attr(
                                    'content')
                            }
                        });
                        $.ajax({
                            url: '{{ route("petugas.notifikasi") }}',
                            method: 'post',
                            data: {
                            },
                            cache: false,
                            dataType: 'json',
                            beforeSend: function() {
                                Swal.fire({
                                    title: "Processing...",
                                    html: "Silakan tunggu sampai proses selesai.",
                                    allowEscapeKey: false,
                                    allowOutsideClick: false,
                                    onOpen: () => {
                                    swal.showLoading();
                                    }
                                });
                            },
                            success: function(data) {
                                if (data.status == true) {
                                    Swal.hideLoading();
                                    Swal.fire(
                                        'Berhasil!',
                                        '' + data.message + '',
                                        'success'
                                    );
                                } else {
                                    Swal.hideLoading();
                                    Swal.fire(
                                        'Error!',
                                        '' + data.message + '',
                                        'error'
                                    );
                                }

                            },
                            error: function() {
                                Swal.hideLoading();
                                Swal.fire(
                                    'Error',
                                    'Koneksi Error',
                                    'error'
                                );
                            }

                        });

                    }
                })
            });
            $('.sinkronpetugas').click(function(e){
                e.preventDefault();
                Swal.fire({
                    title: 'Sinkron Data Petugas?',
                    text: "mensinkronkan data petugas dengan kunjungan",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Kirim'
                }).then((result) => {
                    if (result.value) {
                        //response ajax disini
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $(
                                    'meta[name="csrf-token"]').attr(
                                    'content')
                            }
                        });
                        $.ajax({
                            url: '{{ route("petugas.sinkron") }}',
                            method: 'post',
                            data: {
                            },
                            cache: false,
                            dataType: 'json',
                            beforeSend: function() {
                                Swal.fire({
                                    title: "Processing...",
                                    html: "Silakan tunggu sampai proses selesai.",
                                    allowEscapeKey: false,
                                    allowOutsideClick: false,
                                    onOpen: () => {
                                    swal.showLoading();
                                    }
                                });
                            },
                            success: function(data) {
                                if (data.status == true) {
                                    Swal.hideLoading();
                                    Swal.fire(
                                        'Berhasil!',
                                        '' + data.message + '',
                                        'success'
                                    );
                                } else {
                                    Swal.hideLoading();
                                    Swal.fire(
                                        'Error!',
                                        '' + data.message + '',
                                        'error'
                                    );
                                }

                            },
                            error: function() {
                                Swal.hideLoading();
                                Swal.fire(
                                    'Error',
                                    'Koneksi Error',
                                    'error'
                                );
                            }

                        });

                    }
                })
            });
    </script>
    <script>
        function GetUmur(birthDateString) {
            var today = new Date();
            var age = today.getFullYear() - birthDateString;
            return age;
        }
        function GetJamMenit(JamString) {
            var tgl = new Date(JamString);
            var hours = tgl.getHours();
            var minutes = tgl.getMinutes();
            if (hours < 10) {hours   = "0"+hours;}
            if (minutes < 10) {minutes = "0"+minutes;}
            var jam = hours+':'+minutes;
            return jam;
        }
        // --- FUNGSI UTAMA UNTUK INTERAKSI BINTANG ---
        function refreshStars(starGroup, ratingValue) {
            starGroup.children('i.fa-star').each(function(index) {
                if (index < ratingValue) {
                        $(this).addClass('selected fas').removeClass('far');
                } else {
                        $(this).removeClass('selected fas').addClass('far');
                }
            });
        }

        $('.rating-stars i').on('mouseover', function() {
            var hoverValue = parseInt($(this).data('value'));
            var starGroup = $(this).parent();
            refreshStars(starGroup, hoverValue);
        });

        $('.rating-stars').on('mouseleave', function() {
            var hiddenInput = $(this).next('input[type="hidden"]');
            var savedRating = parseInt(hiddenInput.val());
            refreshStars($(this), savedRating);
        });

        $('.rating-stars i').on('click', function() {
            var clickedValue = parseInt($(this).data('value'));
            var starGroup = $(this).parent();
            var hiddenInput = starGroup.next('input[type="hidden"]');
            // TEMUKAN DISPLAY YANG SESUAI
            var displayElement = starGroup.siblings('.rating-display').find('strong');

            // SIMPAN NILAI
            hiddenInput.val(clickedValue);

            // UPDATE TAMPILAN ANGKA
            displayElement.text(clickedValue);

            // UPDATE TAMPILAN BINTANG
            refreshStars(starGroup, clickedValue);
        });
    </script>
    @include('kunjungan.js-kunjungan')
    @include('kunjungan.js-feedback')
    @include('kunjungan.js-importdatawa')
    @include('kunjungan.js-petugas')
    @include('kunjungan.js-tindaklanjut')
    @include('kunjungan.js-jenis')
    @include('kunjungan.js-tujuan')
    @include('kunjungan.js-flagantrian')
@stop
