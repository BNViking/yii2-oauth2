<?php

namespace bnviking\oauth2\components;

use bnviking\oauth2\exception\OAuth2Exception;
use bnviking\oauth2\Helper;
use bnviking\oauth2\OAuth2Config;
use Yii;
use yii\base\Component;
use yii\helpers\Url;

final class ClientsManager extends Component
{
    /** @var OAuth2Config|null Config => components => bnvOAuth => clients */
    public ?OAuth2Config $clientsConfig;

    /**
     * @param string $clientId
     * @return OAuth2BaseClient
     * @throws OAuth2Exception
     */
    public function getClient(string $clientId): OAuth2BaseClient
    {
        $ClientConfig = $this->clientsConfig->clients[$clientId]??null;
        if (!is_null($ClientConfig)) {
            $ClientConfig['id'] = $clientId;
            $ClientConfig['url'] = Url::to([$this->clientsConfig->authUrl, $this->clientsConfig->clientUrlName => $clientId]);
            $ClientConfig['redirectUrl'] = $ClientConfig['redirectUrl']??Url::to([$this->clientsConfig->authUrl],true);
            $ClientConfig['htmlOptions']['class'] = $ClientConfig['htmlOptions']['class'] ?? "oauth2-icon $clientId";

            try {
                /** @var OAuth2BaseClient $result */
                $result = Yii::createObject($ClientConfig);
                $result->htmlOptions['title'] = $result->name;

            } catch (\Exception $e) {
                throw new OAuth2Exception($e->getMessage()." [components => ".Helper::LOG_CATEGORY." => {$clientId}]");
            }

        }else{
            throw new OAuth2Exception("Error: client config not found! [components => ".Helper::LOG_CATEGORY." => {$clientId}]");
        }

        return $result;
    }

    /**
     * @return OAuth2BaseClient[]
     * @throws OAuth2Exception
     */
    public function getClients(): array
    {
        $result = [];
        if (!is_null($this->clientsConfig)) {
            foreach ($this->clientsConfig->clients as $clientId => $config) {
                $result[$clientId] = $this->getClient($clientId);
            }
        }

        return $result;
    }

    /**
     * @throws OAuth2Exception
     */
    public function init(): void
    {
        $this->clientsConfig = Helper::getComponentConfig();
    }

}