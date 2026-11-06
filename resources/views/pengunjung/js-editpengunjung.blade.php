<script type="text/javascript">
function GetUmur(birthDateString) {
    var today = new Date();
    var age = today.getFullYear() - parseInt(birthDateString, 10);
    return isNaN(age) ? 0 : age;
}
$('#EditPengunjungModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var uid = button.data('uid');

    // Reset error message
    $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('');

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
                $('#EditPengunjungModal .modal-body #pengunjung_id').val(d.data.pengunjung_id);
                $('#EditPengunjungModal .modal-body #pengunjung_uid').val(d.data.pengunjung_uid);
                $('#EditPengunjungModal .modal-body #teks_pengunjung_id').text('#' + d.data.pengunjung_id);
                $('#EditPengunjungModal .modal-body #teks_pengunjung_uid').text(d.data.pengunjung_uid);
                $('#EditPengunjungModal .modal-body #pengunjung_nomor_hp').val(d.data.pengunjung_nomor_hp || '');
                $('#EditPengunjungModal .modal-body #pengunjung_nama').val(d.data.pengunjung_nama || '');
                $('#EditPengunjungModal .modal-body #pengunjung_jk').val(d.data.pengunjung_jenis_kelamin || '');
                $('#EditPengunjungModal .modal-body #pengunjung_tahun_lahir').val(d.data.pengunjung_tahun_lahir || '');
                $('#EditPengunjungModal .modal-body #pengunjung_pekerjaan').val(d.data.pengunjung_pekerjaan || '');
                $('#EditPengunjungModal .modal-body #pengunjung_pendidikan').val(d.data.pengunjung_pendidikan || '');
                $('#EditPengunjungModal .modal-footer #pengunjung_timeline').attr("href", "{{ url('timeline') }}/" + d.data.pengunjung_uid);
                $('#EditPengunjungModal .modal-body #pengunjung_email').val(d.data.pengunjung_email || '');
                $('#EditPengunjungModal .modal-body #pengunjung_alamat').val(d.data.pengunjung_alamat || '');
            } else {
                Swal.fire(
                    'Error!',
                    '' + (d ? d.message : 'Data tidak tersedia') + '',
                    'error'
                );
            }
        },
        error: function() {
            Swal.fire(
                'Error!',
                'Koneksi error',
                'error'
            );
        }
    });
});

$('#EditPengunjungModal .modal-footer #updatePengunjung').on('click', function(e) {
    e.preventDefault();
    $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('');

    var pengunjung_id = $('#EditPengunjungModal .modal-body #pengunjung_id').val();
    var pengunjung_uid = $('#EditPengunjungModal .modal-body #pengunjung_uid').val();
    var pengunjung_nomor_hp = $.trim($('#EditPengunjungModal .modal-body #pengunjung_nomor_hp').val());
    var pengunjung_nama = $.trim($('#EditPengunjungModal .modal-body #pengunjung_nama').val());
    var pengunjung_jk = $('#EditPengunjungModal .modal-body #pengunjung_jk').val();
    var pengunjung_tahun_lahir = $.trim($('#EditPengunjungModal .modal-body #pengunjung_tahun_lahir').val());
    var pengunjung_pekerjaan = $.trim($('#EditPengunjungModal .modal-body #pengunjung_pekerjaan').val());
    var pengunjung_pendidikan = $('#EditPengunjungModal .modal-body #pengunjung_pendidikan').val();
    var pengunjung_email = $.trim($('#EditPengunjungModal .modal-body #pengunjung_email').val());
    var pengunjung_alamat = $.trim($('#EditPengunjungModal .modal-body #pengunjung_alamat').val());

    if (pengunjung_nomor_hp == "") {
        $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('Nomor HP tidak boleh kosong');
        return false;
    } else if (pengunjung_nomor_hp.match(/[^\d+]/)) {
        $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('Silakan masukkan nomor handphone hanya angka');
        return false;
    } else if (pengunjung_nama == "") {
        $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('Isian nama tidak boleh kosong');
        return false;
    } else if (pengunjung_jk == "") {
        $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('Pilih salah satu Jenis Kelamin');
        return false;
    } else if (pengunjung_tahun_lahir == "") {
        $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('Tahun lahir tidak boleh kosong');
        return false;
    } else if (isNaN(pengunjung_tahun_lahir) || GetUmur(pengunjung_tahun_lahir) <= 13) {
        $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('Umur minimal 14 tahun');
        return false;
    } else if (pengunjung_pekerjaan == "") {
        $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('Isian pekerjaan tidak boleh kosong');
        return false;
    } else if (pengunjung_pendidikan == "") {
        $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('Pilih salah satu pendidikan');
        return false;
    } else if (pengunjung_email == "") {
        $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('Isian email harus terisi');
        return false;
    } else if (pengunjung_alamat == "") {
        $('#EditPengunjungModal .modal-body #edit_pengunjung_error').text('Isian alamat harus terisi');
        return false;
    }

    Swal.fire({
        title: 'Menyimpan...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url : '{{ route("pengunjung.update") }}',
        method : 'post',
        data: {
            pengunjung_id: pengunjung_id,
            pengunjung_uid: pengunjung_uid,
            pengunjung_nomor_hp : pengunjung_nomor_hp,
            pengunjung_nama: pengunjung_nama,
            pengunjung_jk: pengunjung_jk,
            pengunjung_tahun_lahir: pengunjung_tahun_lahir,
            pengunjung_pekerjaan: pengunjung_pekerjaan,
            pengunjung_pendidikan: pengunjung_pendidikan,
            pengunjung_email: pengunjung_email,
            pengunjung_alamat: pengunjung_alamat
        },
        cache: false,
        dataType: 'json',
        success: function(data) {
            if (data && data.status == true) {
                $('#EditPengunjungModal').modal('hide');
                Swal.fire(
                    'Berhasil!',
                    '' + data.message + '',
                    'success'
                ).then(function() {
                    $('#dTabel').DataTable().ajax.reload(null, false);
                });
            } else {
                Swal.fire(
                    'Error!',
                    '' + (data ? data.message : 'Gagal memperbarui data') + '',
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
</script>
