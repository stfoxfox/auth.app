
$(document).ready(function () {

    var registrationInputs = $('form[name="registration_form"] input');

    registrationInputs.on('keyup', function () {
        var allow = checkReqFields(registrationInputs);
        if (allow) {
            $('#registration_form_submit').removeClass('btn-disable');
        } else {
            $('#registration_form_submit').addClass('btn-disable');
        }
    });

    $('.change-lang_curr').on('click', function (event) {

        event.preventDefault();

        $('.lang-block').addClass('lang-block_show');
    });

    $('.lang-block_link').on('click', function (event) {

        event.preventDefault();

        var langVal = $(this).data('val');

        $('#language-field').val(langVal);

        $('#language-form').submit();
    });

    $('body').on('click', function (event) {
        if ($(event.target).closest('.lang-block').length === 0 && !$(event.target).hasClass('change-lang_curr')) {
            $('.lang-block').removeClass('lang-block_show');
        }
    });

    $('.d-inline-block').popover('show');

});

