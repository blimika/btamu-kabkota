<script>
$('#ViewMemberModal').on('show.bs.modal', function (event) {
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
                $('#ViewMemberModal .modal-body #petugas_nama').text(d.data.name)
                $('#ViewMemberModal .modal-body #petugas_username').text(d.data.username)
                $('#ViewMemberModal .modal-body #petugas_telepon').text(d.data.user_telepon)
                if (d.data.user_telepon != null)
                {
                    var petugas_telepon = d.data.user_telepon.substr(1);
                    var petugas_wa = "http://wa.me/62"+petugas_telepon;
                }
                else
                {
                    var petugas_wa = "#";
                }
                $('#ViewMemberModal .modal-body #petugas_wa').attr("href",petugas_wa)
                $('#ViewMemberModal .modal-body #petugas_level').text(d.data.user_level)
                if (d.data.email_kodever == 0)
                {
                    $('#ViewMemberModal .modal-body #petugas_email').text(d.data.email)
                }
                else
                {
                    $('#ViewMemberModal .modal-body #petugas_email').text(d.data.ganti_email)
                }
                //lastlogin di cek dan lastip
                if (d.data.user_last_login != null)
                {
                    $('#ViewMemberModal .modal-body #petugas_last_login').text(d.data.user_last_login)
                    $('#ViewMemberModal .modal-body #petugas_last_ip').text(d.data.user_last_ip)
                    $('#ViewMemberModal .modal-body #petugas_last_login').addClass('normal')
                    $('#ViewMemberModal .modal-body #petugas_last_ip').addClass('normal')
                    $('#ViewMemberModal .modal-body #petugas_last_login').removeClass('miring')
                    $('#ViewMemberModal .modal-body #petugas_last_ip').removeClass('miring')
                }
                else
                {
                    $('#ViewMemberModal .modal-body #petugas_last_login').text("belum pernah login")
                    $('#ViewMemberModal .modal-body #petugas_last_ip').text("belum pernah login")
                    $('#ViewMemberModal .modal-body #petugas_last_login').addClass('miring')
                    $('#ViewMemberModal .modal-body #petugas_last_ip').addClass('miring')
                    $('#ViewMemberModal .modal-body #petugas_last_login').removeClass('normal')
                    $('#ViewMemberModal .modal-body #petugas_last_ip').removeClass('normal')
                }
                $('#ViewMemberModal .modal-body #petugas_flag').text(d.data.user_flag)
                $('#ViewMemberModal .modal-body #petugas_created').text(d.data.created_at)
                $('#ViewMemberModal .modal-body #petugas_updated').text(d.data.updated_at)
                if (d.data.user_foto != null)
                    {
                        $('#ViewMemberModal .modal-body #petugas_foto').attr("src",'{{asset("storage")}}'+d.data.user_foto)
                    }
                    else
                    {
                        $('#ViewMemberModal .modal-body #petugas_foto').attr("src","https://placehold.co/480x360/0000FF/FFFFFF/?text=belum+ada+photo")
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
