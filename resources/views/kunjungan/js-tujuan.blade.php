<script>

//ubah tujuan
$('#EditTujuanModal').on('show.bs.modal', function (event) {
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
            $('#EditTujuanModal .modal-body #edit_flag_antrian').val(d.data.kunjungan_flag_antrian)
            $('#EditTujuanModal .modal-body #edit_uid').val(d.data.kunjungan_uid)
            $('#EditTujuanModal .modal-body #edit_id').val(d.data.kunjungan_id)
            $('#EditTujuanModal .modal-body #kunjungan_id').text('#'+d.data.kunjungan_id)
            $('#EditTujuanModal .modal-body #kunjungan_uid').text(d.data.kunjungan_uid)
            $('#EditTujuanModal .modal-body #pengunjung_nama').text(d.data.pengunjung.pengunjung_nama)
            $('#EditTujuanModal .modal-body #pengunjung_jk').text(d.data.pengunjung.pengunjung_jenis_kelamin)
            $('#EditTujuanModal .modal-body #kunjungan_tanggal').text(d.data.kunjungan_tanggal)
            $('#EditTujuanModal .modal-body #kunjungan_nomor_antrian').text(d.data.kunjungan_teks_antrian)
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
            $('#EditTujuanModal .modal-body #kunjungan_flag_antrian').html('<span class="badge '+warna_flag_antrian+' badge-pill">'+d.data.kunjungan_flag_antrian+'</span>')
            if (d.data.kunjungan_jenis == 'perorangan')
            {
                //perorangan
                $('#EditTujuanModal .modal-body #kunjungan_jenis').html('<span class="badge badge-info badge-pill">'+d.data.kunjungan_jenis+'</span>')
            }
            else
            {
                $('#EditTujuanModal .modal-body #kunjungan_jenis').html('<span class="badge badge-primary badge-pill">'+d.data.kunjungan_jenis+' ('+d.data.kunjungan_jumlah_orang+' org)</span> <span class="badge badge-info badge-pill">L'+d.data.kunjungan_jumlah_pria+'</span> <span class="badge badge-danger badge-pill">P'+d.data.kunjungan_jumlah_wanita+'</span>')
            }

            if (d.data.kunjungan_tujuan == 1)
            {
                $('#EditTujuanModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-info badge-pill">'+d.data.tujuan.tujuan_nama+'</span> <span class="badge badge-success badge-pill">'+d.data.layanan_kantor.layanan_kantor_nama+'</span>')
            }
            else if (d.data.kunjungan_tujuan == 2)
            {
                $('#EditTujuanModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-info badge-pill">'+d.data.tujuan.tujuan_inisial+'</span> <span class="badge badge-success badge-pill">'+d.data.layanan_pst.layanan_pst_nama+'</span>')
            }
            else
            {
                $('#EditTujuanModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-danger badge-pill">'+d.data.tujuan.tujuan_nama+'</span>')
            }
            $('#EditTujuanModal .modal-body #kunjungan_jam_datang').text(GetJamMenit(d.data.kunjungan_jam_datang))
            $('#EditTujuanModal .modal-body #kunjungan_jam_pulang').text(GetJamMenit(d.data.kunjungan_jam_pulang))
            $('#EditTujuanModal .modal-body #kunjungan_petugas_nama').text(d.data.petugas.name)
            $('#EditTujuanModal .modal-body #kunjungan_keperluan').text(d.data.kunjungan_keperluan)
            $('#EditTujuanModal .modal-body #kunjungan_tujuan_baru').val(d.data.kunjungan_tujuan)
                if (d.data.kunjungan_tujuan == 1)
                {
                    $('#EditTujuanModal .modal-body #row_layananpst').hide();
                    $('#EditTujuanModal .modal-body #row_layanankantor').show();
                    $('#EditTujuanModal .modal-body #layanankantor_kode_baru').val(d.data.layanan_kantor.layanan_kantor_kode)
                }
                else if (d.data.kunjungan_tujuan == 2)
                {
                    $('#EditTujuanModal .modal-body #row_layananpst').show();
                    $('#EditTujuanModal .modal-body #row_layanankantor').hide();
                    $('#EditTujuanModal .modal-body #layananpst_kode_baru').val(d.data.layanan_pst.layanan_pst_kode)
                }
                else
                {
                    $('#EditTujuanModal .modal-body #row_layananpst').hide();
                    $('#EditTujuanModal .modal-body #row_layanankantor').hide();
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
    $('#EditTujuanModal .modal-body #kunjungan_tujuan_baru').change(function(){
    var kunjungan_tujuan = $('#EditTujuanModal .modal-body #kunjungan_tujuan_baru').val();
    if (kunjungan_tujuan == 1)
    {
        $('#EditTujuanModal .modal-body #row_layananpst').hide();
        $('#EditTujuanModal .modal-body #row_layanankantor').show();
    }
    else if (kunjungan_tujuan == 2)
    {
        $('#EditTujuanModal .modal-body #row_layananpst').show();
        $('#EditTujuanModal .modal-body #row_layanankantor').hide();
    }
    else
    {
        $('#EditTujuanModal .modal-body #row_layananpst').hide();
        $('#EditTujuanModal .modal-body #row_layanankantor').hide();
    }

});
});

$('#EditTujuanModal .modal-footer #simpanTujuanBaru').on('click', function(e) {
    e.preventDefault();
    var kunjungan_id = $('#EditTujuanModal .modal-body #edit_id').val();
    var kunjungan_uid = $('#EditTujuanModal .modal-body #edit_uid').val();
    var tujuan_baru = $('#EditTujuanModal .modal-body #kunjungan_tujuan_baru').val();
    var layanan_pst_baru = $('#EditTujuanModal .modal-body #layananpst_kode_baru').val();
    var layanan_kantor_baru = $('#EditTujuanModal .modal-body #layanankantor_kode_baru').val();
    if (tujuan_baru == "")
    {
        $('#EditTujuanModal .modal-body #tujuan_baru_error').text('Pilih salah satu tujuan');
        return false;
    }
    else if (tujuan_baru == 2 && layanan_pst_baru == 99 )
    {
        $('#EditTujuanModal .modal-body #tujuan_baru_error').text('Tujuan PST, layanan pst harus terpilih selain lainnya');
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
            url : '{{route("tujuanbaru.save")}}',
            method : 'post',
            data: {
                kunjungan_id: kunjungan_id,
                kunjungan_uid: kunjungan_uid,
                kunjungan_tujuan_baru: tujuan_baru,
                kunjungan_layanan_pst_baru: layanan_pst_baru,
                kunjungan_layanan_kantor_baru: layanan_kantor_baru
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
//batas ubah tujuan
</script>
