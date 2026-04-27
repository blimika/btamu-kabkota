<div class="row">
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-center">Kunjungan dan Permintaan Status</h5>
                <div id="kunjungan_status" style="height: 300px;">
                    <ul class="feeds">
                        <li>
                            <div class="bg-info">
                                <i class="far fa-bell"></i>
                            </div> Ruang Tunggu
                            {!!Generate::StatusKunjungan('ruang_tunggu')!!}
                        </li>
                        <li>
                            <div class="bg-warning">
                                <i class="ti-server"></i>
                            </div> Dalam Layanan
                            {!!Generate::StatusKunjungan('dalam_layanan')!!}
                        </li>
                        <li>
                            <div class="bg-success">
                                <i class="ti-briefcase"></i>
                            </div> Selesai
                            {!!Generate::StatusKunjungan('selesai')!!}
                        </li>
                        <li>
                            <div class="bg-danger">
                                <i class="ti-user"></i>
                            </div> Total
                            {!!Generate::StatusKunjungan('total')!!}
                        </li>
                    </ul>
                    <div class="text-right">
                            <a href="{!!route('kunjungan.index')!!}" class="btn btn-xs btn-primary">Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-center">Kunjungan dan Feedback Status</h5>
                <div id="" style="height: 300px;">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 m-t-10 border-right border-bottom">
                            <h4 class="card-title text-center">Rating Petugas</h4>
                            <center class="m-t-10 m-b-10">
                                <font class="display-4">{{number_format($feedback_petugas,2, '.', '')}}</font>
                            </center>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 m-t-10 border-bottom">
                            <h4 class="card-title text-center">Rating Sarpras</h4>
                            <center class="m-t-10 m-b-10">
                                <font class="display-4">{{number_format($feedback_sarpras,2, '.', '')}}</font>
                            </center>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12 text-center border-bottom">
                            <h5 class="m-t-10 m-b-10"><i class="fas fa-user"></i> {{number_format($feedback_sudah,0, ',', '.')}} dari {{number_format($feedback_sudah+$feedback_belum,0, ',', '.')}} total kunjungan</h5>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 border-right">
                            <center class="m-b-10">
                                <font class="display-5">{{number_format($feedback_sudah,0, ',', '.')}}</font>
                                <br />
                                <span class="label label-success">sudah</span>
                            </center>
                            <i>ada {{number_format($feedback_ada_komentar,0, ',', '.')}} ({{number_format(($feedback_ada_komentar/$feedback_sudah)*100,2, ',', '.')}}%) kunjungan berkomentar</i>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <center class="m-b-10">
                                <font class="display-5">{{number_format($feedback_belum,0, ',', '.')}}</font>
                                <br />
                                <span class="label label-danger">belum</span>
                            </center>
                            <div class="text-right">
                                    <a href="{!!route('pengunjung.feedback')!!}" class="btn btn-xs btn-primary">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-center">Statistik Kunjungan (coming soon)</h5>
                <div id="" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
