<?php

namespace App\Classes\Security;

class SecurityNotifications
{
    const INCORRECT_USER_OR_PASSWORD =  [
        "data" => "Invalid username or password, try again",
        "code" => 403,
        "location" => "AUTHENTICATION"
    ];

    const EXIST_EMAIL = [
        "data" => "User with this email already exists",
        "location" => "EMAIL_REGISTRATION",
        "code" => 400
    ];

    const INCORRECT_EMAIL = [
        "data" => "Incorrect Email",
        "location" => "PASSWORD_RECOVER",
        "code" => 400
    ];

    const SEND_EMAIL_RECOVER = [
        "data" => "An mail with further instructions was sent to your email address",
        "location" => "PASSWORD_RECOVER",
        "code" => 400
    ];

    const EXIST_EMAIL_CHANGE = [
        "data" => "User with this email already exists",
        "location" => "EMAIL_CHANGE",
        "code" => 400
    ];

    const UNKNOWN_ERROR_EMAIL_CHANGE = [
        "data" => "Error",
        "location" => "EMAIL_CHANGE",
        "code" => 520
    ];

    const LESS_PASSWORD = [
        "data" => "Password must be at least 6 characters long",
        "location" => "PASSWORD_REGISTRATION",
        "code" => 400
    ];

    const PASSWORD_DO_NOT_MATCH = [
        "data" => "passwords-do-not-match",
        "location" => "PASSWORD_CHANGE",
        "code" => 400
    ];

    const CHARACTERS_LONG_6 = [
        "data" => "Password must be at least 6 characters long",
        "location" => "PASSWORD_CHANGE",
        "code" => 400
    ];



//TODO: все, что ниже, переделается под формт того, что выше
    const APPLICATION_NOT_FOUND =  [
        "data" => "Приложение не найдено",
        "code" => 403
    ];

    const INCORRECT_INCOMING_DATA =  [
        "data" => "Не верные входящие данные",
        "code" => 403
    ];

    const USER_NOT_FOUND =  [
        "data" => "Пользователь не найден",
        "code" => 403
    ];

    const INVALID_PASSWORD = [
        "data" => "Не валидный пароль",
        "code" => 400
    ];

    const INVALID_TOKEN = [
        "data" => "Не валидный токен",
        "code" => 400
    ];

    const INVALID_SESSION = [
        "data" => "Не валидная сессия",
        "code" => 400,
        'location' => 'REMOVE TOKEN'
    ];

    const INVALID_EMAIL = [
        "data" => "Не валидный e-mail",
        "code" => 400
    ];


    //TODO: Это убрать вообще отсюда
    const CONFIRM_REGISTRATION = [
        "en" => "Confirmation of registration for konstruktor.com",
        "ru" => "Подтверждение регистрации на konstruktor.com"
    ];

    const MESSAGE_CONFIRM_REGISTRATION = [
        "en" => "Go to link to confirm registration for konstruktor.com ",
        "ru" => "Пройдите по ссылке для подтверждения регистрации на konstruktor.com"
    ];

    const SENT_EMAIL_CHANGE_PASSWORD = [
        "en" => "A link has been sent to your e-mail, after which you can change the password",
        "ru" => "На Ваш e-mail отправлена ссылка, перейдя по которой вы сможете изменить пароль"
    ];

    const EMAIL_TITLE_RECOVER_PASSWORD = [
        "en" => "An mail with further instructions was sent to your email address",
        "ru" => "Восстановление пароля на konstruktor.com"
    ];

    const EMAIL_BODY_RECOVER_PASSWORD = [
        "en" => "Go to link to confirm registration for recovery password ",
        "ru" => "Пройдите по ссылке для восстановления пароля"
    ];

    const RECOVER_PASSWORD = [
        "en" => "Password changed",
        "ru" => "Пароль был изменен"
    ];


    const EMAIL_BODY_CHANGE_EMAIL = [
        "en" => "Follow the link to complete the change of e-mail",
        "ru" => "Пройдите по ссылке для завершения смены e-mail"
    ];

    const CHANGE_EMAIL = [
        "en" => "E-mail changed",
        "ru" => "E-mail был изменен"
    ];

    /**
     * @param $data
     * @return SecurityResponseObject
     */
    static function getSuccessResponse($data = null): SecurityResponseObject {
        return new SecurityResponseObject($data, 200);
    }

    /**
     * @param array $notify
     * @return SecurityResponseObject
     */
    static function getErrorResponse(array $notify): SecurityResponseObject {
        return new SecurityResponseObject($notify["data"], $notify["code"], $notify["location"]);
    }


}
