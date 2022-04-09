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
final class Twitch extends OAuth2BaseClient
{
    /** @var string Base authorization URL  */
    public string $authAppUrl = 'https://id.twitch.tv/oauth2/authorize';

    /** @var string Token URL */
    public string $tokenUrl = 'https://id.twitch.tv/oauth2/token';

    /** @var string User url API */
    public string $userApiUrl = 'https://api.twitch.tv/helix/users';

    /** @var string Title */
    public string $name = 'Twitch';

    public string $scope = 'user:read:email channel:moderate chat:edit chat:read whispers:read whispers:edit channel:manage:polls channel:manage:predictions channel:read:goals channel:read:polls channel:read:predictions moderator:manage:banned_users';

    public array $mapToUserResult = [
        'id' => 'id',
        'email' => 'email',
        'username' => 'login',
        'token' => 'access_token',
        'tokenReset' => 'refresh_token',
        'tokenType' => 'token_type',
        'tokenExpires' => 'expires_in',
    ];

    /**
     * @throws \bnviking\oauth2\exception\OAuth2Exception
     */
    public function beforeRequestUserData(array &$config, array &$data): void
    {
        $configPreAuth = [
            'baseUrl' => 'https://id.twitch.tv/oauth2/userinfo',
            'method' => 'GET',
            'removeHeaders' => true,
            'timeout' => 30,
            'sslVerifyPeer' => false,
            'headers'=>[
                'Authorization'=>"{$this->token['token_type']} {$this->token['access_token']}",
            ]
        ];

        $PreAuth = $this->getResponseData($configPreAuth,[]);
        unset($config['headers']['Content-Type'], $config['headers']['Accept']);
        $data = ['login'=>$PreAuth['preferred_username']];
        $config['headers']['Client-ID'] = $this->clientID;

    }

    public function afterSetUserData(array &$user): void
    {
        $user = $user['data'][0]??[];
    }
}