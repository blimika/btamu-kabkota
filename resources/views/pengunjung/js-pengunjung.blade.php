<script type="text/javascript">
function GetUmur(birthDateString) {
    var today = new Date();
    var age = today.getFullYear() - parseInt(birthDateString, 10);
    return isNaN(age) ? '-' : age;
}
// View Modal
$('#ViewPengunjungModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var uid = button.data('uid');

    // Reset initial fields
    $('#ViewPengunjungModal .modal-body #pengunjung_id').text('Loading...');
    $('#ViewPengunjungModal .modal-body #pengunjung_uid').text(uid || '');
    $('#ViewPengunjungModal .modal-body #pengunjung_nama').text('');
    $('#ViewPengunjungModal .modal-body #pengunjung_jk').text('');
    $('#ViewPengunjungModal .modal-body #pengunjung_tahun_lahir').text('');
    $('#ViewPengunjungModal .modal-body #pengunjung_pekerjaan').text('');
    $('#ViewPengunjungModal .modal-body #pengunjung_pendidikan').text('');
    $('#ViewPengunjungModal .modal-body #pengunjung_email').text('');
    $('#ViewPengunjungModal .modal-body #pengunjung_nomor_hp').text('');
    $('#ViewPengunjungModal .modal-body #pengunjung_wa').hide();
    $('#ViewPengunjungModal .modal-body #pengunjung_alamat').text('');
    $('#ViewPengunjungModal .modal-body #kunjungan_terakhir').text('Loading...');
    $('#ViewPengunjungModal .modal-body #pengunjung_foto').attr("src", "https://placehold.co/480x360/0000FF/FFFFFF/?text=loading...");
    $('#ViewPengunjungModal .modal-footer #pengunjung_timeline').attr("href", "{{ url('timeline') }}/" + uid);

    // Load data pengunjung
    $.ajax({
        url : '{{ route("webapi") }}',
        method : 'get',
        data: {
            model: 'pengunjung',
            uid: uid
        },
        cache: false,
        dataType: 'json',
        success: function(d) {
            if (d && d.status == true && d.data) {
                var item = d.data;
                $('#ViewPengunjungModal .modal-body #pengunjung_id').text('#' + item.pengunjung_id);
                $('#ViewPengunjungModal .modal-body #pengunjung_uid').text(item.pengunjung_uid);
                $('#ViewPengunjungModal .modal-body #pengunjung_nama').text(item.pengunjung_nama || '-');

                var jk = item.pengunjung_jenis_kelamin;
                if (jk === 'laki_laki') {
                    $('#ViewPengunjungModal .modal-body #pengunjung_jk').text('Laki-Laki');
                } else if (jk === 'perempuan') {
                    $('#ViewPengunjungModal .modal-body #pengunjung_jk').text('Perempuan');
                } else {
                    $('#ViewPengunjungModal .modal-body #pengunjung_jk').text(jk || '-');
                }

                if (item.pengunjung_tahun_lahir) {
                    var umur = GetUmur(item.pengunjung_tahun_lahir);
                    $('#ViewPengunjungModal .modal-body #pengunjung_tahun_lahir').text(item.pengunjung_tahun_lahir + ' (' + umur + ' tahun)');
                } else {
                    $('#ViewPengunjungModal .modal-body #pengunjung_tahun_lahir').text('-');
                }

                $('#ViewPengunjungModal .modal-body #pengunjung_pekerjaan').text(item.pengunjung_pekerjaan || '-');
                $('#ViewPengunjungModal .modal-body #pengunjung_pendidikan').text(item.pendidikan ? item.pendidikan.pendidikan_nama : '-');
                $('#ViewPengunjungModal .modal-body #pengunjung_email').text(item.pengunjung_email || '-');
                $('#ViewPengunjungModal .modal-body #pengunjung_alamat').text(item.pengunjung_alamat || '-');

                if (item.pengunjung_nomor_hp) {
                    var hp = item.pengunjung_nomor_hp.toString().replace(/[^0-9]/g, '');
                    if (hp.startsWith('0')) {
                        hp = '62' + hp.substring(1);
                    }
                    $('#ViewPengunjungModal .modal-body #pengunjung_nomor_hp').text(item.pengunjung_nomor_hp);
                    $('#ViewPengunjungModal .modal-body #pengunjung_wa').attr("href", "https://wa.me/" + hp).show();
                } else {
                    $('#ViewPengunjungModal .modal-body #pengunjung_nomor_hp').text('-');
                    $('#ViewPengunjungModal .modal-body #pengunjung_wa').hide();
                }

                $('#ViewPengunjungModal .modal-footer #pengunjung_timeline').attr("href", "{{ url('timeline') }}/" + item.pengunjung_uid);

                if (item.pengunjung_foto_profil) {
                    var foto = item.pengunjung_foto_profil;
                    if (!foto.startsWith('/')) {
                        foto = '/' + foto;
                    }
                    $('#ViewPengunjungModal .modal-body #pengunjung_foto').attr("src", '{{ asset("storage") }}' + foto);
                } else {
                    $('#ViewPengunjungModal .modal-body #pengunjung_foto').attr("src", "https://placehold.co/480x360/0000FF/FFFFFF/?text=belum+ada+photo");
                }

                if (item.kunjungan && item.kunjungan.length > 0) {
                    var teks = "";
                    var tmax = Math.min(item.kunjungan.length, 10);
                    for (var i = 0; i < tmax; i++) {
                        var k = item.kunjungan[i];
                        teks += "🟢 <strong>" + (k.kunjungan_tanggal || '-') + "</strong> (" + (k.kunjungan_keperluan || '-') + ")<br />";
                    }
                    $('#ViewPengunjungModal .modal-body #kunjungan_terakhir').html(teks);
                } else {
                    $('#ViewPengunjungModal .modal-body #kunjungan_terakhir').text('tidak ada kunjungan');
                }
            } else {
                Swal.fire(
                    'Error',
                    '' + (d ? d.message : 'Data tidak ditemukan') + '',
                    'error'
                );
            }
        },
        error: function() {
            Swal.fire(
                'Error',
                'Koneksi Error',
                'error'
            );
        }
    });
});
// Batas view
</script>
