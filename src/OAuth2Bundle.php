<?php

namespace bnviking\oauth2;


use yii\web\AssetBundle;

class OAuth2Bundle extends AssetBundle
{
    public $sourcePath = __DIR__.'/assets';

    public $js = [
    ];

    public $publishOptions = [
        'forceCopy' => true,
    ];
    
    public function init(): void
    {
        parent::init();
        $this->css[] = './css/oauth2.css';
    }

}