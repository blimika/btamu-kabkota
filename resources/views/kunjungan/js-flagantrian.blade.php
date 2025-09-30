<script>
$('#EditFlagAntrianModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var uid = button.data('uid')
    //load dulu transaksinya
    $.ajax({
        url : '{{route("webapi")}}/',
        method : 'get',
        data: {
            model: 'kunjungan',
            uid: uid
        },
        cache: false,
        dataType: 'json',
        success: function(d){
            if (d.status == true)
            {
                //value
            $('#EditFlagAntrianModal .modal-body #edit_flag_antrian').val(d.data.kunjungan_flag_antrian)
            $('#EditFlagAntrianModal .modal-body #edit_uid').val(d.data.kunjungan_uid)
            $('#EditFlagAntrianModal .modal-body #edit_id').val(d.data.kunjungan_id)
            $('#EditFlagAntrianModal .modal-body #kunjungan_id').text('#'+d.data.kunjungan_id)
            $('#EditFlagAntrianModal .modal-body #kunjungan_uid').text(d.data.kunjungan_uid)
            $('#EditFlagAntrianModal .modal-body #pengunjung_nama').text(d.data.pengunjung.pengunjung_nama)
            $('#EditFlagAntrianModal .modal-body #pengunjung_jk').text(d.data.pengunjung.pengunjung_jenis_kelamin)
            $('#EditFlagAntrianModal .modal-footer #pengunjung_timeline').attr("href","{{route('timeline','')}}/"+d.data.pengunjung.pengunjung_uid)
            $('#EditFlagAntrianModal .modal-body #kunjungan_tanggal').text(d.data.kunjungan_tanggal)
            $('#EditFlagAntrianModal .modal-body #kunjungan_nomor_antrian').text(d.data.kunjungan_teks_antrian)
            if (d.data.kunjungan_flag_antrian == 'ruang_tunggu')
            {
                var warna_flag_antrian = 'badge-danger';
            }
            else if (d.data.kunjungan_flag_antrian == 'dalam_layanan')
            {
                var warna_flag_antrian = 'badge-warning';
            }
            else
            {
                var warna_flag_antrian = 'badge-success';
            }
            $('#EditFlagAntrianModal .modal-body #kunjungan_flag_antrian').html('<span class="badge '+warna_flag_antrian+' badge-pill">'+d.data.kunjungan_flag_antrian+'</span>')
            if (d.data.kunjungan_jenis == 'perorangan')
            {
                //perorangan
                $('#EditFlagAntrianModal .modal-body #kunjungan_jenis').html('<span class="badge badge-info badge-pill">'+d.data.kunjungan_jenis+'</span>')
            }
            else
            {
                $('#EditFlagAntrianModal .modal-body #kunjungan_jenis').html('<span class="badge badge-primary badge-pill">'+d.data.kunjungan_jenis+' ('+d.data.kunjungan_jumlah_orang+' org)</span> <span class="badge badge-info badge-pill">L'+d.data.kunjungan_jumlah_pria+'</span> <span class="badge badge-danger badge-pill">P'+d.data.kunjungan_jumlah_wanita+'</span>')
            }

            if (d.data.kunjungan_tujuan == 1)
            {
                $('#EditFlagAntrianModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-info badge-pill">'+d.data.tujuan.tujuan_nama+'</span> <span class="badge badge-success badge-pill">'+d.data.layanan_kantor.layanan_kantor_nama+'</span>')
            }
            else if (d.data.kunjungan_tujuan == 2)
            {
                $('#EditFlagAntrianModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-info badge-pill">'+d.data.tujuan.tujuan_inisial+'</span> <span class="badge badge-success badge-pill">'+d.data.layanan_pst.layanan_pst_nama+'</span>')
            }
            else
            {
                $('#EditFlagAntrianModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-danger badge-pill">'+d.data.tujuan.tujuan_nama+'</span>')
            }
            $('#EditFlagAntrianModal .modal-body #kunjungan_jam_datang').text(GetJamMenit(d.data.kunjungan_jam_datang))
            $('#EditFlagAntrianModal .modal-body #kunjungan_jam_pulang').text(GetJamMenit(d.data.kunjungan_jam_pulang))

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

$('#EditFlagAntrianModal .modal-footer #updateFlagAntrian').on('click', function(e) {
    e.preventDefault();
    var kunjungan_id = $('#EditFlagAntrianModal .modal-body #edit_id').val();
    var kunjungan_uid = $('#EditFlagAntrianModal .modal-body #edit_uid').val();
    var kunjungan_flag_antrian = $('#EditFlagAntrianModal .modal-body #edit_flag_antrian').val();

    if (kunjungan_flag_antrian == "")
    {
        $('#EditFlagAntrianModal .modal-body #edit_kunjungan_error').text('Pilih salah satu flag antrian');
        return false;
    }
    else
    {
        //ajax responsen
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url : '{{route('flagantrian.update')}}',
            method : 'post',
            data: {
                kunjungan_id: kunjungan_id,
                kunjungan_uid: kunjungan_uid,
                kunjungan_flag_antrian: kunjungan_flag_antrian,
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
        //batas
    }

});
</script>
