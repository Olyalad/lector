<?php

use yii\db\Migration;

class m170421_025310_add_column_pages extends Migration
{
    public function safeUp()
    {
        $this->addColumn('pages', 'js_code', $this->text()->comment('Код JavaScript'));
        $this->addColumn('pages', 'css_code', $this->text()->comment('Код Css'));
    }

    public function safeDown()
    {
        $this->dropColumn('pages', 'js_code');
        $this->dropColumn('pages', 'css_code');
    }
}
