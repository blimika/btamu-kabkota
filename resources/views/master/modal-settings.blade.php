<div class="modal fade" id="EditSettingModal" tabindex="-1" role="dialog" aria-labelledby="vcenter">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-white">Edit Setting</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal m-t-4" name="formEditSetting" id="formEditSetting" action="#"
                    method="POST">
                    <dl class="row">
                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8"><span id="view_id"></span></dd>
                        <dt class="col-sm-4">Key</dt>
                        <dd class="col-sm-8"><span id="view_key"></span></dd>
                        <dt class="col-sm-4">Label</dt>
                        <dd class="col-sm-8"><span id="view_label"></span></dd>
                    </dl>
                    <hr />
                    <div class="form-group row">
                        <label class="control-label col-md-2">Value</label>
                        <div class="input-group col-md-10">
                            <input type="text" class="form-control" id="edit_value" name="value" />
                        </div>
                    </div>
                    <div class="form-group">
                        <span id="setting_error" class="text-danger"></span>
                    </div>
                    <input type="hidden" id="edit_id" name="edit_id" value="" />
                    <input type="hidden" id="edit_key" name="edit_key" value="" />
                    <input type="hidden" id="edit_label" name="edit_label" value="" />
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success waves-effect" id="updatesetting" data-dismiss="modal">UPDATE</button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
            </form>
        </div>
    </div>
</div>
