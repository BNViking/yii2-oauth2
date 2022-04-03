<?php

namespace bnviking\oauth2;


use yii\base\Component;

class OAuth2Config extends Component
{
    public string $clientUrlName = 'client';
    public string $authUrl = 'oauth2/index';

    public array $clients = [];

}