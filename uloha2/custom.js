document.addEventListener("DOMContentLoaded", function () {

    var agree = $('.agree-with-points');
    var disAgree = $('.disagree-with-points');
    var selectedAgree = false;
    var selectedDisAgree = false;
    var finalAgreement = $(".final-agreement");


    $('.agree-with-points').click(function () {

        if (selectedAgree) {
            agree.css("color", "#4d5053");
            selectedAgree = false;
            finalAgreement.val(0);
        } else {


                finalAgreement.val(1)

            agree.css("color", "#ff2366");
            disAgree.css("color", "#4d5053");
            selectedAgree = true;
        }

    })


    $('.disagree-with-points').click(function () {
        if (selectedDisAgree) {
            disAgree.css("color", "#4d5053");
            selectedDisAgree = false;
            finalAgreement.val(0);
        } else {
            finalAgreement.val(-1);
            disAgree.css("color", "#ff2366");
            agree.css("color", "#4d5053");
            selectedDisAgree = true;
        }

    })

    $(".show-team").click(function () {
        agree.css("color", "#4d5053");
        disAgree.css("color", "#4d5053");
        selectedAgree = false;
        selectedDisAgree = false;

    })


});