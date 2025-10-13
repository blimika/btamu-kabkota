<div class="modal fade" id="ImportPengunjungModal" tabindex="-1" role="dialog" aria-labelledby="vcenter">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title text-white">Import Data Pengunjung</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal m-t-4" name="formInputPengunjung" id="formInputPengunjung" action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <input type="file" class="form-control" id="file_import" name="file_import"
                            required="">
                    </div>
                    <div class="form-group">
                        <span id="importpengunjung_error" class="text-danger"></span>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success waves-effect" id="BtnImportPengunjung"
                    data-dismiss="modal">Import</button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
            </form>
        </div>
    </div>
</div>
