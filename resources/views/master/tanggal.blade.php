@extends('layouts.utama')
@section('konten')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Tanggal</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Depan</a></li>
                    <li class="breadcrumb-item active">Master Tanggal</li>
                </ol>
                @if (Auth::User()->user_level == 'admin')
                    <a href="javascript:void(0)" class="btn btn-success m-l-15" data-toggle="modal"
                        data-target="#GenerateTanggal"><i class="fa fa-plus-circle"></i> Generate Tanggal</a>
                @endif
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
                    <h4 class="card-title">Master Tanggal Bukutamu</h4>
                    <!--form upload jadwal petugas-->
                    <div class="row">
                        <div class="col-lg-8">
                            <!--form--filter-->
                        <form class="form-horizontal">
                            <div class="form-group row">
                                <label for="tahun" class="col-sm-1 control-label">Filter : </label>
                                <div class="col-md-4">
                                    <select name="tahun" id="tahun" class="form-control">
                                        <option value="0">Semua Tahun</option>
                                     @foreach ($data_tahun as $iTahun)
                                     <option value="{{$iTahun->tahun}}" @if (request('tahun')==$iTahun->tahun or $tahun==$iTahun->tahun)
                                     selected
                                    @endif>{{$iTahun->tahun}}</option>
                                     @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success">Filter</button>
                                </div>
                            </div>
                        </form>
                        <!--batas form filter-->
                        </div>
                        <div class="col-lg-4 text-right">
                            @if (Auth::User()->user_level == 'admin')
                                <a href="{{ route('master.formatjadwal') }}" class="btn btn-info">
                                    <i class="ti-export"></i> &nbsp;Format Import</a>
                                <a href="javascript:void(0)" class="btn btn-success m-l-15" data-toggle="modal"
                                    data-target="#ImportJadwalModal"><i class="ti-import"></i> Import Jadwal</a>
                            @endif
                        </div>
                    </div>

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
                    <div class="row tabeltanggal">
                        <div class="col-lg-12 col-xs-12">
                            <div class="table-responsive m-t-40">
                                <table id="dTabel" class="display table table-hover table-striped table-bordered"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>tanggal</th>
                                            <th>hari</th>
                                            <th>Jenis</th>
                                            <th>deskripsi</th>
                                            <th>petugas1</th>
                                            <th>petugas2</th>
                                            <th>aksi</th>
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
        </div>
    </div>
    @include('master.modal-tanggal')
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
            var tahun = $('#tahun').val();
            $('#dTabel').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('master.listtanggal') }}",
                    type: 'GET',
                    data: {
                        tahun: tahun
                    },
                    cache: false,
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'tanggal_angka'
                    },
                    {
                        data: 'tanggal_hari'
                    },
                    {
                        data: 'tanggal_jenis'
                    },
                    {
                        data: 'tanggal_deskripsi'
                    },
                    {
                        data: 'tanggal_petugas1'
                    },
                    {
                        data: 'tanggal_petugas2'
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
                    //hapus tamu

                    //batas hapus

                }
            });
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass(
                'btn btn-primary mr-1');
        });
    </script>
    <!-- Sweet-Alert  -->
    <script src="{{ asset('assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    @include('master.js-tanggal')
@stop
