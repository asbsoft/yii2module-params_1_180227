<?php

namespace asb\yii2\modules\params_1_180227\models;

use asb\yii2\modules\params_1_180227\Module;

use asb\yii2\common_2_170212\i18n\TranslationsBuilder;

use yii\data\ArrayDataProvider;
use Yii;

/**
 * @author ASB <ab2014box@gmail.com>
 */
class ParamsDataProvider extends ArrayDataProvider
{
    public $isScalar;
    /**
     * Constructor.
     * @param yii\base\Module $module
     * @param boolean $scalar show scalar parameters only
     * @param array $config Yii2-object config
     */
    public function __construct($module, $scalar, $config = [])
    {
        $this->isScalar = $scalar;

        $this->key = 'param'; // column that is used as the key of the data models
        $this->modelClass = Parameter::className(); // property is used to get columns' names

        $thisModule = Module::getModuleByClassname(Module::className());

        // prepare translation for parameters' comment
        $tcCat = TranslationsBuilder::getBaseTransCategory($module);
        $tcCat = rtrim($tcCat, '/');
        $tcParams = $tcCat . '/params'; // find translation for module in its messages LANG/params.php file

        $this->allModels = [];
        foreach ($module->params as $param => $value) {
            // force translation for default en-version
            $trans = Yii::$app->i18n->translations; //?! work inside loop only
            if (isset($trans[$tcParams])) {
                if (is_array($trans[$tcParams])) {
                    $trans[$tcParams]['forceTranslation'] = true;
                } else {
                    $trans[$tcParams]->forceTranslation = true;
                }
            }

            // find comment for parameter
            $comment = Yii::t($tcParams, $param);
            if ($comment == $param) { // no translation - nothing show
                $comment = '';
            }

            if (!$scalar || is_scalar($value)) {
                 $model = $thisModule->model('Parameter', [[
                     'param'   => $param,
                     'comment' => $comment,
                     'module'  => $module->uniqueId,
                     'type'    => gettype($value),
                     'value'   => $value,
                 ]]);
                 $this->allModels[] = $model;
            }
        }

        parent::__construct($config);
    }

}
