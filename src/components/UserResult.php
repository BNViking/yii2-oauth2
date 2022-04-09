<?php

namespace bnviking\oauth2\components;

use yii\base\Component;

final class UserResult extends Component
{
    public string $id = '';
    public string $email = '';
    public string $username = '';

    public string $token = '';
    public string $tokenReset = '';
    public string $tokenType = '';

    /** @var int seconds */
    public int $tokenExpires = 0;

}