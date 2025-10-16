<div class="row">
    <div class="col-lg-4 col-md-12 border-right">
        <center class="m-t-30 m-b-40 p-t-20 p-b-20">
            <font class="display-4">{{number_format($data->average('kunjungan_nilai_feedback'),2, '.', '')}}</font>
            <div class="m-b-10">
                {{--Start Rating--}}
                @php
                    $rating = $data->average('kunjungan_nilai_feedback');
                    $totalStars = 6;
                @endphp
                @for ($i = 1; $i <= $totalStars; $i++)
                    @if ((int) $rating >= $i)
                        <span class="fa fa-star text-warning"></span>
                    @else
                        <span class="fa fa-star"></span>
                    @endif
                @endfor
                {{--End Rating--}}
            </div>
            <h6 class="text-muted"><i class="fas fa-user"></i> {{$data->count()}} total</h6>
        </center>
    <hr>
</div>
<div class="col-lg-8 col-md-12">
    <div class="row">
        <div class="col-lg-2 col-md-2">
            <span class="float-right">
                6 <span class="fa fa-star text-warning"></span>
            </span>
        </div>
        <div class="col-lg-10 col-md-10">
            <div class="progress">
                <div class="progress-bar bg-success wow animated progress-animated" style="width: {{number_format(($data->where('kunjungan_nilai_feedback','6')->count()/$data->count())*100,2,".",",")}}%; height:20px;" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
    </div>
    <div class="row m-t-10">
        <div class="col-lg-2 col-md-2">
            <span class="float-right">
                5 <span class="fa fa-star text-warning"></span>
            </span>
        </div>
        <div class="col-lg-10 col-md-10">
            <div class="progress">
                <div class="progress-bar bg-info wow animated progress-animated" style="width: {{number_format(($data->where('kunjungan_nilai_feedback','5')->count()/$data->count())*100,2,".",",")}}%; height:20px;" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
    </div>
    <div class="row m-t-10">
        <div class="col-lg-2 col-md-2">
            <span class="float-right">
                4 <span class="fa fa-star text-warning"></span>
            </span>
        </div>
        <div class="col-lg-10 col-md-10">
            <div class="progress">
                <div class="progress-bar bg-warning wow animated progress-animated" style="width: {{number_format(($data->where('kunjungan_nilai_feedback','4')->count()/$data->count())*100,2,".",",")}}%; height:20px;" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
    </div>
    <div class="row m-t-10">
        <div class="col-lg-2 col-md-2">
            <span class="float-right">
                3 <span class="fa fa-star text-warning"></span>
            </span>
        </div>
        <div class="col-lg-10 col-md-10">
            <div class="progress">
                <div class="progress-bar bg-primary wow animated progress-animated" style="width: {{number_format(($data->where('kunjungan_nilai_feedback','3')->count()/$data->count())*100,2,".",",")}}%; height:20px;" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
    </div>
    <div class="row m-t-10">
        <div class="col-lg-2 col-md-2">
            <span class="float-right">
                2 <span class="fa fa-star text-warning"></span>
            </span>
        </div>
        <div class="col-lg-10 col-md-10">
            <div class="progress">
                <div class="progress-bar bg-inverse wow animated progress-animated" style="width: {{number_format(($data->where('kunjungan_nilai_feedback','2')->count()/$data->count())*100,2,".",",")}}%; height:20px;" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
    </div>
    <div class="row m-t-10">
        <div class="col-lg-2 col-md-2">
            <span class="float-right">
                1 <span class="fa fa-star text-warning"></span>
            </span>
        </div>
        <div class="col-lg-10 col-md-10">
            <div class="progress">
                <div class="progress-bar bg-danger wow animated progress-animated" style="width: {{number_format(($data->where('kunjungan_nilai_feedback','1')->count()/$data->count())*100,2,".",",")}}%; height:20px;" role="progressbar" aria-valuenow="0%" aria-valuemin="0%" aria-valuemax="100%">
                </div>
            </div>
        </div>
    </div>
</div>
</div>
