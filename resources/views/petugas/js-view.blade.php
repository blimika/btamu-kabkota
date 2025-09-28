<script>
$('#ViewPetugasModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var uid = button.data('uid')
    //load dulu transaksinya
    $.ajax({
        url : '{{route("webapi")}}',
        method : 'get',
        data: {
            model:'petugas',
            uid: uid
        },
        cache: false,
        dataType: 'json',
        success: function(d){
            if (d.status == true)
            {
                $('#ViewPetugasModal .modal-body #petugas_nama').text(d.data.name)
                $('#ViewPetugasModal .modal-body #petugas_username').text(d.data.username)
                $('#ViewPetugasModal .modal-body #petugas_telepon').text(d.data.user_telepon)
                if (d.data.user_telepon != null)
                {
                    var petugas_telepon = d.data.user_telepon.substr(1);
                    var petugas_wa = "http://wa.me/62"+petugas_telepon;
                }
                else
                {
                    var petugas_wa = "#";
                }
                $('#ViewPetugasModal .modal-body #petugas_wa').attr("href",petugas_wa)
                $('#ViewPetugasModal .modal-body #petugas_level').text(d.data.user_level)
                if (d.data.email_kodever == 0)
                {
                    $('#ViewPetugasModal .modal-body #petugas_email').text(d.data.email)
                }
                else
                {
                    $('#ViewPetugasModal .modal-body #petugas_email').text(d.data.ganti_email)
                }
                //lastlogin di cek dan lastip
                if (d.data.user_last_login != null)
                {
                    $('#ViewPetugasModal .modal-body #petugas_last_login').text(d.data.user_last_login)
                    $('#ViewPetugasModal .modal-body #petugas_last_ip').text(d.data.user_last_ip)
                    $('#ViewPetugasModal .modal-body #petugas_last_login').addClass('normal')
                    $('#ViewPetugasModal .modal-body #petugas_last_ip').addClass('normal')
                    $('#ViewPetugasModal .modal-body #petugas_last_login').removeClass('miring')
                    $('#ViewPetugasModal .modal-body #petugas_last_ip').removeClass('miring')
                }
                else
                {
                    $('#ViewPetugasModal .modal-body #petugas_last_login').text("belum pernah login")
                    $('#ViewPetugasModal .modal-body #petugas_last_ip').text("belum pernah login")
                    $('#ViewPetugasModal .modal-body #petugas_last_login').addClass('miring')
                    $('#ViewPetugasModal .modal-body #petugas_last_ip').addClass('miring')
                    $('#ViewPetugasModal .modal-body #petugas_last_login').removeClass('normal')
                    $('#ViewPetugasModal .modal-body #petugas_last_ip').removeClass('normal')
                }
                $('#ViewPetugasModal .modal-body #petugas_flag').text(d.data.user_flag)
                $('#ViewPetugasModal .modal-body #petugas_created').text(d.data.created_at)
                $('#ViewPetugasModal .modal-body #petugas_updated').text(d.data.updated_at)
                if (d.data.user_foto != null)
                    {
                        $('#ViewPetugasModal .modal-body #petugas_foto').attr("src",'{{asset("storage")}}'+d.data.user_foto)
                    }
                    else
                    {
                        $('#ViewPetugasModal .modal-body #petugas_foto').attr("src","https://placehold.co/480x360/0000FF/FFFFFF/?text=belum+ada+photo")
                    }
            }
            else
            {
                alert(data.hasil);
            }
        },
        error: function(){
            alert("error load view");
        }

    });
});
</script>
