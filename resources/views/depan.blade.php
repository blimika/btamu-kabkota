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
            <a href="" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Kunjungan Baru</a>
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
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                This is some text within a card block.
            </div>
        </div>
    </div>
</div>
                
@endsection

@section('css')
   
@stop
@section('js')
    

@stop
