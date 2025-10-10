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
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Pengunjung</td>
                                    <td>{{$pengunjung}}</td>
                                    <td>
                                        <a href="{{ route('petugas.format') }}" class="btn btn-info"><i class="ti-export"></i> &nbsp;Format</a>
                                        <a href="javascript:void(0)" class="btn btn-success m-l-15" data-toggle="modal" data-target="#ImportPetugasModal"><i class="ti-import"></i> Import</a>
                                        <a href="{{ route('petugas.format') }}" class="btn btn-danger m-l-15"><i class="ti-export"></i> &nbsp;Export</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kunjungan</td>
                                    <td>{{$kunjungan}}</td>
                                    <td>
                                        <a href="{{ route('petugas.format') }}" class="btn btn-info"><i class="ti-export"></i> &nbsp;Format</a>
                                        <a href="javascript:void(0)" class="btn btn-success m-l-15" data-toggle="modal" data-target="#ImportPetugasModal"><i class="ti-import"></i> Import</a>
                                        <a href="{{ route('petugas.format') }}" class="btn btn-danger m-l-15"><i class="ti-export"></i> &nbsp;Export</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Petugas</td>
                                    <td>{{$petugas}}</td>
                                    <td>
                                        <a href="{{ route('petugas.format') }}" class="btn btn-info"><i class="ti-export"></i> &nbsp;Format</a>
                                        <a href="javascript:void(0)" class="btn btn-success m-l-15" data-toggle="modal" data-target="#ImportPetugasModal"><i class="ti-import"></i> Import</a>
                                        <a href="{{ route('petugas.format') }}" class="btn btn-danger m-l-15"><i class="ti-export"></i> &nbsp;Export</a>
                                    </td>
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
    @include('data.modal-import')
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
@stop
