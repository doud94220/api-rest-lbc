$(document).ready(function () {

    $("#submit").on('click', function (e) {

        $.ajax({
            url: '/validation/modification/put',
            type: 'PUT',
            //data: JSON.stringify(data),
            data: {
                "id": $('#id-annonce').val(),
                'titre': $('#titre-annonce').val(),
                'contenu': $('#contenu-annonce').val()
            },
            success: function () {
                alert("La modification de l'annonce en PUT est un succès !");
                window.location.href = "/home";
            },
            error: function (xhr, status, error) {
                alert('L appel en PUT a échoué...:' + xhr.responseText);
            }
        });
    });

});