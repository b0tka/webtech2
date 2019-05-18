$(document).ready(function () {
    $("#templateSelection").change(function getTemplateFromDb(){
        $.ajax({
            type: 'GET',
            url: 'http://147.175.121.210:8117/webte2/uloha3/templateResource.php/templates/' + $('#templateSelection').val(),
            success: function (msg) {
                CKEDITOR.instances['mailBodyTextArea'].setData(msg);
            }
        });
    });
