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
                <li class="breadcrumb-item active">Bukutamu</li>
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
    <div class="col-12">
        <h5 class="card-subtitle float-right m-b-10">{{Tanggal::HariPanjang(\Carbon\Carbon::now())}}</h5>
    </div>
</div>
@include('dashboard.baris1')
<!--grafik menurut jenis kelamin--->
@include('dashboard.baris2')
<!--baris3--->
@include('dashboard.baris3')
@if (Auth::user())
   <!--baris4--->
    @include('dashboard.baris4')
@endif
<!---baris5--->
@include('dashboard.baris5')
@if (Auth::user() || Generate::CekAkses(\Request::getClientIp(true)))
    <!--tabel jumlah 10 kunjungan terakhir--->
    @include('dashboard.10kunjungan')
    @include('feedback.modal-feedback')
@endif
@endsection

@section('css')
    <!--This page css - Morris CSS -->
    <link href="{{asset('assets/node_modules/morrisjs/morris.css')}}" rel="stylesheet">
    <link href="{{asset('assets/node_modules/Magnific-Popup-master/dist/magnific-popup.css')}}" rel="stylesheet">
    <!-- page css -->
    <link href="{{asset('dist/css/pages/user-card.css')}}" rel="stylesheet">
    <!-- page css -->
    <link href="{{asset('dist/css/pages/tab-page.css')}}" rel="stylesheet">
    <!--highcharts-->
    <link href="{{asset('dist/grafik/highcharts.css')}}" rel="stylesheet">
    <style>
       .product-review {
  margin: 0px;
  padding: 25px; }
  .product-review li {
    display: block;
    padding: 20px 0;
    list-style: none; }
    .product-review li .font,
    .product-review li span {
      display: inline-block;
      margin-left: 10px; }

.social-profile {
  text-align: center;
  background: rgba(7, 10, 43, 0.8); }

.profile-tab li a.nav-link,
.customtab li a.nav-link {
  border: 0px;
  padding: 15px 20px;
  color: #212529; }
  .profile-tab li a.nav-link.active,
  .customtab li a.nav-link.active {
    border-bottom: 2px solid #fb9678;
    color: #fb9678; }
  .profile-tab li a.nav-link:hover,
  .customtab li a.nav-link:hover {
    color: #fb9678; }

/*******************/
/*widgets -app*/
/*******************/
.css-bar:after {
  z-index: 1; }

.css-bar > i {
  z-index: 10; }

ul.list-icons {
  margin: 0px;
  padding: 0px; }
  ul.list-icons li {
    list-style: none;
    line-height: 30px;
    margin: 5px 0;
    transition: 0.2s ease-in; }
    ul.list-icons li a {
      color: #212529; }
      ul.list-icons li a:hover {
        color: #fb9678; }
    ul.list-icons li i {
      padding-right: 8px; }
        /*******************
Progress bar
******************/
.progress.active .progress-bar,
.progress-bar.active {
  -webkit-animation: progress-bar-stripes 2s linear infinite;
  -o-animation: progress-bar-stripes 2s linear infinite;
  animation: progress-bar-stripes 2s linear infinite; }

.progress-vertical {
  min-height: 250px;
  height: 250px;
  position: relative;
  display: inline-block;
  margin-bottom: 0;
  margin-right: 20px; }

.progress-vertical-bottom {
  min-height: 250px;
  height: 250px;
  position: relative;
  display: inline-block;
  margin-bottom: 0;
  margin-right: 20px;
  transform: rotate(180deg); }

.progress-animated {
  -webkit-animation-duration: 5s;
  -webkit-animation-name: myanimation;
  -webkit-transition: 5s all;
  animation-duration: 5s;
  animation-name: myanimation;
  transition: 5s all; }

@-webkit-keyframes myanimation {
  from {
    width: 0; } }

@keyframes myanimation {
  from {
    width: 0; } }
    </style>
@stop
@section('js')
    <script src="{{asset('dist/js/pages/jasny-bootstrap.js')}}"></script>
    <!-- Magnific popup JavaScript -->
    <script src="{{asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup-init.js')}}"></script>
    <!--highchart-->
    <script src="{{asset('dist/grafik/highcharts.js')}}"></script>
    <script src="{{asset('dist/grafik/exporting.js')}}"></script>
    <script src="{{asset('dist/grafik/offline-exporting.js')}}"></script>
    <script src="{{asset('dist/grafik/export-data.js')}}"></script>
    <script src="{{asset('dist/grafik/series-label.js')}}"></script>
    <script src="{{asset('dist/grafik/accessibility.js')}}"></script>
    <!---moment.js url--->
    <script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <!--Morris JavaScript -->
    <script src="{{asset('assets/node_modules/raphael/raphael-min.js')}}"></script>
    <script src="{{asset('assets/node_modules/morrisjs/morris.js')}}"></script>
    @if(!$DataKunjungan->isEmpty())
        @include('dashboard.grafik')
        @if (Auth::user() || Generate::CekAkses(\Request::getClientIp(true)))
            @include('feedback.js-page-feedback')
        @endif
    @endif
@stop
