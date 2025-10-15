<script>
$('#ViewKunjunganModal').on('show.bs.modal', function (event) {
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
            $('#ViewKunjunganModal .modal-body #kunjungan_id').text('#'+d.data.kunjungan_id)
            $('#ViewKunjunganModal .modal-body #kunjungan_uid').text(d.data.kunjungan_uid)
            $('#ViewKunjunganModal .modal-body #pengunjung_nama').text(d.data.pengunjung.pengunjung_nama)
            $('#ViewKunjunganModal .modal-body #pengunjung_jk').text(d.data.pengunjung.pengunjung_jenis_kelamin)
            $('#ViewKunjunganModal .modal-body #pengunjung_tahun_lahir').text(d.data.pengunjung.pengunjung_tahun_lahir+' ('+ GetUmur(d.data.pengunjung.pengunjung_tahun_lahir) + ' tahun)')
            $('#ViewKunjunganModal .modal-body #pengunjung_pekerjaan').text(d.data.pengunjung.pengunjung_pekerjaan)
            $('#ViewKunjunganModal .modal-body #pengunjung_pendidikan').text(d.data.pengunjung.pendidikan.pendidikan_nama)
            $('#ViewKunjunganModal .modal-body #pengunjung_email').text(d.data.pengunjung.pengunjung_email)
            $('#ViewKunjunganModal .modal-body #pengunjung_nomor_hp').text(d.data.pengunjung.pengunjung_nomor_hp)
            if (d.data.pengunjung.pengunjung_nomor_hp != null)
            {
                var pengunjung_nomor_hp = d.data.pengunjung.pengunjung_nomor_hp.substr(1);
                var pengunjung_wa = "http://wa.me/62"+pengunjung_nomor_hp;
            }
            else
            {
                var pengunjung_wa = "#";
            }
            $('#ViewKunjunganModal .modal-body #pengunjung_wa').attr("href",pengunjung_wa)
            $('#ViewKunjunganModal .modal-body #pengunjung_alamat').text(d.data.pengunjung.pengunjung_alamat)
            $('#ViewKunjunganModal .modal-footer #pengunjung_timeline').attr("href","{{route('timeline','')}}/"+d.data.pengunjung.pengunjung_uid)
            $('#ViewKunjunganModal .modal-body #kunjungan_tanggal').text(d.data.kunjungan_tanggal)
            if (d.data.kunjungan_foto != null)
                {
                    $('#ViewKunjunganModal .modal-body #kunjungan_foto').attr("src",'{{asset("storage")}}'+d.data.kunjungan_foto)
                }
                else
                {
                    $('#ViewKunjunganModal .modal-body #kunjungan_foto').attr("src","https://placehold.co/480x360/0000FF/FFFFFF/?text=belum+ada+photo")
                }
            $('#ViewKunjunganModal .modal-body #kunjungan_nomor_antrian').text(d.data.kunjungan_teks_antrian)
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
            $('#ViewKunjunganModal .modal-body #kunjungan_flag_antrian').html('<span class="badge '+warna_flag_antrian+' badge-pill">'+d.data.kunjungan_flag_antrian+'</span>')
            if (d.data.kunjungan_jenis == 'perorangan')
            {
                //perorangan
                $('#ViewKunjunganModal .modal-body #kunjungan_jenis').html('<span class="badge badge-info badge-pill">'+d.data.kunjungan_jenis+'</span>')
            }
            else
            {
                $('#ViewKunjunganModal .modal-body #kunjungan_jenis').html('<span class="badge badge-primary badge-pill">'+d.data.kunjungan_jenis+' ('+d.data.kunjungan_jumlah_orang+' org)</span> <span class="badge badge-info badge-pill">L'+d.data.kunjungan_jumlah_pria+'</span> <span class="badge badge-danger badge-pill">P'+d.data.kunjungan_jumlah_wanita+'</span>')
            }

            if (d.data.kunjungan_tujuan == 1)
            {
                $('#ViewKunjunganModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-info badge-pill">'+d.data.tujuan.tujuan_nama+'</span> <span class="badge badge-success badge-pill">'+d.data.layanan_kantor.layanan_kantor_nama+'</span>')
            }
            else if (d.data.kunjungan_tujuan == 2)
            {
                $('#ViewKunjunganModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-info badge-pill">'+d.data.tujuan.tujuan_inisial+'</span> <span class="badge badge-success badge-pill">'+d.data.layanan_pst.layanan_pst_nama+'</span>')
            }
            else
            {
                $('#ViewKunjunganModal .modal-body #kunjungan_tujuan').html('<span class="badge badge-danger badge-pill">'+d.data.tujuan.tujuan_nama+'</span>')
            }
            if (d.data.kunjungan_jam_datang != null)
            {
                $('#ViewKunjunganModal .modal-body #kunjungan_jam_datang').text(GetJamMenit(d.data.kunjungan_jam_datang))
            }
            else
            {
                $('#ViewKunjunganModal .modal-body #kunjungan_jam_datang').html("<i>--belum tersedia--</i>")
            }
            if (d.data.kunjungan_jam_pulang != null)
            {
                $('#ViewKunjunganModal .modal-body #kunjungan_jam_pulang').text(GetJamMenit(d.data.kunjungan_jam_pulang))
            }
            else
            {
                $('#ViewKunjunganModal .modal-body #kunjungan_jam_pulang').html("<i>--belum tersedia--</i>")
            }

            if (d.data.petugas != null)
            {
                var petugas_pelayanan = "<b>"+ d.data.petugas.name +"</b>";
            }
            else
            {
                var petugas_pelayanan = "<i>--belum tersedia--</i>";
            }
            if (d.data.kunjungan_flag_feedback == 'sudah')
            {
                var nilai_feedback = d.data.kunjungan_nilai_feedback;
                var rating_layanan = "";
                for (i = 1; i < 7; i++) {
                    if (i <= nilai_feedback)
                    {
                        rating_layanan += '<span class="fa fa-star text-warning"></span>';
                    }
                    else
                    {
                        rating_layanan +='<span class="fa fa-star"></span>';
                    }
                }

                var sarpras_feedback = d.data.kunjungan_sarpras_feedback;
                var rating_sarpras = "";
                for (i = 1; i < 7; i++) {
                    if (i <= sarpras_feedback)
                    {
                        rating_sarpras += '<span class="fa fa-star text-warning"></span>';
                    }
                    else
                    {
                        rating_sarpras +='<span class="fa fa-star"></span>';
                    }
                }
                var warna_flag_feedback = 'badge-success';
                var teks_flag_feedback = 'sudah';
            }
            else
            {
                var rating_layanan = "<i>--belum tersedia--</i>";
                var rating_sarpras = "<i>--belum tersedia--</i>";
                var warna_flag_feedback = 'badge-danger';
                var teks_flag_feedback = 'belum';
            }
            if (d.data.kunjungan_tanggal_feedback == null)
            {
                var tanggal_feedback = "<i>---tidak tersedia----</i>";
            }
            else
            {
                //var tanggal_feedback = moment(d.data.kunjungan_tanggal_feedback);
                var tanggal_feedback = moment(d.data.kunjungan_tanggal_feedback).locale('id').format('LLLL');
            }
            if (d.data.kunjungan_ip_feedback == null)
            {
                var ip_feedback = "<i>---tidak tersedia----</i>";
            }
            else
            {
                var ip_feedback = d.data.kunjungan_ip_feedback;
            }
            if (d.data.kunjungan_komentar_feedback == null)
            {
                var komentar_feedback = "-";
            }
            else
            {
                var komentar_feedback = "<i>"+d.data.kunjungan_komentar_feedback+"</i>";
            }
            if (d.data.kunjungan_agent_feedback == null)
            {
                var agent_feedback = "<i>---tidak tersedia----</i>";
            }
            else
            {
                var agent_feedback = "<i>"+d.data.kunjungan_agent_feedback+"</i>";
            }
            $('#ViewKunjunganModal .modal-body #rating_layanan').html(rating_layanan)
            $('#ViewKunjunganModal .modal-body #rating_sarpras').html(rating_sarpras)
            $('#ViewKunjunganModal .modal-body #kunjungan_flag_feedback').html('<span class="badge '+warna_flag_feedback+' badge-pill">'+teks_flag_feedback+'</span>')
            $('#ViewKunjunganModal .modal-body #petugas_layanan').html(petugas_pelayanan)
            $('#ViewKunjunganModal .modal-body #kunjungan_keperluan').html(d.data.kunjungan_keperluan)
            $('#ViewKunjunganModal .modal-body #kunjungan_tindak_lanjut').html(d.data.kunjungan_tindak_lanjut)
            $('#ViewKunjunganModal .modal-body #kunjungan_komentar_feedback').html(komentar_feedback)
            $('#ViewKunjunganModal .modal-body #kunjungan_tanggal_feedback').html(tanggal_feedback)
            $('#ViewKunjunganModal .modal-body #kunjungan_ip_feedback').html(ip_feedback)
            $('#ViewKunjunganModal .modal-body #kunjungan_agent_feedback').html(agent_feedback)
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
</script>
