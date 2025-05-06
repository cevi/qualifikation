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
</script>
