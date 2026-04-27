<script>
//view feedback
$('#ViewPDFModal').on('show.bs.modal', function (event) {
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
            $('#ViewPDFModal .modal-body #edit_uid').val(d.data.kunjungan_uid)
            $('#ViewPDFModal .modal-body #edit_id').val(d.data.kunjungan_id)
            $('#ViewPDFModal .modal-body #kunjungan_id').text('#'+d.data.kunjungan_id)
            $('#ViewPDFModal .modal-body #kunjungan_uid').text(d.data.kunjungan_uid)
            $('#ViewPDFModal .modal-body #pengunjung_nama').text(d.data.pengunjung.pengunjung_nama)
            $('#ViewPDFModal .modal-body #pengunjung_jk').text(d.data.pengunjung.pengunjung_jenis_kelamin)
            $('#ViewPDFModal .modal-body #kunjungan_tanggal').text(d.data.kunjungan_tanggal)
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
            $('#ViewPDFModal .modal-body #kunjungan_flag_antrian').html('<span class="badge '+warna_flag_antrian+' badge-pill">'+d.data.kunjungan_flag_antrian+'</span>')
            if (d.data.kunjungan_jenis == 'perorangan')
            {
                //perorangan
                $('#ViewPDFModal .modal-body #kunjungan_jenis').html('<span class="badge badge-info badge-pill">'+d.data.kunjungan_jenis+'</span>')
            }
            else
            {
                $('#ViewPDFModal .modal-body #kunjungan_jenis').html('<span class="badge badge-primary badge-pill">'+d.data.kunjungan_jenis+' ('+d.data.kunjungan_jumlah_orang+' org)</span> <span class="badge badge-info badge-pill">L'+d.data.kunjungan_jumlah_pria+'</span> <span class="badge badge-danger badge-pill">P'+d.data.kunjungan_jumlah_wanita+'</span>')
            }

            if (d.data.kunjungan_tujuan == 1)
            {
                $('#ViewPDFModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-info badge-pill">'+d.data.tujuan.tujuan_nama+'</span> <span class="badge badge-success badge-pill">'+d.data.layanan_kantor.layanan_kantor_nama+'</span>')
            }
            else if (d.data.kunjungan_tujuan == 2)
            {
                $('#ViewPDFModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-info badge-pill">'+d.data.tujuan.tujuan_inisial+'</span> <span class="badge badge-success badge-pill">'+d.data.layanan_pst.layanan_pst_nama+'</span>')
            }
            else
            {
                $('#ViewPDFModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-danger badge-pill">'+d.data.tujuan.tujuan_nama+'</span>')
            }
            $('#ViewPDFModal .modal-body #kunjungan_petugas_nama').text(d.data.petugas.name)
            //$('#ViewKunjunganModal .modal-footer #pengunjung_timeline').attr("href","{{route('timeline','')}}/"+d.data.pengunjung.pengunjung_uid)
            //$('#ViewKunjunganModal .modal-body #kunjungan_tanggal').text(d.data.kunjungan_tanggal)
            if (d.data.kunjungan_pdf != null)
            {
                $('#ViewPDFModal .modal-body #pdf_permintaan').attr("src",'{{asset("storage")}}'+d.data.kunjungan_pdf)
            }
            else
            {
                $('#ViewPDFModal .modal-body #pdf_permintaan').attr("src","")
            }
            }
            else
            {
                alert(d.message);
            }
        },
        error: function(){
            alert("error load page");
        }

    });
});
//batas view feedback
</script>
