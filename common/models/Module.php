<?php

namespace common\models;

use himiklab\sortablegrid\SortableGridBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "module".
 *
 * @property integer $id
 * @property string $name
 * @property integer $allow
 * @property integer $creator_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Module extends \yii\db\ActiveRecord
{
    const STATUS_NO_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const ALLOW_ALL = 1;
    const ALLOW_GROUPS = 0;

    public $groupsId = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'module';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'creator_id',
                'updatedByAttribute' => false,
            ],
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort_order'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'allow'], 'required'],
            [['allow', 'creator_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            ['allow', 'default', 'value' => self::ALLOW_ALL],
            ['allow', 'in', 'range' => [self::ALLOW_ALL, self::ALLOW_GROUPS]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_NO_ACTIVE]],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'allow' => 'Доступен всем',
            'allowName' => 'Доступ',
            'groupsId' => 'Группы',
            'creator_id' => 'Создатель',
            'creator' => 'Создатель',
            'status' => 'Активный',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'pagesCount' => 'Количество страниц'
        ];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->allow == self::ALLOW_GROUPS && !Yii::$app->request->post('Module')['groupsId']) {
                $this->addError('groupsId', 'Вы не выбрали группы');
                return false;
            }
            return true;
        }
        return false;
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->allow == self::ALLOW_GROUPS) {
            if ($groups = Yii::$app->request->post('Module')['groupsId']) {
                $this->unlinkAll('groups', true);
                foreach ($groups as $groupId) {
                    $group = Groups::findOne($groupId);
                    $this->link('groups', $group);
                }
            }
        } elseif ($this->allow == self::ALLOW_ALL) {
            $this->unlinkAll('groups', true);
        }

    }

//    public function delete()
//    {
//        $this->status = self::STATUS_DELETED;
//        $this->save(false);
//    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            foreach ($this->pages as $page) {
                foreach ($page->questions as $question) {
                    $question->unlinkAll('answers', true);
                }
                $page->unlinkAll('questions', true);
            }
            $this->unlinkAll('pages', true);

            $this->unlinkAll('groups', true);
            return true;
        }
        return false;
    }


    /**
     * Создатель модуля
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }


    /**
     * Группы, которым открыт модуль
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Groups::className(), ['id' => 'group_id'])
            ->viaTable('module_allow', ['module_id' => 'id']);
    }


    /**
     * Слайды модуля
     * @return \yii\db\ActiveQuery
     */
    public function getPages()
    {
        return $this->hasMany(Pages::className(), ['module_id' => 'id'])->orderBy('sort_order');
    }


    /**
     * Количество слайдов модуля
     * @return int
     */
    public function getPagesCount()
    {
        return $this->getPages()->count();
    }


    /**
     * Список пользователей, прошедших модуль
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable('module_execution', ['module_id' => 'id']);
    }


    /**
     * Все запуски модуля
     * @return \yii\db\ActiveQuery
     */
    public function getExecutions()
    {
        return $this->hasMany(ModuleExecution::className(), ['module_id' => 'id']);
    }


    /**
     * Прошел текущий пользователь модуль или нет
     * @return bool
     */
    public function complete()
    {
        if (ModuleExecution::find()
            ->where(['module_id' => $this->id, 'user_id' => \Yii::$app->user->id])
            ->andWhere(['is not', 'finish', null])
            ->one()
        )
            return true;
        return false;
    }


    /**
     * Доступность модуля (доступен всем | группы)
     * @return string
     */
    public function getAllowName()
    {
        if ($this->allow == self::ALLOW_ALL) {
            return 'Доступен всем';
        } elseif ($this->allow == self::ALLOW_GROUPS) {
            $return = [];
            if ($groups = $this->groups) {
                foreach ($groups as $grp) {
                    $return[] = $grp->name;
                }
            }
            return implode(', ', $return);
        }
    }


    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_NO_ACTIVE => 'Не активен',
        ];
    }


    public function getStatusName()
    {
        if ($this->status == self::STATUS_ACTIVE)
            return 'Активен';
        elseif ($this->status == self::STATUS_NO_ACTIVE)
            return 'Не активен';
    }


    /**
     * @inheritdoc
     * @return ModuleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ModuleQuery(get_called_class());
    }
}
