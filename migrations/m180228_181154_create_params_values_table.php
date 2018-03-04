<?php

use yii\db\Migration;

use asb\yii2\modules\params_1_180227\models\ParamsValues as Model;
use asb\yii2\modules\params_1_180227\models\ParamsModules as JoinedModel;

/**
 * Handles the creation of table `params_values`.
 * Has foreign keys to the tables:
 *
 * - `params_modules`
 */
class m180228_181154_create_params_values_table extends Migration
{
    protected $tableName;
    protected $baseTableName;
    protected $joinedTableName;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->tableName = Model::tableName();
        $this->baseTableName = Model::baseTableName();
        $this->joinedTableName = JoinedModel::tableName();
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'param' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'value' => $this->string()->notNull(),
            'module_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `module_id`
        $this->createIndex(
            "idx-{$this->baseTableName}-module_id",
            $this->tableName,
            'module_id'
        );

        if ($this->db->driverName !== 'sqlite') {
            $this->addForeignKey(
                "fk-{$this->baseTableName}-module_id",
                $this->tableName, 'module_id',
                $this->joinedTableName, 'id',
                'CASCADE'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if ($this->db->driverName !== 'sqlite') {
            $this->dropForeignKey(
                "fk-{$this->baseTableName}-module_id",
                $this->tableName
            );
        }

        $this->dropIndex(
            "idx-{$this->baseTableName}-module_id",
            $this->tableName
        );

        $this->dropTable($this->tableName);
    }

}
