<?php

namespace common\models;

use Yii;

/**
 * This is the ActiveQuery class for [[ModuleExecution]].
 *
 * @see ModuleExecution
 */
class ModuleExecutionQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return ModuleExecution[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ModuleExecution|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }


    /**
     * @param bool $finished
     * @return $this
     */
    public function finished($finished = true)
    {
        if ($finished)
            return $this->andWhere('finish');
        return $this->andWhere(['finish' => null]);
    }
}
