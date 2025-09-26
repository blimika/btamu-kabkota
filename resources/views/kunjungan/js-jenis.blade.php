<script>
    //jenis kunjungan
$('#EditJenisKunjunganModal').on('show.bs.modal', function (event) {
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
            $('#EditJenisKunjunganModal .modal-body #edit_flag_antrian').val(d.data.kunjungan_flag_antrian)
            $('#EditJenisKunjunganModal .modal-body #edit_uid').val(d.data.kunjungan_uid)
            $('#EditJenisKunjunganModal .modal-body #edit_id').val(d.data.kunjungan_id)
            $('#EditJenisKunjunganModal .modal-body #kunjungan_id').text('#'+d.data.kunjungan_id)
            $('#EditJenisKunjunganModal .modal-body #kunjungan_uid').text(d.data.kunjungan_uid)
            $('#EditJenisKunjunganModal .modal-body #pengunjung_nama').text(d.data.pengunjung.pengunjung_nama)
            $('#EditJenisKunjunganModal .modal-body #pengunjung_jk').text(d.data.pengunjung.pengunjung_jenis_kelamin)
            $('#EditJenisKunjunganModal .modal-body #kunjungan_tanggal').text(d.data.kunjungan_tanggal)
            $('#EditJenisKunjunganModal .modal-body #kunjungan_nomor_antrian').text(d.data.kunjungan_teks_antrian)
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
            $('#EditJenisKunjunganModal .modal-body #kunjungan_flag_antrian').html('<span class="badge '+warna_flag_antrian+' badge-pill">'+d.data.kunjungan_flag_antrian+'</span>')
            if (d.data.kunjungan_jenis == 'perorangan')
            {
                //perorangan
                $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis').html('<span class="badge badge-info badge-pill">'+d.data.kunjungan_jenis+'</span>')
            }
            else
            {
                $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis').html('<span class="badge badge-primary badge-pill">'+d.data.kunjungan_jenis+' ('+d.data.kunjungan_jumlah_orang+' org)</span> <span class="badge badge-info badge-pill">L'+d.data.kunjungan_jumlah_pria+'</span> <span class="badge badge-danger badge-pill">P'+d.data.kunjungan_jumlah_wanita+'</span>')
            }

            if (d.data.kunjungan_tujuan == 1)
            {
                $('#EditJenisKunjunganModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-info badge-pill">'+d.data.tujuan.tujuan_nama+'</span> <span class="badge badge-success badge-pill">'+d.data.layanan_kantor.layanan_kantor_nama+'</span>')
            }
            else if (d.data.kunjungan_tujuan == 2)
            {
                $('#EditJenisKunjunganModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-info badge-pill">'+d.data.tujuan.tujuan_inisial+'</span> <span class="badge badge-success badge-pill">'+d.data.layanan_pst.layanan_pst_nama+'</span>')
            }
            else
            {
                $('#EditJenisKunjunganModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-danger badge-pill">'+d.data.tujuan.tujuan_nama+'</span>')
            }
            $('#EditJenisKunjunganModal .modal-body #kunjungan_jam_datang').text(GetJamMenit(d.data.kunjungan_jam_datang))
            $('#EditJenisKunjunganModal .modal-body #kunjungan_jam_pulang').text(GetJamMenit(d.data.kunjungan_jam_pulang))
            $('#EditJenisKunjunganModal .modal-body #kunjungan_petugas_nama').text(d.data.petugas.name)
            $('#EditJenisKunjunganModal .modal-body #kunjungan_keperluan').text(d.data.kunjungan_keperluan)
            $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis_baru').val(d.data.kunjungan_jenis)
                if (d.data.kunjungan_jenis == 'kelompok')
                {
                    $('#EditJenisKunjunganModal .modal-body #row_kelompok').show();
                }
                else
                {
                    $('#EditJenisKunjunganModal .modal-body #row_kelompok').hide();
                }
             $('#EditJenisKunjunganModal .modal-body #jumlah_orang').val(d.data.kunjungan_jumlah_orang)
             $('#EditJenisKunjunganModal .modal-body #jumlah_pria').val(d.data.kunjungan_jumlah_pria)
             $('#EditJenisKunjunganModal .modal-body #jumlah_wanita').val(d.data.kunjungan_jumlah_wanita)
             if (d.data.kunjungan_foto != null)
                {
                    $('#EditJenisKunjunganModal .modal-body #kunjungan_foto').attr("src",'{{asset("storage")}}'+d.data.kunjungan_foto)
                }
                else
                {
                    $('#EditJenisKunjunganModal .modal-body #tamu_foto').attr("src","https://placehold.co/480x360/0000FF/FFFFFF/?text=belum+ada+photo")
                }
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
    $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis_baru').change(function(){
    var kunjungan_jenis = $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis_baru').val();
    if (kunjungan_jenis == 'kelompok')
    {
        $('#EditJenisKunjunganModal .modal-body #row_kelompok').show();
    }
    else
    {
        $('#EditJenisKunjunganModal .modal-body #row_kelompok').hide();
    }

    });
});

$('#EditJenisKunjunganModal .modal-footer #simpanJenisKunjungan').on('click', function(e) {
    e.preventDefault();
    var kunjungan_id = $('#EditJenisKunjunganModal .modal-body #edit_id').val();
    var kunjungan_uid = $('#EditJenisKunjunganModal .modal-body #edit_uid').val();
    var kunjungan_jenis = $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis_baru').val();
    var jumlah_orang = $('#EditJenisKunjunganModal .modal-body #jumlah_orang').val();
    var jumlah_pria = $('#EditJenisKunjunganModal .modal-body #jumlah_pria').val();
    var jumlah_wanita = $('#EditJenisKunjunganModal .modal-body #jumlah_wanita').val();
    if (kunjungan_jenis == "")
    {
        $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis_error').text('Pilih salah satu jenis kunjungan');
        return false;
    }
    else if (kunjungan_jenis == 'kelompok')
    {
        if (jumlah_orang == "")
        {
            $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis_error').text('karena terpilih kelompok, jumlah pengunjung tidak boleh kosong');
            return false;
        }
        else if (jumlah_orang < 2)
        {
            $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis_error').text('karena terpilih kelompok, jumlah pengunjung minimal 2 orang');
            return false;
        }
        else if (jumlah_pria == "")
        {
            $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis_error').text('karena terpilih kelompok, jumlah pengunjung laki-laki minimal 0');
            return false;
        }
        else if (jumlah_wanita == "")
        {
            $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis_error').text('karena terpilih kelompok, jumlah pengunjung perempuan minimal 0');
            return false;
        }
        else if (jumlah_orang != (parseInt(jumlah_pria)+parseInt(jumlah_wanita)))
        {
            $('#EditJenisKunjunganModal .modal-body #kunjungan_jenis_error').text('Jumlah pengunjung total ('+jumlah_orang+') tidak sama dengan jumlah pengunjung laki-laki ('+jumlah_pria+') + jumlah pengunjung perempuan ('+jumlah_wanita+')');
            return false;
        }
        else
        {
            var isian_clear = true;
        }
    }
    else
    {
        var isian_clear = true;
    }

    if (isian_clear)
    {
        //ajax responsen
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url : '{{route("jeniskunjungan.save")}}',
            method : 'post',
            data: {
                kunjungan_id: kunjungan_id,
                kunjungan_uid: kunjungan_uid,
                kunjungan_jenis : kunjungan_jenis,
                jumlah_orang: jumlah_orang,
                jumlah_pria: jumlah_pria,
                jumlah_wanita: jumlah_wanita
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
//batas jenis
</script>
