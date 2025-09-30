<script>
$('#UpdateProfil').on('click', function(e) {
    e.preventDefault();
    var name = $('#name').val();
    var username = $('#username').val();
    var email = $('#email').val();
    var telepon = $('#telepon').val();
    if (name == "")
    {
        $('#formEditProfil #editprofil_error').text('Nama harus terisi');
        return false;
    }
    else if (username == "")
    {
        $('#formEditProfil #editprofil_error').text('Username harus terisi');
        return false;
    }
    else if (email == "")
    {
        $('#formEditProfil #editprofil_error').text('Email harus terisi');
        return false;
    }
    else if (telepon == "")
    {
        $('#formEditProfil #editprofil_error').text('Telepon/WA harus terisi');
        return false;
    }
    else
    {
        if (email != "")
        {
            var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if(!email.match(mailformat))
            {
                $('#formEditProfil #editprofil_error').text('Format email tidak sesuai');
                return false;
            }
            //ajax update
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url : '{{route('petugas.updateprofil')}}',
                method : 'post',
                data: {
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
                            //sudah sukses
                            location.reload();
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
            //batas update
        }
    }
});
//ganti passwd profil
$('#UpdatePasswd').on('click', function(e) {
    e.preventDefault();
    var passwd_lama = $('#edit_passwd_lama').val();
    var passwd_baru = $('#edit_passwd_baru').val();
    var ulangi_passwd_baru = $('#edit_ulangi_passwdbaru').val();
    if (passwd_lama == "")
    {
        $('#formGantiPasswd #gantipasswd_error').text('Password lama harus terisi');
        return false;
    }
    else if (passwd_baru == "")
    {
        $('#formGantiPasswd #gantipasswd_error').text('Password baru harus terisi');
        return false;
    }
    else if (ulangi_passwd_baru == "")
    {
        $('#formGantiPasswd #gantipasswd_error').text('Ulangi Password baru harus terisi');
        return false;
    }
    else if (passwd_baru != ulangi_passwd_baru)
    {
        $('#formGantiPasswd #gantipasswd_error').text('Password baru dengan Ulangi Password baru tidak sama');
        return false;
    }
    else
    {
       //ajax
        //ajax update
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url : '{{route('petugas.gantipassword')}}',
            method : 'post',
            data: {
                passwd_lama: passwd_lama,
                passwd_baru: passwd_baru,
                ulangi_passwd_baru: ulangi_passwd_baru,
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
                        location.replace('{{route('logout')}}');
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
///batas
</script>
