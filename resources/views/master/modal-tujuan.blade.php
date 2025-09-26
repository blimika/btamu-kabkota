<div class="modal fade" id="EditTujuan" tabindex="-1" role="dialog" aria-labelledby="vcenter">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title text-white">Edit Tujuan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal m-t-4" name="formEditTujuan" id="formEditTujuan" action="#"
                    method="POST">
                    <dl class="row">
                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8"><span id="edit_id"></span></dd>
                    </dl>
                    <hr />
                    <div class="form-group row">
                        <label class="control-label col-md-2">Kode</label>
                        <div class="input-group col-md-10">
                            <input type="text" class="form-control" id="edit_tujuan_kode" name="tujuan_kode" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Inisial</label>
                        <div class="input-group col-md-10">
                            <input type="text" class="form-control" id="edit_tujuan_inisial" name="tujuan_inisial" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Nama</label>
                        <div class="input-group col-md-10">
                            <input type="text" class="form-control" id="edit_tujuan_nama" name="tujuan_nama" />
                        </div>
                    </div>
                    <div class="form-group">
                        <span id="tanggal_error" class="text-danger"></span>
                    </div>
                    <input type="hidden" id="tujuan_id" name="tujuan_id" value="" />
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success waves-effect" id="updatetgl"
                    data-dismiss="modal">UPDATE</button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
            </form>
        </div>
    </div>
</div>
