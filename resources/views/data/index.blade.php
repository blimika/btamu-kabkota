@extends('layouts.utama')
@section('konten')
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">Data Manajemen</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Depan</a></li>
                <li class="breadcrumb-item active">Data Manajemen</li>
            </ol>
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
                    <h4 class="card-title">Database </h4>
                    Jumlah record masing-masing tabel pada aplikasi
                    <div class="row">
                    <div class="col-lg-10 col-xs-12">
                    <div class="table-responsive m-t-20">
                        <table id="dTabel" class="table table-striped" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Nama Tabel</th>
                                    <th>Jumlah Record</th>
                                    <th>Aksi</th>
                                    @if ($petugas_old > 0)
                                        <th colspan="2">Khusus Provinsi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Pengunjung</td>
                                    <td>{{$pengunjung}}</td>
                                    <td>
                                        <a href="{{ route('pengunjung.format') }}" class="btn btn-info"><i class="ti-export"></i> &nbsp;Format</a>
                                        <a href="javascript:void(0)" class="btn btn-success m-l-15" data-toggle="modal" data-target="#ImportPengunjungModal"><i class="ti-import"></i> Import</a>
                                        <a href="{{ route('petugas.format') }}" class="btn btn-danger m-l-15"><i class="ti-export"></i> &nbsp;Export</a>
                                    </td>
                                    @if ($pengunjung_old > 0)
                                        <td>{{$pengunjung_old}}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="sinkronpengunjung btn btn-info" ><i class="fas fa-sync"></i> Sinkron Pengunjung</a>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Kunjungan</td>
                                    <td>{{$kunjungan}}</td>
                                    <td>
                                        <a href="{{ route('kunjungan.format') }}" class="btn btn-info"><i class="ti-export"></i> &nbsp;Format</a>
                                        <a href="javascript:void(0)" class="btn btn-success m-l-15" data-toggle="modal" data-target="#ImportKunjunganModal"><i class="ti-import"></i> Import</a>
                                        <a href="{{ route('petugas.format') }}" class="btn btn-danger m-l-15"><i class="ti-export"></i> &nbsp;Export</a>
                                    </td>
                                    @if ($kunjungan_old > 0)
                                        <td>{{$kunjungan_old}}</td>
                                        <td>
                                            @if ($pengunjung > 0)
                                                <a href="javascript:void(0)" class="sinkronkunjungan btn btn-warning" ><i class="fas fa-sync"></i> Sinkron Kunjungan</a>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Petugas</td>
                                    <td>{{$petugas}}</td>
                                    <td>
                                        <a href="{{ route('petugas.format') }}" class="btn btn-info"><i class="ti-export"></i> &nbsp;Format</a>
                                        <a href="javascript:void(0)" class="btn btn-success m-l-15" data-toggle="modal" data-target="#ImportPetugasModal"><i class="ti-import"></i> Import</a>
                                        <a href="{{ route('petugas.format') }}" class="btn btn-danger m-l-15"><i class="ti-export"></i> &nbsp;Export</a>
                                    </td>
                                    @if ($petugas_old > 0)
                                        <td>{{$petugas_old}}</td>
                                        <td><a href="javascript:void(0)" class="sinkronpetugas btn btn-danger" ><i class="fas fa-sync"></i> Sinkron Petugas</a></td>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>

                    </div>
            </div>
        </div>
    </div>
</div>
    @include('data.modal-importpetugas')
    @include('data.modal-pengunjungimport')
    @include('data.modal-kunjunganimport')
@endsection

@section('css')
    <!-- Date picker plugins css -->
    <link href="{{asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--alerts CSS -->
    <link href="{{asset('assets/node_modules/sweetalert2/dist/sweetalert2.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/node_modules/Magnific-Popup-master/dist/magnific-popup.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css')}}">
@stop
@section('js')
    <script src="{{asset('dist/js/pages/jasny-bootstrap.js')}}"></script>
    <!-- Date Picker Plugin JavaScript -->
    <script src="{{asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
    <!-- This is data table -->
    <script src="{{asset('assets/node_modules/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js')}}"></script>
    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <!-- end - This is for export functionality only -->

    <!-- Sweet-Alert  -->
    <script src="{{asset('assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js')}}"></script>
     <!-- Magnific popup JavaScript -->
     <script src="{{asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup.min.js')}}"></script>
     <script src="{{asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup-init.js')}}"></script>
     @include('data.js-importpetugas')
     @include('data.js-importpengunjung')
     @include('data.js-importkunjungan')
     <script>
        $('.sinkronpetugas').click(function(e){
                e.preventDefault();
                Swal.fire({
                    title: 'Sinkron Data Petugas?',
                    text: "mensinkronkan data petugas ke sistem baru",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sinkron'
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
                            url: '{{ route("data.sinkronpetugas") }}',
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
                                    ).then(function() {
                                        location.reload();
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
            //pengunjung
            $('.sinkronpengunjung').click(function(e){
                e.preventDefault();
                Swal.fire({
                    title: 'Sinkron Data Pengunjung?',
                    text: "mensinkronkan data pengunjung ke sistem baru",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sinkron'
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
                            url: '{{ route("data.sinkronpengunjung") }}',
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
                                    ).then(function() {
                                        location.reload();
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
            //kunjungan
            $('.sinkronkunjungan').click(function(e){
                e.preventDefault();
                Swal.fire({
                    title: 'Sinkron Data Kunjungan?',
                    text: "mensinkronkan data kunjungan ke sistem baru",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sinkron'
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
                            url: '{{ route("data.sinkronkunjungan") }}',
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
                                    ).then(function() {
                                        location.reload();
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
     </script>
@stop
