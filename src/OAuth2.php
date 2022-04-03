<?php

namespace bnviking\oauth2;


use bnviking\oauth2\components\AuthResult;
use bnviking\oauth2\components\ClientsManager;
use bnviking\oauth2\exception\OAuth2Exception;
use Yii;

class OAuth2
{

    public static function init(): AuthResult
    {
        $result = new AuthResult();
        $state = Helper::getRequestParam('state', '');

        try {
            if (($error = Helper::getRequestParam('error','')) !== '') {
                throw new OAuth2Exception(Helper::getRequestParam('error_description', $error));
            }

            /** @var OAuth2Config $componentConfig */
            $componentConfig = Helper::getComponentConfig();
            $clientManager = new ClientsManager();

            $clientId = ($state !== '') ? Helper::getFromCache($state, '') : Helper::getRequestParam($componentConfig->clientUrlName,'');
            $client = $clientManager->getClient($clientId);

            if (($code = Helper::getRequestParam('code','')) !== '') {
                $client->auth($code, $state);
                $result->setAction(AuthResult::ACTION_ENTERS_SITE);
                $result->setClient($client);
            }else{
                $result->setAction(AuthResult::ACTION_SIGN_IN_APP);
                $result->setClient($client);
                $urlApp = $client->getAppAuthUrl();
                Yii::$app->getResponse()->redirect($urlApp);
                return $result;
            }

        }catch (OAuth2Exception $e) {
            $result->addError($e->getMessage());
        }

        if ($result->getAction() !== AuthResult::ACTION_ENTERS_SITE) {
            Helper::removeFromCache($state);
        }

        return $result;
    }
}