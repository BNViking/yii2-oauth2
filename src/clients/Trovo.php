<?php
namespace bnviking\oauth2\clients;


use bnviking\oauth2\components\OAuth2BaseClient;
use yii\httpclient\Client;

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

final class Trovo extends OAuth2BaseClient
{
    /** @var string Base authorization URL  */
    public string $authAppUrl = 'https://open.trovo.live/page/login.html';

    /** @var string Token URL */
    public string $tokenUrl = 'https://open-api.trovo.live/openplatform/exchangetoken';

    /** @var string User url API */
    public string $userApiUrl = 'https://open-api.trovo.live/openplatform/getuserinfo';

    /** @var string Title */
    public string $name = 'Trovo';

    public string $scope = 'channel_update_self+user_details_self';

    /** @var string Format request for config http client */
    public string $formatRequestClient = Client::FORMAT_JSON;

    public array $mapToUserResult = [
        'id' => 'userId',
        'mail' => 'email',
        'username' => 'userName',
        'token' => 'access_token',
        'tokenReset' => 'refresh_token',
        'tokenType' => 'token_type',
        'tokenExpires' => 'expires_in',
    ];

    public function beforeRequestUserData(array &$config, array &$data): void
    {
        $config['headers']['Content-Type'] = 'application/json';
        $config['headers']['Client-ID'] = $this->clientID;
        unset($data['client_id']);
    }

    public function beforeRequestToken(array &$config, array &$data): void
    {
        $config['headers']['Content-Type'] = 'application/json';
        $config['headers']['Client-ID'] = $this->clientID;
        unset($data['client_id']);
    }

    /**
     * @param array $token
     */
    public function afterSetToken(array &$token): void
    {
        /* todo: Bug Trovo return Bearer but use OAuth  */
        $token['token_type'] = 'OAuth';
    }
}