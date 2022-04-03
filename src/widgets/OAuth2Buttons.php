<?php
namespace bnviking\oauth2\widgets;


use bnviking\oauth2\components\ClientsManager;
use bnviking\oauth2\exception\OAuth2Exception;
use yii\base\Widget;

class OAuth2Buttons extends Widget
{
    public function run(): string
    {
        $error = '';
        $clients = [];
        try {
            $clientManager = new ClientsManager();
            $clients = $clientManager->getClients();
        } catch (OAuth2Exception $e) {
            $error = $e->getMessage();
        }

        return $this->render('o-auth2-buttons',['clients' => $clients,'error' => $error]);
    }

}