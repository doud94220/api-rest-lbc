$(document).ready(function () {

    $("#submit").on('click', function (e) {

        let data = { "id": $('#id-annonce').val() }

        console.log(data);

        $.ajax({
            url: '/validation/modification/put/' + $('#id-annonce').val(),
            type: 'PUT',
            data: JSON.stringify(data),
            success: function () {
                alert('L appel en PUT a fonctionné');
            },
            error: function (xhr, status, error) {
                alert('L appel en PUT a échoué...:' + xhr.responseText);
            }
        });
    });

});