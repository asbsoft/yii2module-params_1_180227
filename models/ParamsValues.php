<?php

namespace asb\yii2\modules\params_1_180227\models;

use asb\yii2\modules\params_1_180227\Module;

use asb\yii2\common_2_170212\models\DataModel;

use Yii;

/**
 * This is the model class for table "{{%params_values}}".
 *
 * @property integer $id
 * @property string $param
 * @property string $type
 * @property string $value
 * @property integer $module_id
 *
 * @property ParamsModules $module
 */
class ParamsValues extends DataModel
{
    public $module_uid;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['module_id', 'required'],
            ['module_id', 'integer'],
            ['module_id', 'exist',
                'skipOnError' => true,
                'targetClass' => ParamsModules::className(),
                'targetAttribute' => ['module_id' => 'id'],
            ],

            ['param', 'string', 'max' => 255],
            ['param', 'required'],
            ['param', 'unique',
                'targetAttribute' => ['module_id', 'param'],
                'message' => Yii::t($this->tcModule, 'Such param already exists for this module')
            ],

            ['type', 'string', 'max' => 255],
            ['type', 'required'],

            ['value', 'string', 'max' => 255],
            ['value', 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'        => Yii::t($this->tcModule, 'ID'),
            'param'     => Yii::t($this->tcModule, 'Param'),
            'type'      => Yii::t($this->tcModule, 'Type'),
            'value'     => Yii::t($this->tcModule, 'Value'),
            'module_id' => Yii::t($this->tcModule, 'Module ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne($this->module->model('ParamsModules')->className(), ['id' => 'module_id']);
    }

    /**
     * @inheritdoc
     * @return ParamsValuesQuery the active query used by this AR class.
     */
    public static function find()
    {
        //return new ParamsValuesQuery(get_called_class());
        $module = Module::getModuleByClassname(Module::className());
        $queryModel = $module->model('ParamsValuesQuery', [get_called_class()]);
        return $queryModel;
    }

    /** Saved parameters cache */
    protected static $_params = [];
    /**
     * Correct application and modules parameters.
     */
    public static function correctParams()
    {
        $modules = [];
        $params = static::find()->all();
        foreach ($params as $param) {
            if (!isset($modules[$param->module_uid])) {
                $modules[$param->module_uid] = Module::getModuleByUniqueId($param->module_uid);
            }
            $paramValue = $param->value;
          //$paramValue = unserialize($param->value); //!!
            switch ($param->type) { // only for scalar types
                case 'float':   $paramValue = (float) $paramValue; break;
                case 'double':  $paramValue = (double) $paramValue; break;
                case 'integer': $paramValue = (int)   $paramValue; break;
                case 'boolean': $paramValue = (bool)  $paramValue; break;
              //case 'string':  $paramValue = (string)$paramValue; break;
            }
            $modules[$param->module_uid]->params[$param->param] = $paramValue;
            static::$_params[$param->module_uid][$param->param] = $paramValue;
        }
    }

    /**
     * Check if exists saved parameter for module.
     * @param yii\base\Module $module
     * @param string $paramName
     * @return boolean
     */
    public static function hasSavedParam($module, $paramName)
    {
        if (isset(static::$_params[$module->uniqueId][$paramName])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove saved parameter
     * @param string $moduleUid
     * @param string $paramName
     * @return boolean
     */
    public function removeParam($moduleUid, $paramName)
    {
        $found = static::find()
            ->where(['param' => $paramName])
            ->andWhere(['module_uid' => $moduleUid])
            ->one();
        if (!$found) {
            $this->addError('param', Yii::t($this->tcModule
              , "Parameter '{param}' for module '{muid}' not found"
              , ['param' => $paramName, 'muid' => $moduleUid]
            ));
            $result = false;
        } else {
            $found->delete();
            $countParamsForModule = static::find()
                ->where(['module_uid' => $moduleUid])
                ->count();
            if ($countParamsForModule == 0) { // no more saved params for whis module - delete
                $modelPM = $this->module->model('ParamsModules');
                $foundModule = $modelPM::find()->where(['module_uid' => $moduleUid])->one();
                if ($foundModule) {
                    $foundModule->delete();
                }
            }
            $result = true;
        }
        return $result;
    }

    /**
     * Correct POST-ed value according to type
     * @param string $value
     * @param string $type
     * @return mix corrected value or original value on error with error message in $this->errors
     */
    public function correctValue($value, $type)
    {
        $value0 = $value;
        if (gettype($value) !== $type) {
            switch ($type) {
                case 'boolean':
                    if ($value === '0' || strtolower($value) === 'false') {
                        $value = false;
                    } elseif ($value === '1' || strtolower($value) === 'true') {
                        $value = true;
                    } else {
                        $this->addError('value', Yii::t($this->tcModule, 'Error in boolean value'));
                    }
                    break;
                case 'integer':
                    if (preg_match('|^\-?[\d]+$|', $value)) {
                        $value = (integer)$value;
                    } else {
                        $this->addError('value', Yii::t($this->tcModule, 'Error in integer value'));
                    }
                    break;
                case 'float':
                case 'double':
                    if (preg_match('|^[+-]?(\d*\.)?\d+(e[-+]?\d+)?$|i', $value)) {
                        if ($type === 'float') $value = (float)$value;
                        if ($type === 'double') $value = (double)$value;
                    } else {
                        $this->addError('value', Yii::t($this->tcModule, 'Error in float value'));
                    }
                    break;
                default:
                    $this->addError('value', Yii::t($this->tcModule, "Unsupported type '{type}'", ['type' => $type]));
                    break;
            }
        }
        return $value;
    }

    /**
     * @inheritdoc
     * @return bool whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $paramsModulesModel = $this->module->model('ParamsModules');
        $foundModule = $paramsModulesModel::find()->where(['module_uid' => $this->module_uid])->one();
        if (!$foundModule) {
            $foundModule = $paramsModulesModel;
            $foundModule->module_uid = $this->module_uid;
            $foundModule->save();
        }
        $this->module_id = $foundModule->id;

        $this->value = (string)$this->value;

        $result = parent::save($runValidation, $attributeNames);
        return $result;
    }

    /**
     * @inheritdoc
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface|ActiveQuery]] instance.
     */
//*
    protected static function findByCondition($condition)
    {
        $query = parent::findByCondition($condition);
        //list($tableName, $alias) = $query->getTableNameAndAlias(); //?! private
        $alias = $query->tableAliasMain; // non-portable
        if (isset($query->where['id'])) { // monkey patch for SQL error 1052 Column 'id' in where clause is ambiguous
            $query->where["{$alias}.id"] = $query->where['id'];
            unset($query->where['id']);
        }
        return $query;
    }
/**/

}
