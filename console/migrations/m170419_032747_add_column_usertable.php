<?php

use yii\db\Migration;

class m170419_032747_add_column_usertable extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn('user', 'surname', $this->string(255)->notNull()->comment('Фамилия'));
        $this->addColumn('user', 'firstname', $this->string(255)->notNull()->comment('Имя'));
        $this->addColumn('user', 'secname', $this->string(255)->comment('Отчество'));
        $this->addColumn('user', 'birth_date', $this->date()->comment('Дата рождения'));
        $this->addColumn('user', 'id_gender', $this->smallInteger()->comment('Пол')->defaultValue(0)->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'surname');
        $this->dropColumn('user', 'firstname');
        $this->dropColumn('user', 'secname');
        $this->dropColumn('user', 'birth_date');
        $this->dropColumn('user', 'id_gender');
    }

}
