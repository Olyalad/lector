<?php

use yii\db\Migration;

class m170502_063939_add_column_module extends Migration
{

    public function safeUp()
    {
        $this->addColumn('module', 'sort_order', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('module', 'sort_order');
    }
}
