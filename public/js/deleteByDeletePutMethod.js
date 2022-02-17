$(document).ready(function () {

    $("#submit").on('click', function (e) {

        $.ajax({
            url: '/validation/suppression',
            type: 'DELETE',
            //data: JSON.stringify(data),
            data: {
                "id": $('#id-annonce').val()
            },
            success: function () {
                alert("La suppression de l'annonce en DELETE est un succès !");
                window.location.href = "/home";
            },
            error: function (xhr, status, error) {
                alert('L appel en DELETE a échoué...:' + xhr.responseText);
            }
        });
    });

});