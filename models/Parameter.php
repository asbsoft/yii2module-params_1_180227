<?php

namespace asb\yii2\modules\params_1_180227\models;

use asb\yii2\modules\params_1_180227\Module;

use asb\yii2\common_2_170212\models\BaseModel;
use asb\yii2\common_2_170212\behaviors\ParamsAccessBehaviour;

use Yii;

use Exception;

/**
 * @author ASB <ab2014box@gmail.com>
 */
class Parameter extends BaseModel
{
    public $param;
    public $comment;
    public $module;
    public $type;
    public $value;

    public $user;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->user = Yii::$app->user;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'param'   => Yii::t($this->tcModule, 'Parameter'),
            'comment' => Yii::t($this->tcModule, 'Comment'),
            'module'  => Yii::t($this->tcModule, 'Module'),
            'type'    => Yii::t($this->tcModule, 'Type'),
            'value'   => Yii::t($this->tcModule, 'Value'),
        ];
    }

    /**
     * Check if current user can edit this parameter
     * @return boolean
     */
    public function canUserEditParam($module)
    {
        $canEdit = false;
        try {
            $roles = $module->rolesForParam($this->param);
            foreach ($roles as $role) {
                if ($this->user->can($role)) {
                    $canEdit = true;
                    break;
                }
            }
        } catch(Exception $ex) { // module don't have params behaviors
            $defaultRole = (new ParamsAccessBehaviour)->defaultRole;
            $canEdit = $this->user->can($defaultRole);
        }    
        return $canEdit;
    }

    /**
     * Check if current user can edit this parameter
     * @return boolean
     */
    public function canUserRemoveParam($module)
    {
        $canEdit = $this->canUserEditParam($module);

        $paramsValuesModel = $this->module->model('ParamsValues');
        $hasSavedParam = $paramsValuesModel::hasSavedParam($module, $this->param);

        return $canEdit && is_scalar($this->value) && $hasSavedParam;
    }

}
