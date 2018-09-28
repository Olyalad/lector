<?php

use yii\db\Migration;

class m170417_034406_create_module_tables extends Migration
{
     // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('module', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Название модуля'),
        ], $tableOptions);

        $this->createTable('pages', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Название страницы'),
            'text' => $this->text()->notNull()->comment('Текст страницы'),
            'image' => $this->string(255)->notNull()->comment('Картинка'),
            'module_id' => $this->integer()->notNull()->comment('id from `module`'),
            'sort_order' => $this->integer(5)->notNull()->comment('Порядок слайда'),
        ], $tableOptions);

        $this->createTable('questions', [
            'id' => $this->primaryKey(),
            'page_id' => $this->integer()->notNull()->comment('id from `pages`'),
            'question' => $this->string(255)->notNull()->comment('Вопрос'),
            'answer_id' => $this->integer()->notNull()->comment('right id from `answers`'),
        ], $tableOptions);

        $this->createTable('answers', [
            'id' => $this->primaryKey(),
            'question_id' => $this->integer()->notNull()->comment('id from `questions`'),
            'answer' => $this->string(255)->notNull()->comment('Ответ'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('answers');
        $this->dropTable('questions');
        $this->dropTable('pages');
        $this->dropTable('module');
    }

}
