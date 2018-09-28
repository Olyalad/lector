<?php

use yii\db\Migration;

/**
 * Class m171206_104508_remove_columns_user_table
 */
class m171206_104508_remove_columns_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('user', 'password_hash');
        $this->dropColumn('user', 'password_reset_token');
        $this->dropColumn('user', 'birth_date');
        $this->dropColumn('user', 'id_gender');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->addColumn('user', 'id_gender', $this->smallInteger()->comment('Пол')->defaultValue(0)->notNull());
        $this->addColumn('user', 'birth_date', $this->date()->comment('Дата рождения'));
        $this->addColumn('user', 'password_reset_token', $this->string()->unique());
        $this->addColumn('user', 'password_hash', $this->string()->notNull());
    }


}
