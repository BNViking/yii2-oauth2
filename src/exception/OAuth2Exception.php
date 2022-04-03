<?php

namespace bnviking\oauth2\exception;

use yii\base\Exception;

class OAuth2Exception extends Exception
{

    public function getName(): string
    {
        return 'OAuth2Exception';
    }

}