$(document).ready(function () {
    $("#templateSelection").change(function getTemplateFromDb(){
        $.ajax({
            type: 'GET',
            url: 'https://147.175.121.210:4117/webte2/uloha3/templateResource.php/templates/' + $('#templateSelection').val(),
            success: function (msg) {
                alert(msg);
            }
        });
    });
});