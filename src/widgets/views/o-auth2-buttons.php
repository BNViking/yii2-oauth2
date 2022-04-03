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
