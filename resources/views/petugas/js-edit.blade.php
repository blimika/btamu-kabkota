<script>
    $('#EditPetugasModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var uid = button.data('uid')

    $.ajax({
        url : '{{route("webapi")}}/',
        method : 'get',
        data: {
            model: 'petugas',
            uid: uid
        },
        cache: false,
        dataType: 'json',
        success: function(d){
            if (d.status == true)
            {
                $('#EditPetugasModal .modal-body #edit_user_uid').val(d.data.user_uid);
                $('#EditPetugasModal .modal-body #edit_level').val(d.data.user_level);
                $('#EditPetugasModal .modal-body #edit_name').val(d.data.name)
                $('#EditPetugasModal .modal-body #edit_username').val(d.data.username)
                $('#EditPetugasModal .modal-body #edit_email').val(d.data.email)
                $('#EditPetugasModal .modal-body #edit_telepon').val(d.data.user_telepon)
            }
            else
            {
                alert(d.message);
            }
        },
        error: function(){
            alert("error load transaksi");
        }
        });
    });
    //simpan edit member
    $('#EditPetugasModal .modal-footer #UpdateMemberData').on('click', function(e) {
        e.preventDefault();
        var uid = $('#EditPetugasModal .modal-body #edit_user_uid').val();
        var level = $('#EditPetugasModal .modal-body #edit_level').val();
        var name = $('#EditPetugasModal .modal-body #edit_name').val()
        var username = $('#EditPetugasModal .modal-body #edit_username').val()
        var email = $('#EditPetugasModal .modal-body #edit_email').val()
        var telepon = $('#EditPetugasModal .modal-body #edit_telepon').val()
        if (level == "")
        {
            $('#EditPetugasModal .modal-body #edit_member_error').text('Pilih salah satu level member');
            return false;
        }
        else if (name == "")
        {
            $('#EditPetugasModal .modal-body #edit_member_error').text('Nama lengkap tidak boleh kosong');
            return false;
        }
        else if (username == "")
        {
            $('#EditPetugasModal .modal-body #edit_member_error').text('Username tidak boleh kosong');
            return false;
        }
        else if (email == "")
        {
            $('#EditPetugasModal .modal-body #edit_member_error').text('E-mail tidak boleh kosong');
            $('#EditPetugasModal .modal-body #edit_email').focus();
            return false;
        }
        else if (telepon == "")
        {
            $('#EditPetugasModal .modal-body #edit_member_error').text('Telepon tidak boleh kosong');
            return false;
        }
        else
        {
            if (email != "")
            {
                var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                if(!email.match(mailformat))
                {
                    $('#EditPetugasModal .modal-body #edit_member_error').text('Format e-mail tidak sesuai');
                    $('#EditPetugasModal .modal-body #edit_email').focus();
                    return false;
                }
            }
            //ajax edit petugas
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url : '{{route('petugas.updatedata')}}',
                method : 'post',
                data: {
                    uid: uid,
                    level: level,
                    name: name,
                    username: username,
                    email: email,
                    telepon: telepon,
                },
                cache: false,
                dataType: 'json',
                success: function(data){
                    if (data.status == true)
                    {
                        Swal.fire(
                            'Berhasil!',
                            ''+data.message+'',
                            'success'
                        ).then(function() {
                            $('#dTabel').DataTable().ajax.reload(null,false);
                        });
                    }
                    else
                    {
                        Swal.fire(
                            'Error!',
                            ''+data.message+'',
                            'error'
                        );
                    }

                },
                error: function(){
                    Swal.fire(
                        'Error',
                        'Koneksi Error',
                        'error'
                    );
                }

            });
            //batas ajax
    }
});
    //batas
</script>
