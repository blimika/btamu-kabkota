@extends('layouts.utama')
@section('konten')
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">Profil</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Depan</a></li>
                <li class="breadcrumb-item active">Profil</li>
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
    <!-- Column -->
    <div class="col-lg-4 col-xlg-3 col-md-5">
        <div class="card">
            <div class="card-body">
                <center class="m-t-30">
                    @if (Auth::user()->user_foto != NULL)
                            @if (Storage::disk('public')->exists(Auth::user()->user_foto))
                            <img src="{{asset('storage'.Auth::user()->user_foto)}}" width="150" height="150" class="img-circle" />
                            @else
                                <img src="https://placehold.co/480x480/0022FF/FFFFFF/?text=photo+tidak+ada" class="img-circle" width="150" />
                            @endif
                        @else
                            <img src="https://placehold.co/480x480/0022FF/FFFFFF/?text=photo+tidak+ada" class="img-circle" width="150" />
                        @endif
                    <h4 class="card-title m-t-10">{{Auth::user()->name}}</h4>
                    <h6 class="card-subtitle">{{Auth::user()->username}}</h6>
                    <div class="row text-center justify-content-md-center">
                        {!! Generate::RatingPetugas(Auth::user()->user_uid) !!}
                    </div>
                </center>
            </div>
            <div>
                <hr> </div>
            <div class="card-body">
                <small class="text-muted p-t-30 db">#UID</small>
                <h6>{{Auth::user()->user_uid}}</h6>
                <small class="text-muted">Email address </small>
                <h6>{{Auth::user()->email}}</h6>
                <small class="text-muted p-t-30 db">Telepon</small>
                <h6>{{Auth::user()->user_telepon}}</h6>
                <small class="text-muted p-t-30 db">Level</small>
                <h6>{{Auth::user()->user_level}}</h6>
                <small class="text-muted p-t-30 db">Login Terakhir</small>
                <h6>{{Auth::user()->user_last_login}}</h6>
                <small class="text-muted p-t-30 db">IP Terakhir</small>
                <h6>{{Auth::user()->user_last_ip}}</h6>

            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-lg-8 col-xlg-9 col-md-7">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profil" role="tab">Profil</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#pelayanan" role="tab">Pelayanan</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#editprofil" role="tab">Edit Profil</a> </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="profil" role="tabpanel">
                    <div class="card-body">
                    </div>
                </div>
                <div class="tab-pane" id="pelayanan" role="tabpanel">
                    <div class="card-body">
                    </div>
                </div>
                <div class="tab-pane" id="editprofil" role="tabpanel">
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <!-- Date picker plugins css -->
    <link href="{{asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--alerts CSS -->
    <link href="{{asset('assets/node_modules/sweetalert2/dist/sweetalert2.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css')}}">
    <link href="{{asset('dist/css/pages/progressbar-page.css')}}" rel="stylesheet">
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
    <script>
        $(function () {
            $('#dTabel').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy','excel','print'
                ],
                responsive: true,
                "displayLength": 30,

            });
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-info mr-1');
        });
    </script>


@stop
