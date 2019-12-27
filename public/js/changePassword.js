
$(document).ready(function () {

    var passwordInputs = $('form[name="change_password_form"] input');

    passwordInputs.on('keyup', function () {
        var allow = checkReqFields(passwordInputs);
        var submitField = $('#change_password_form_submit');

        if (allow) {
            submitField.removeAttr('disabled');
            submitField.removeClass('disabled');
        } else {
            submitField.attr('disabled');
            submitField.addClass('disabled');
        }
    });

    $('.d-inline-block').popover('show');

});

