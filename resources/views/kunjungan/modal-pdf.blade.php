<!--view feedback modal--->
<div class="modal fade" id="ViewPDFModal" tabindex="-1" role="dialog" aria-labelledby="vcenter">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title" id="title">View PDF Permintaan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">ID</div>
                    <div class="col-lg-4"><span id="kunjungan_id"></div>
                    <div class="col-lg-3">Jenis</div>
                    <div class="col-lg-3"><span id="kunjungan_jenis"></span></div>
                </div>
                <div class="row">
                    <div class="col-lg-2">UID</div>
                    <div class="col-lg-4"><span id="kunjungan_uid"></span></div>
                    <div class="col-lg-3">Tujuan</div>
                    <div class="col-lg-3"><span id="kunjungan_tujuan"></span></div>
                </div>
                <div class="row">
                    <div class="col-lg-2">Nama</div>
                    <div class="col-lg-4"><span id="pengunjung_nama"></span></div>
                    <div class="col-lg-3"></div>
                    <div class="col-lg-3"></div>
                </div>
                <div class="row">
                    <div class="col-lg-2">Tanggal</div>
                    <div class="col-lg-4"><span id="kunjungan_tanggal"></span></div>
                    <div class="col-lg-3">Petugas Layanan</div>
                    <div class="col-lg-3"><span id="kunjungan_petugas_nama"></span></div>
                </div>
                <hr style="width: 100%; color: black; height: 1px;" />
                <iframe id="pdf_permintaan" name="pdf_permintaan" src="" width="100%" height="600px" style="border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
