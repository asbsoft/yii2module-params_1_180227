<?php

use yii\db\Migration;

use asb\yii2\modules\params_1_180227\models\ParamsModules as Model;

/**
 * Handles the creation of table `params_modules`.
 */
class m180228_180537_create_params_modules_table extends Migration
{
    protected $tableName;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->tableName = Model::tableName();
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'module_uid' => $this->string()->notNull()->defaultValue('')->unique(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
