<p align="center">
    <a href="https://bnv.one" target="_blank">
        <img src="https://avatars.githubusercontent.com/u/12669446?s=400&u=d883b62c0adcae00380135155c820b5d928224dc&v=4" height="100px">
    </a>
    <h2 align="center">OAuth2.0</h1>
    <h4 align="center">Extension for Yii2 framework</h3>
    <p align="center">
        <img src="./widget.png" alt="Widget OAuth2.0" height="48">
    </p>
</p>
<hr>

The following clients are currently supported for authorization:

 * vk.com [[register your application](https://vk.com/apps?act=manage)]
 * mail.ru [[register your application](https://o2.mail.ru/app/)]
 * yandex.ru [[register your application](https://oauth.yandex.ru/)]
 * discord.com [[register your application](https://discord.com/developers/applications)]
 * trovo.live [[register your application](https://developer.trovo.live/)]
 * twitch.tv [[register your application](https://dev.twitch.tv/console)]

Installation
------------

The preferred way to install this extension is through [composer] (http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist bnviking/yii2-oauth2 "~v1.1.0"
```

or add

```
"bnviking/yii2-oauth2": "~v1.0.0"
```

to the require section of your `composer.json` file.

Config
-----

```php
'components' => [
    ...
    'bnvOAuth2'=> [
        'class' => \bnviking\oauth2\OAuth2Config::class,
        'clientUrlName' => 'client',
        'authUrl' => 'auth2/authorize', 
        'clients' => [
            'discord'=>[
                'class'=> \bnviking\oauth2\clients\Discord::class,
                'clientID' => 'discord_client_id',
                'clientSecret' => 'discord_client_secret',
            ],
            'vkontakte'=>[
                'class'=> \bnviking\oauth2\clients\VKontakte::class,
                'clientID' => 'vkontakte_client_id',
                'clientSecret' => 'vkontakte_client_secret',
            ],
            'yandex'=>[
                'class'=> \bnviking\oauth2\clients\Yandex::class,
                'clientID' => 'yandex_client_id',
                'clientSecret' => 'yandex_client_secret',
            ],
            'mailru'=>[
                'class'=> \bnviking\oauth2\clients\MailRu::class,
                'clientID' => 'mailru_client_id',
                'clientSecret' => 'mailru_client_secret',
            ],
            'trovo'=>[
                'class'=> \bnviking\oauth2\clients\Trovo::class,
                'clientID' => 'trovo_client_id',
                'clientSecret' => 'trovo_client_secret',
            ],
            'twitch'=>[
                'class'=> \bnviking\oauth2\clients\Twitch::class,
                'clientID' => 'twitch_client_id',
                'clientSecret' => 'twitch_client_secret',
            ],
        ]
    ]
    ...
]

```

Create link to OAuth2
---------------------
> * clientUrlName - name param Client ID for create URL
> * authUrl - `<controller>`/`<action>`
>
Redirect URL for your APP: _https://my.site/controller/action_

Use in action
-------------

```php
<?php

namespace app\controllers;

use bnviking\oauth2\OAuth2;
use yii\web\Controller;
use Yii;

class Auth2Controller extends Controller
{
    public function actionAuthorize(): string
    {
        /** @var \bnviking\oauth2\components\AuthResult $authResult */
        $authResult = OAuth2::init();

        if ($authResult->hasError()) {
            $errors = $authResult->getErrors();
            Yii::$app->session->setFlash('error', implode('<br>', $errors));
            $this->goHome();
            return '';
        }

        if ($authResult->getAction() === AuthResult::ACTION_ENTERS_SITE) {
            /** @var \bnviking\oauth2\components\OAuth2BaseClient $clientData Auth client data */
            $clientData = $authResult->getClient();
            /** @var \bnviking\oauth2\components\UserResult $userData User data */
            $userData = $authResult->getUser();
            /*
             * property $userData:
             *   id - User ID
             *   mail - User email
             *   username - User name
             *   token - Auth token
             *   tokenReset - Reset token
             *   tokenType - Token Type
             *   tokenExpires - Token lifetime [seconds]
            */

            /*
             * Here you can register or enter the site
             */

        }
        ...
    }
}

```

Widget
------

```php
<?=\bnviking\oauth2\widgets\OAuth2Buttons::widget()?>
```

<p align="center">
    <img src="./widget.png" alt="Widget OAuth2.0 example">
</p>

###### Additional html options for widget

```php
'components' => [
    ...
     'bnvOAuth2'=> [
        'clients' => [
            ...
            'htmlOptions' => ['class'=>'my-css-class']
        ]
     ]
]
```

Custom widget example
---------------------
>All clients can be retrieved using the ClientManager
>

`widget.php`

```php
<?php
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
            /** @var \bnviking\oauth2\components\OAuth2BaseClient[] $clients Auth client data */
            $clients = $clientManager->getClients();
        } catch (OAuth2Exception $e) {
            $error = $e->getMessage();
        }

        return $this->render('view-widget',['clients' => $clients,'error' => $error]);
    }

}
```

`view-widget.php`

```php
<?php
/* @var yii\web\View $this */
/* @var OAuth2BaseClient[] $clients */
/* @var string $error */

use bnviking\oauth2\components\OAuth2BaseClient;
use bnviking\oauth2\exception\OAuth2Exception;
use yii\helpers\Html;

\bnviking\oauth2\OAuth2Bundle::register($this);
?>
<div class="row">
    <div class="col-lg-12 text-center">
        <?php
            if ($error!== '') {
                echo Html::tag('div',$error,['class'=>'alert alert-danger','role'=>'alert']);
            }
            echo Html::beginTag('div',['class'=>'btn-group']);
                foreach ($clients as $clientId => $client) {
                    echo Html::a('', $client->url, $client->htmlOptions);
                }
            echo Html::endTag('div');
        ?>
    </div>
</div>
```