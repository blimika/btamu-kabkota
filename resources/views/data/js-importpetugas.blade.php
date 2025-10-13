<script>
    //import excel jadwal petugas
    $('#ImportPetugasModal .modal-footer #BtnImportPetugas').on('click', function(e) {
        e.preventDefault();
        //var file_import_jadwal = $('#ImportJadwalModal .modal-body #file_import_jadwal')[0].files[0];

        var formData = new FormData();
        formData.append('file_import', $('#ImportPetugasModal .modal-body #file_import')[0].files[0]);
        //ajax upload file
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ route('pengunjung.import') }}',
            method: 'post',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.status == true) {
                    Swal.fire(
                        'Berhasil!',
                        '' + data.message + '',
                        'success'
                    ).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        '' + data.message + '',
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
        //batas ajax
    });
</script>
