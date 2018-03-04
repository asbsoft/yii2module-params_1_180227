<?php

namespace asb\yii2\modules\params_1_180227\models;

use asb\yii2\modules\params_1_180227\Module;

use yii\db\ActiveQuery;
use Yii;

/**
 * This is the ActiveQuery class for [[ParamsValues]].
 *
 * @see ParamsValues
 */
class ParamsValuesQuery extends ActiveQuery
{
    public $tableAliasMain = 'main';
    public $tableAliasModules = 'mod';

    public $tablenameParamsModules;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $module = Module::getModuleByClassname(Module::className());
        $modelParamsModules = $module->model('ParamsModules');
        $this->tablenameParamsModules = $modelParamsModules->tableName();

        $this->from([$this->tableAliasMain => $module->model('ParamsValues')->tableName()]);
    }
    
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     */
    public function count($q = '*', $db = null)
    {
        $this->alias($this->tableAliasMain)
            ->leftJoin([$this->tableAliasModules => $this->tablenameParamsModules]
                , "{$this->tableAliasMain}.module_id = {$this->tableAliasModules}.id");
        return parent::count($q, $db);
    }

    /**
     * @inheritdoc
     * @return ParamsValues[]|array
     */
    public function all($db = null)
    {
        $this
            ->alias($this->tableAliasMain)
            ->leftJoin([$this->tableAliasModules => $this->tablenameParamsModules]
                , "{$this->tableAliasMain}.module_id = {$this->tableAliasModules}.id")
            ->select([
                "{$this->tableAliasMain}.*",
                "{$this->tableAliasModules}.module_uid",
            ]);
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ParamsValues|array|null
     */
    public function one($db = null)
    {
        $this
            ->alias($this->tableAliasMain)
            ->leftJoin([$this->tableAliasModules => $this->tablenameParamsModules]
                , "{$this->tableAliasMain}.module_id = {$this->tableAliasModules}.id")
            ->select([
                "{$this->tableAliasMain}.*",
                "{$this->tableAliasModules}.module_uid",
            ]);
        return parent::one($db);
    }

}
