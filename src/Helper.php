<?php
namespace bnviking\oauth2;

use bnviking\oauth2\exception\OAuth2Exception;
use Yii;
use yii\base\InvalidConfigException;

class Helper
{
    public const LOG_CATEGORY = 'bnvOAuth2';
    public const COMPONENT_NAME = 'bnvOAuth2';

    /**
     * @throws OAuth2Exception
     */
    public static function getComponentConfig(): ?OAuth2Config
    {
        try {
            $result = Yii::$app->get(self::COMPONENT_NAME);
        } catch (InvalidConfigException $e) {
            throw new OAuth2Exception($e->getMessage());
        }
        return $result;
    }

    public static function getRequestParam(string $paramName, mixed $defaultValue): mixed
    {
        return Yii::$app->getRequest()->getQueryParam($paramName,$defaultValue);
    }

    public static function setToCache(string $key,$value): void
    {
        Yii::$app->getCache()->set($key,$value,60);
    }

    public static function getFromCache(string $key, $defaultValue): mixed
    {
        return Yii::$app->getCache()->get($key)??$defaultValue;
    }

    public static function removeFromCache(string $key): mixed
    {
        return Yii::$app->getCache()->delete($key);
    }
}