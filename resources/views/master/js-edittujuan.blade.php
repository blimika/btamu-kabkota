<script>
//edit modal view
$('#EditTujuanModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var id = button.data('id')
    var kode = button.data('kode')
    var inisial = button.data('inisial')
    var nama = button.data('nama')
    $('#EditTujuanModal .modal-body #edit_id').text(id);
    $('#EditTujuanModal .modal-body #edit_kode').text(kode);
    $('#EditTujuanModal .modal-body #edit_tujuan_inisial').val(inisial);
    $('#EditTujuanModal .modal-body #edit_tujuan_nama').val(nama);
    $('#EditTujuanModal .modal-body #edit_tujuan_id').val(id);
});
//batas
//cek sblm submit
$('#EditTujuanModal .modal-footer #updatetujuan').on('click', function(e) {
    e.preventDefault();
    var id = $('#EditTujuanModal .modal-body #edit_tujuan_id').val();
    var inisial = $('#EditTujuanModal .modal-body #edit_tujuan_inisial').val();
    var nama = $('#EditTujuanModal .modal-body #edit_tujuan_nama').val();

    if (inisial == "")
    {
        $('#EditTujuanModal .modal-body #edit_tujuan_error').text('Inisial tidak boleh kosong');
        return false;
    }
    else if (inisial.length != 3)
    {
        $('#EditTujuanModal .modal-body #edit_tujuan_error').text('Inisial harus 3 karakter');
        return false;
    }
    else if (!(/^[A-Za-z]*$/).test(inisial))
    {
         $('#EditTujuanModal .modal-body #edit_tujuan_error').text('Inisial harus berupa huruf tidak boleh yang lain');
        return false;
    }
    else if (nama == "")
    {
         $('#EditTujuanModal .modal-body #edit_tujuan_error').text('nama tidak boleh kosong');
        return false;
    }
    else
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
            $.ajax({
                url : '{{route('master.updatetujuan')}}',
                method : 'post',
                data: {
                    edit_tujuan_id: id,
                    edit_tujuan_inisial: inisial,
                    edit_tujuan_nama: nama,
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
    }
});
//batas
</script>
