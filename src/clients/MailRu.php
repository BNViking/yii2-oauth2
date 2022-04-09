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
final class MailRu extends OAuth2BaseClient
{
    /** @var string Base authorization URL  */
    public string $authAppUrl = 'https://oauth.mail.ru/login';

    /** @var string Token URL */
    public string $tokenUrl = 'https://oauth.mail.ru/token';

    /** @var string User url API */
    public string $userApiUrl = 'https://oauth.mail.ru/userinfo';

    /** @var string Title */
    public string $name = 'MailRu';

    /** @var string List of all the OAuth2 scopes */
    public string $scope = 'userinfo';

    public array $mapToUserResult = [
        'id' => 'id',
        'email' => 'email',
        'username' => 'first_name',
        'token' => 'access_token',
        'tokenReset' => 'refresh_token',
        'tokenType' => 'token_type',
        'tokenExpires' => 'expires_in',
    ];

    public function beforeRequestUserData(array &$config, array &$data): void
    {
        $token = $this->getToken();
        $data['access_token'] = $token['access_token'];
    }
}