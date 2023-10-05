<script>
    $(document).ready(function(){
        $('.confirm').on('click', function(e){
            e.preventDefault(); //cancel default action

            swal({
                title: 'Rückmeldung löschen?',
                text: 'Willst du die Rückmeldung wirklich löschen?',
                icon: 'info',
                buttons: ["Abbrechen", "Ja"],
            }).then((willDelete) => {
                if (willDelete) {
                    document.getElementById("DeleteForm").submit();
                }
            });
        });
    });
    function editPost(post) {
        $('#post_id').val(post['id']);
        $('#comment').val(post['comment']);
        $('#user_id').val(post['user_id']);
        $('#show_on_survey').prop("checked", post['show_on_survey']);
    }
</script>
