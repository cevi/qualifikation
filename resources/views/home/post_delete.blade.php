<script type="module">
    $(document).ready(function(){
        $('.confirm').on('click', function(e){
            e.preventDefault(); //cancel default action

            Swal.fire({
                title: 'Rückmeldung löschen?',
                text: 'Willst du die Rückmeldung wirklich löschen?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Ja',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: 'blue',
                cancelButtonColor: 'red',
            }).then((result) => {
                if (result.isConfirmed) {
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
    window.editPost = editPost;
</script>
