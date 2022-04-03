<?php

namespace bnviking\oauth2\components;

use yii\base\Component;

final class AuthResult extends Component
{
    public const ACTION_SIGN_IN_APP = 'action-sign-in-app';
    public const ACTION_ENTERS_SITE  = 'action-enters-site';

    /** @var string[] Errors */
    private array $errors = [];

    /** @var string Action that is currently taking place self::ACTION_... */
    private string $action = '';

    /** @var OAuth2BaseClient Auth Client data */
    private OAuth2BaseClient $client;

    /** @var UserResult User data */
    private UserResult $user;

    public function hasError(): bool
    {
        return count($this->errors)>0;
    }

    public function addError($msg): void
    {
        $this->errors[] = $msg;
    }

    public function getErrors(): array
    {
        return array_unique($this->errors);
    }

    /**
     * @return OAuth2BaseClient
     */
    public function getClient(): OAuth2BaseClient
    {
        return $this->client;
    }

    /**
     * @param OAuth2BaseClient $client
     */
    public function setClient(OAuth2BaseClient $client): void
    {
        $this->client = $client;
        $userConfig = [];
        $data = array_merge($client->getToken(),$client->getUser());
        foreach ($client->mapToUserResult as $propertyUser => $propertyData) {
            if (isset($data[$propertyData])) {
                $userConfig[$propertyUser] = $data[$propertyData];
            }
        }
        $this->user = new UserResult($userConfig);
    }

    /**
     * @return UserResult
     */
    public function getUser(): UserResult
    {
        return $this->user;
    }

    /**
     * @return string Action that is currently taking place
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action Action that is currently taking place
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }


}