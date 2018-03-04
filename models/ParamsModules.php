<?php

namespace asb\yii2\modules\params_1_180227\models;

use asb\yii2\common_2_170212\models\DataModel;

use Yii;

/**
 * This is the model class for table "{{%params_modules}}".
 *
 * @property integer $id
 * @property string $module_uid
 *
 * @property ParamsValues[] $paramsValues
 */
class ParamsModules extends DataModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_uid'], 'string', 'max' => 255],
            [['module_uid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t($this->tcModule, 'ID'),
            'module_uid' => Yii::t($this->tcModule, 'Module unique ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParamsValues()
    {
        return $this->hasMany($this->module->model('ParamsValues')->className(), [
            'module_id' => 'id'
        ]);
    }

}
