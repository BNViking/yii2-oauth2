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
final class Discord extends OAuth2BaseClient
{
    /** @var string Base authorization URL  */
    public string $authAppUrl = 'https://discord.com/api/oauth2/authorize';

    /** @var string Token URL */
    public string $tokenUrl = 'https://discord.com/api/oauth2/token';

    /** @var string User url API */
    public string $userApiUrl = 'https://discord.com/api/users/@me';

    /** @var string Title */
    public string $name = 'Discord';

    public string $scope = 'identify email';

    public array $mapToUserResult = [
        'id' => 'id',
        'email' => 'email',
        'username' => 'username',
        'token' => 'access_token',
        'tokenReset' => 'refresh_token',
        'tokenType' => 'token_type',
        'tokenExpires' => 'expires_in',
    ];

}