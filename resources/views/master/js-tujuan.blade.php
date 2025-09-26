<script>
$('#simpanTujuan').on('click', function(e) {
        e.preventDefault();
        var tujuan_kode = $('#tujuan_kode').val();
        var tujuan_inisial = $('#tujuan_inisial').val();
        var tujuan_nama = $('#tujuan_nama').val();
        var len = tujuan_inisial.length;
        if (tujuan_kode == "")
            {
                Swal.fire({
                    type: 'error',
                    title: 'error',
                    text: 'Kode Tujuan harus terisi'
                    });
                return false;
            }
        else if (tujuan_inisial == "")
            {
                Swal.fire({
                    type: 'error',
                    title: 'error',
                    text: 'Inisial harus terisi'
                    });
                return false;
            }
        else if (tujuan_inisial.length != 3)
            {
                Swal.fire({
                    type: 'error',
                    title: 'error',
                    text: 'Inisial harus 3 karakter'
                    });
                return false;
            }
        else if (!(/^[A-Za-z]*$/).test(tujuan_inisial))
            {
                Swal.fire({
                    type: 'error',
                    title: 'error',
                    text: 'Inisial harus berupa huruf tidak boleh yang lain'
                    });
                return false;
            }
        else if (tujuan_nama == "")
            {
                Swal.fire({
                    type: 'error',
                    title: 'error',
                    text: 'Nama harus terisi'
                    });
                return false;
            }
        else
        {
            //valid format
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            //ajax simpan ip address
            $.ajax({
            url : '{{route('master.simpantujuan')}}',
            method : 'post',
            data: {
                tujuan_kode: tujuan_kode,
                tujuan_inisial: tujuan_inisial,
                tujuan_nama: tujuan_nama,
            },
            cache: false,
            dataType: 'json',
            success: function(data){
                    if (data.status == true)
                    {
                        Swal.fire(
                            'Berhasil!',
                            ''+data.hasil+'',
                            'success'
                        ).then(function() {
                            $('#dTabel').DataTable().ajax.reload(null,false);
                        });
                    }
                    else
                    {
                        Swal.fire(
                            'Error!',
                            ''+data.hasil+'',
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
