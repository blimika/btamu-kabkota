<div class="modal fade" id="EditPetugasModal" tabindex="-1" role="dialog" aria-labelledby="vcenter">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title text-white" id="EditPetugasModal">Edit Petugas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal m-t-4" name="formEditPetugas" id="formEditPetugas" action=""  method="POST">
                    <div class="form-group">
                    <label class="control-label">Level</label>
                        <select class="form-control" id="edit_level" name="level" required>
                            <option value=""></option>
                            <option value="operator">Operator</option>
                            <option value="admin">Admin</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control" id="edit_name" aria-describedby="name" autocomplete="off" placeholder="name" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="edit_username" aria-describedby="username" autocomplete="off" placeholder="username" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">E-Mail</label>
                            <input type="text" class="form-control" id="edit_email" name="email" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Telepon/WA</label>
                            <input type="text" class="form-control" id="edit_telepon" name="telepon" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <span id="edit_member_error" class="text-danger"></span>
                    </div>
                    <input type="hidden" id="edit_user_uid" name="user_uid" value="" />
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success waves-effect" id="UpdateMemberData" name="UpdateMemberData" data-dismiss="modal">UPDATE</button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </form>
        </div>
    </div>
</div>
