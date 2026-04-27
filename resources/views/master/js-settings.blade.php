<script>
//edit modal view
$('#EditSettingModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var id = button.data('id')
    var kunci = button.data('kunci')
    var teks = button.data('teks')
    var nilai = button.data('nilai')
    $('#EditSettingModal .modal-body #view_id').text(id);
    $('#EditSettingModal .modal-body #view_key').text(kunci);
    $('#EditSettingModal .modal-body #view_label').text(teks);
    $('#EditSettingModal .modal-body #edit_value').val(nilai);
    $('#EditSettingModal .modal-body #edit_id').val(id);
    $('#EditSettingModal .modal-body #edit_key').val(kunci);
    $('#EditSettingModal .modal-body #edit_label').val(teks);
});
//batas
//cek sblm submit
$('#EditSettingModal .modal-footer #updatesetting').on('click', function(e) {
    e.preventDefault();
    var id = $('#EditSettingModal .modal-body #edit_id').val();
    var key = $('#EditSettingModal .modal-body #edit_key').val();
    var value = $('#EditSettingModal .modal-body #edit_value').val();
    var label = $('#EditSettingModal .modal-body #edit_label').val();

    if (value == "")
    {
        $('#EditSettingModal .modal-body #setting_error').text('Value tidak boleh kosong');
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
                url : '{{route('master.updatesetting')}}',
                method : 'post',
                data: {
                    edit_id: id,
                    edit_key: key,
                    edit_value: value,
                    edit_label: label,
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
                            location.reload(true);
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
