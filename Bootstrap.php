<?php

namespace asb\yii2\modules\params_1_180227;

use asb\yii2\modules\params_1_180227\models\ParamsValues;

use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * @author ASB <ab2014box@gmail.com>
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add to any standard yii\base\Module dynamic submodules from Modules manager and bootstrap them.
        $app->on(Application::EVENT_BEFORE_REQUEST, function($event) use($app) {
            ParamsValues::correctParams();
        });
    }

}
