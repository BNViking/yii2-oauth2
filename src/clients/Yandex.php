<?php
namespace bnviking\oauth2\clients;


use bnviking\oauth2\components\OAuth2BaseClient;

/**
 * @property string $userApiUrl
 * @property string $authAppUrl
 * @property string $tokenUrl
 * @property string $userInfoUrl
 * @property string $clientID
 * @property string $clientSecret
 * @property string $redirectUrl
 * @property string $scope
 * @property string $name
 * @property string $htmlOptions
 */
final class Yandex extends OAuth2BaseClient
{
    /** @var string Base authorization URL  */
    public string $authAppUrl = 'https://oauth.yandex.ru/authorize';

    /** @var string Token URL */
    public string $tokenUrl = 'https://oauth.yandex.ru/token';

    /** @var string Base url API */
    public string $userApiUrl = 'https://login.yandex.ru/info';

    /** @var string Title */
    public string $name = 'Yandex';

    /** @var string Format response API */
    public string $formatApi = 'json';

    public array $mapToUserResult = [
        'id' => 'id',
        'email' => 'default_email',
        'username' => 'login',
        'token' => 'access_token',
        'tokenReset' => 'refresh_token',
        'tokenType' => 'token_type',
        'tokenExpires' => 'expires_in',
    ];
}