@extends('layouts.utama')
@section('konten')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Feedback</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Depan</a></li>
                    <li class="breadcrumb-item active">Feedback</li>
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
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <center id="pesanerror">
                        <div class="alert alert-success m-5"><span id="tekserror"></span></div>
                    </center>
                    <div class="col-lg-8 m-t-40">
                        @if ($data)
                            <p>
                                Hi, <b>{{$data->Pengunjung->pengunjung_nama}}</b>
                            </p>
                            <p>
                               Kami mengucapkan terima kasih atas kunjungan Anda ke {{ENV('NAMA_SATKER')}}. <br />
                                Kami berharap Anda memiliki pengalaman yang menyenangkan bersama kami.
                            </p>
                            <p>
                                # Detil Kunjungan <br>
                                <dl class="row">
                                    <dt class="col-lg-3 col-md-3 col-xs-12">UID</dt>
                                    <dd class="col-lg-9 col-sm-9">#{{$data->kunjungan_uid}}</dd>
                                    <dt class="col-lg-3 col-md-3 col-xs-12">Nama</dt>
                                    <dd class="col-lg-9 col-sm-9">{{ $data->Pengunjung->pengunjung_nama }}</dd>
                                    <dt class="col-lg-3 col-md-3 col-xs-12">Email</dt>
                                    <dd class="col-lg-9 col-sm-9">{{ $data->Pengunjung->pengunjung_email }}</dd>
                                    <dt class="col-lg-3 col-md-3 col-xs-12">Nomor HP</dt>
                                    <dd class="col-lg-9 col-sm-9">{{ $data->Pengunjung->pengunjung_nomor_hp }}</dd>
                                    <dt class="col-lg-3 col-md-3 col-xs-12">Tanggal Kunjungan</dt>
                                    <dd class="col-lg-9 col-sm-9">{{ \Carbon\Carbon::parse($data->created_at)->isoFormat('dddd, D MMMM Y H:mm:ss') }}</dd>
                                    @if ($data->kunjungan_flag_antrian == 'selesai')
                                        <dt class="col-lg-3 col-md-3 col-xs-12">Petugas yang melayani</dt>
                                        <dd class="col-lg-9 col-sm-9">{{ $data->Petugas->name}}</dd>
                                    @endif
                                </dl>
                            </p>
                            <!--- sudah selesai dilayani--->
                            @if ($data->kunjungan_flag_antrian == 'selesai' )
                            <p>
                                Untuk meningkatkan layanan, kami sangat menghargai jika Anda dapat meluangkan beberapa menit untuk mengisi feedback singkat berikut ini. Tanggapan Anda sangat berharga bagi kami untuk terus memberikan pelayanan terbaik.
                            </p>
                            <p>
                                @if ($data->kunjungan_flag_feedback == 'sudah')
                                    <button type="button" class="btn btn-rounded btn-info m-t-5" data-uid="{{$data->kunjungan_uid}}" data-nama="{{$data->Pengunjung->pengunjung_nama}}" data-tanggal="{{$data->kunjungan_tanggal}}" data-toggle="modal" data-target="#ViewFeedbackModal"><span data-toggle="tooltip" data-placement="top" title="Sudah memberikan feedback"><i class="fas fa-check-circle"></i> feedback</span></button>
                                @else
                                    <button type="button" class="btn btn-rounded btn-danger tombolfeedback m-t-5" data-uid="{{$data->kunjungan_uid}}" data-nama="{{$data->Pengunjung->pengunjung_nama}}" data-tanggal="{{$data->kunjungan_tanggal}}" data-toggle="modal" data-target="#BeriFeebackModal"><span data-toggle="tooltip" data-placement="top" title="Belum memberikan feedback"><i class="fas fa-question"></i> feedback</span></button>
                                @endif
                            </p>
                            @endif
                            <!---batasnya sudah dilayani--->
                            <p>
                                Sekali lagi, terima kasih atas kunjungan Anda dan kami berharap dapat menyambut Anda kembali di masa depan.
                            </p>
                            <p>
                                Salam hangat,<br /> <br />
                                {{ENV('NAMA_APLIKASI')}} <br />
                                {{ENV('NAMA_SATKER')}}<br />
                                {{ENV('ALAMAT_SATKER')}}

                            </p>
                        @else
                            Data kunjungan tidak valid
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('feedback.modal-feedback')
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
    <script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <!-- Sweet-Alert  -->
    <script src="{{ asset('assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <!-- Magnific popup JavaScript -->
    <script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}"></script>

    @include('feedback.js-page-feedback')
@stop
