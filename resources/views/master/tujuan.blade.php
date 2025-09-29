@extends('layouts.utama')
@section('konten')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Tujuan</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Depan</a></li>
                    <li class="breadcrumb-item active">Master Tujuan</li>
                </ol>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
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
                    <h4 class="card-title">Master Tujuan</h4>
                    <!--form upload jadwal petugas-->

                    <!--batas-->
                    <center id="preloading">
                        <button class="btn btn-success" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading, Memproses data tanggal...
                        </button>
                    </center>
                    <center id="pesanerror">
                        <div class="alert alert-success m-5"><span id="tekserror"></span></div>
                    </center>
                    <div class="row tabeltujuan">
                        <div class="col-lg-8 col-xs-12">
                            <div class="m-t-40">
                                <table id="dTabel" class="display table table-hover table-striped table-bordered"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>kode</th>
                                            <th>inisial</th>
                                            <th>nama</th>
                                            <th>kunjungan</th>
                                            <th>aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <h4 class="card-title">Input Tujuan Baru</h4>
                            <h6 class="card-subtitle">akses menu tambah tujuan baru</h6>
                            <form class="mt-4" name="formTujuan">
                                <div class="form-group">
                                    <label for="tujuan_kode">Kode</label>
                                    <input type="number" class="form-control" name="tujuan_kode" id="tujuan_kode" aria-describedby="Kode" placeholder="Masukkan Kode Tujuan">
                                    <small id="tujuan_kode_teks" class="form-text text-muted">Kode berupa angka</small>
                                </div>
                                <div class="form-group">
                                    <label for="tujuan_inisial">Inisial</label>
                                    <input type="text" class="form-control" name="tujuan_inisial" id="tujuan_inisial" aria-describedby="tujuan_inisial" placeholder="Masukan Inisial max 3 char">
                                    <small id="tujuan_inisial_teks" class="form-text text-muted">inisial max 3 char</small>
                                </div>
                                <div class="form-group">
                                    <label for="tujuan_nama">Nama</label>
                                    <input type="text" class="form-control" name="tujuan_nama" id="tujuan_nama" aria-describedby="tujuan_nama" placeholder="Masukan Nama Tujuan Lengkap">
                                    <small id="tujuan_nama_teks" class="form-text text-muted">nama tujuan lengkap</small>
                                </div>
                                <button type="submit" id="simpanTujuan" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('master.modal-tujuan')
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
    <style type="text/css">
        #preloading,
        #pesanerror {
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
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <!-- end - This is for export functionality only -->
    <script>
        $(document).ready(function() {
            $('#dTabel').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('master.listtujuan') }}",
                    type: 'GET',
                    cache: false,
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'tujuan_kode'
                    },
                    {
                        data: 'tujuan_inisial'
                    },
                    {
                        data: 'tujuan_nama'
                    },
                    {
                        data: 'kunjungan',
                        orderable: false
                    },
                    {
                        data: 'aksi',
                        orderable: false
                    },
                ],
                dom: 'Bfrtip',
                iDisplayLength: 30,
                buttons: [
                    'copy', 'excel', 'print'
                ],
                responsive: true,
                "fnDrawCallback": function() {
                    //hapus tujuan
                $(".hapustujuan").click(function (e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var kode = $(this).data('kode');
                    var inisial = $(this).data('inisial');
                    var nama = $(this).data('nama');
                    var kunjungan = $(this).data('kunjungan');
                    Swal.fire({
                                title: 'Akan dihapus?',
                                text: "Data tujuan ("+kode+"-"+inisial+"-"+nama+") akan dihapus permanen dan data "+kunjungan+" kunjungan akan ikut terhapus",
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
                                        url : '{{route('master.hapustujuan')}}',
                                        method : 'post',
                                        data: {
                                            id: id,
                                            tujuan_kode: kode,
                                            tujuan_inisial: inisial,
                                            tujuan_nama: nama
                                        },
                                        cache: false,
                                        dataType: 'json',
                                        success: function(d){
                                            if (d.status == true)
                                            {
                                                Swal.fire(
                                                    'Berhasil!',
                                                    ''+d.message+'',
                                                    'success'
                                                ).then(function() {
                                                    $('#dTabel').DataTable().ajax.reload(null,false);
                                                });
                                            }
                                            else
                                            {
                                                Swal.fire(
                                                    'Error!',
                                                    ''+d.message+'',
                                                    'danger'
                                                );
                                            }

                                        },
                                        error: function(){
                                            Swal.fire(
                                                'Error',
                                                'Koneksi Error',
                                                'danger'
                                            );
                                        }

                                    });

                                }
                            })
                    });
                    //batas hapus

                }
            });
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass(
                'btn btn-primary mr-1');
        });
    </script>
    <!-- Sweet-Alert  -->
    <script src="{{ asset('assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    @include('master.js-tujuan')
    @include('master.js-edittujuan')
@stop
