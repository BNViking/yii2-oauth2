<?php

namespace bnviking\oauth2\components;


use bnviking\oauth2\exception\OAuth2Exception;
use bnviking\oauth2\Helper;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Request;

abstract class OAuth2BaseClient extends Component
{
    /** @var string Base authorization URL  */
    public string $authAppUrl = '';

    /** @var string Token URL */
    public string $tokenUrl = '';

    /** @var string User url API */
    public string $userApiUrl = '';

    /** @var string Client ID */
    public string $clientID = '';

    /** @var string Client secret */
    public string $clientSecret = '';

    /** @var string Redirect url */
    public string $redirectUrl = '';

    /** @var string List of all the OAuth2 scopes */
    public string $scope = '';

    /** @var string Id tag */
    public string $id = '';

    /** @var string Title */
    public string $name = '';

    /** @var array Html options for tag */
    public array $htmlOptions = [];

    /** @var string|null Url auth */
    public ?string $url;

    /** @var string|null Name your app */
    public ?string $appName;

    /** @var string Format response API */
    public string $formatApi = '';

    /** @var string Format request for config http client */
    public string $formatRequestClient = Client::FORMAT_URLENCODED;

    /** @var string Version Api */
    public string $versionApi = '';

    /** @var array Return token API */
    private array $token = [];

    /** @var array Map for UserResult property [UserResult Attr]=>[Client->token|Client->user Attr] */
    public array $mapToUserResult = [];

    /** @var array Return user info API */
    private array $user = [];

    public function init(): void
    {
        $this->appName = $this->appName??Yii::$app->name;
    }

    /**
     * @param string $code
     * @param string $state
     * @throws OAuth2Exception
     */
    public function auth(string $code, string $state): void
    {
        $ClientId = Helper::getFromCache($state,'');
        if ($ClientId !== $this->id) {
            throw new OAuth2Exception("Error state");
        }

        $this->setTokenData($code);
        $this->setUserData();
    }

    /**
     * @return string Url auth in App
     */
    public function getAppAuthUrl(): string
    {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientID,
            'redirect_uri' => $this->redirectUrl,
        ];

        if ($this->scope !== '') {
            $params['scope'] = $this->scope;
        }

        $this->updateAppAuthUrlParams($params);

        $state = hash('sha256', uniqid(date('d.m.Y').'-'.time(), true));
        $params['state'] = $state;
        Helper::setToCache($state, $this->id);

        return $this->authAppUrl.'?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @param array $config
     * @param array $data
     * @return Request
     * @throws InvalidConfigException
     */
    private function createRequestApi(array $config, array $data): Request
    {
        $client = new Client([
            'baseUrl' => $config['baseUrl'],
            'requestConfig' => [
                'format' => $this->formatRequestClient
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ]
        ]);

        $request = $client->createRequest()
            ->setMethod($config['method'])
            ->setData($data);

        if (isset($config['removeHeaders']) && $config['removeHeaders'] === true) {
            $request->getHeaders()->removeAll();
        }

        if (isset($config['headers'])) {
            foreach ($config['headers'] as $name => $value) {
                $request->getHeaders()->add($name,$value);
            }
        }

        return $request;
    }

    /**
     * @throws OAuth2Exception
     */
    private function getResponseData(array $config, array $data): array
    {
        try {
            $request = $this->createRequestApi($config, $data);
            $response = $request->send();
            $result = Json::decode($response->getContent());
        } catch (InvalidConfigException | \Exception $e) {
            throw new OAuth2Exception($e->getMessage());
        }

        return $result;
    }

    /**
     * @throws OAuth2Exception
     */
    private function setTokenData($code): void
    {
        $config = [
            'baseUrl' => $this->tokenUrl,
            'url' => '',
            'method' => 'POST',
            'removeHeaders' => true,
            'timeout' => 30,
            'sslVerifyPeer' => false,
            'headers'=>[
                'User-Agent' => $this->appName,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ]
        ];

        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientID,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUrl,
            'code' => $code,
        ];

        $this->beforeRequestToken($config,$data);
        $this->token = $this->getResponseData($config,$data);

        if (!isset($this->token['access_token'])) {
            $message = $this->token['error_description']??$this->token['message']??'not message';
            throw new OAuth2Exception("Get token data error {$this->token['error']} {$message}");
        }

        $this->afterSetToken($this->token);
    }

    /**
     * @throws OAuth2Exception
     */
    private function setUserData(): void
    {
        $config = [
            'baseUrl' => $this->userApiUrl,
            'method' => 'GET',
            'removeHeaders' => true,
            'timeout' => 30,
            'sslVerifyPeer' => false,
            'headers'=>[
                'Content-Type'=>'application/x-www-form-urlencoded',
                'Accept'=>'application/json',
            ]
        ];

        if (isset($this->token['token_type'])) {
            $config['headers']['Authorization'] = "{$this->token['token_type']} {$this->token['access_token']}";
        }

        $data = [
            'client_id' => $this->clientID,
            'access_token' => $this->token['access_token']
        ];

        $this->beforeRequestUserData($config,$data);
        $this->user = $this->getResponseData($config,$data);
        $this->afterSetUserData($this->user);
    }

    public function updateAppAuthUrlParams(array &$params): void {}
    public function beforeRequestToken(array &$config, array &$data): void {}
    public function beforeRequestUserData(array &$config, array &$data): void {}
    public function afterSetToken(array &$token): void {}
    public function afterSetUserData(array &$user): void {}

    /**
     * @return array
     */
    public function getToken(): array
    {
        return $this->token;
    }

    /**
     * @return array
     */
    public function getUser(): array
    {
        return $this->user;
    }

}