$(document).ready(function() {
    $('.remove-item').click(function(e) {
        e.preventDefault();

        var produktId = $(this).data('produkt-id');

        $.ajax({
            url: 'kosik.php',
            type: 'GET',
            data: { odstranit_id: produktId },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    alert(data.message);  
                    location.reload();  // Znovu načteme stránku, aby se aktualizoval košík
                }
            },
            error: function() {
                alert('Došlo k chybě při odstraňování produktu z košíku.');
            }
        });
    });
});
