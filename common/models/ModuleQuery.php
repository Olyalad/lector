<?php

namespace common\models;

use Yii;

/**
 * This is the ActiveQuery class for [[Module]].
 *
 * @see Module
 */
class ModuleQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Module[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Module|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }


    /**
     * @param int $active
     * @return $this
     */
    public function active($active = Module::STATUS_ACTIVE)
    {
        return $this->andWhere(['module.status' => $active]);
    }

    /**
     * Запрос проверяет роли пользователя.
     * Для преподавателей, админов запрос не выполняется.
     * Если это студент, то ему показываются модули доступные всем или доступные его группе
     *  (используется подзапрос).
     * Если у студента нет группы, то показываются только доступные всем модули.
     * @return $this
     */
    public function allow()
    {
        if (Yii::$app->user->can('student') && !Yii::$app->user->can('createModule')) {
            $userGroup = User::getStudentGroup(Yii::$app->user->id);
            if ($userGroup) {
                $subquery = Module::find()->select('module.id')->active()->joinWith('groups')->andWhere(['group_id' => $userGroup->id]);
                return $this->joinWith('groups')->andWhere(['or', ['allow' => Module::ALLOW_ALL], ['module.id' => $subquery]]);
            } else {
                return $this->andWhere(['allow' => Module::ALLOW_ALL]);
            }
        }
        return $this;
    }

    public function hideEmptyModules()
    {
        $subquery = Pages::find()->select('module_id')->distinct();
        return $this->andWhere(['in', 'module.id', $subquery]);
    }

}
