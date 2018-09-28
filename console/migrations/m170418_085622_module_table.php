<?php

use yii\db\Migration;

class m170418_085622_module_table extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->addColumn('module', 'allow',
            $this->integer(1)->notNull()->comment('Доступ к модулю. 1-всем, 0-по т. module_allow')->defaultValue(1));
        $this->addColumn('module', 'creator_id',
            $this->integer()->notNull()->comment('id создателя'));
        $this->addColumn('module', 'status', $this->smallInteger(6)->notNull()->defaultValue(1));
        $this->addColumn('module', 'created_at', $this->integer()->notNull());
        $this->addColumn('module', 'updated_at', $this->integer()->notNull());


        $this->createTable('module_allow', [
            'id' => $this->primaryKey(),
            'module_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addCommentOnTable('module_allow', 'Каким груупам открыт модуль');

        $this->createTable('module_execution', [
            'id' => $this->primaryKey(),
            'module_id' => $this->integer()->notNull()->comment('id from module'),
            'user_id' => $this->integer()->notNull(),
            'start' => $this->integer()->notNull(),
            'finish' => $this->integer(),
        ], $tableOptions);


        $this->createTable('module_execution_answers', [
            'id' => $this->primaryKey(),
            'exec_id' => $this->integer()->notNull()->comment('id from module_execution'),
            'question_id' => $this->integer()->notNull()->comment('id from questions'),
            'useranswer' => $this->integer()->notNull()->comment('пользовательский ответ'),
            'right' => $this->smallInteger(2)->notNull()->defaultValue(1)->comment('Правильный?'),
            'time' => $this->integer()->notNull(),
        ], $tableOptions);
    }


    public function safeDown()
    {
        $this->dropTable('module_execution_answers');
        $this->dropTable('module_execution');
        $this->dropTable('module_allow');
        $this->dropColumn('module', 'allow');
        $this->dropColumn('module', 'creator_id');
        $this->dropColumn('module', 'status');
        $this->dropColumn('module', 'created_at');
        $this->dropColumn('module', 'updated_at');
    }
}
