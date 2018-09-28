<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "module_execution".
 *
 * @property integer $id
 * @property integer $module_id
 * @property integer $user_id
 * @property integer $start
 * @property integer $finish
 */
class ModuleExecution extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'module_execution';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'start',
                'updatedAtAttribute' => false,
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id'], 'required'],
            [['module_id', 'user_id', 'start', 'finish'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_id' => 'Модуль',
            'user_id' => 'Студент',
            'user' => 'Студент',
            'user_group' => 'Группа',
            'start' => 'Start',
            'finish' => 'Дата прохождения',
            'timeSpent' => 'Затраченное время',
            'wrongAnswers' => 'Количество ошибочных ответов',
        ];
    }


    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->unlinkAll('answers', true);
            return true;
        }
        return false;
    }


    /**
     * Модуль
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }


    /**
     * Все ответы пользователя
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(ModuleExecutionAnswers::className(), ['exec_id' => 'id']);
    }


    /**
     * Пользователь, прошедший модуль
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    /**
     * Затраченное время
     * @return string
     */
    public function getTimeSpent()
    {
        if ($this->finish) {
            $date1 = new \DateTime();
            $date1->setTimestamp($this->start);
            $date2 = new \DateTime();
            $date2->setTimestamp($this->finish);

            $interval = date_diff($date2, $date1);

            return $interval->format('%H:%I:%S');
        }
    }


    /**
     * Количество ошибочных ответов
     * @return int
     */
    public function getWrongAnswers()
    {
        return $this->getAnswers()->andWhere(['right' => ModuleExecutionAnswers::ANSWER_WRONG])->count();
    }

    /**
     * Страницы с ошибками
     * @return int
     */
    public function getPagesWithWrongAnswers()
    {
        $answers = $this->getAnswers()->andWhere(['right' => ModuleExecutionAnswers::ANSWER_WRONG])->all();

        $return = [];
        foreach ($answers as $answer) {
            $page = $answer->page;
            $return[] = $page->getNumberPage() + 1;
        }

        return implode('; ', $return);
    }


    /**
     * Проверка и сохранение пользовательского ответа
     * @param int $questionId
     * @param int $userAnswer
     * @return bool - правильность ответа
     */
    public function setAnswer($questionId, $userAnswer)
    {
        $question = Questions::findOne($questionId);

        $modelAnswer = new ModuleExecutionAnswers();
        $modelAnswer->attributes = [
            'question_id' => $question->id,
            'useranswer' => $userAnswer,
        ];

        if ($question->answer_id == $userAnswer) {
            $modelAnswer->right = ModuleExecutionAnswers::ANSWER_RIGHT;
            $this->link('answers', $modelAnswer);
            return true;
        } else {
            $modelAnswer->right = ModuleExecutionAnswers::ANSWER_WRONG;
            $this->link('answers', $modelAnswer);
            return false;
        }
    }


    /**
     * Проверка, что студент ответил на все вопросы
     * @return bool|int - number of page
     */
    public function checkAllDone()
    {
        //получить страницы, на вопросы которых ответил пользователь
        $countAnswers = $this->getAnswers()
            ->leftJoin('questions', 'question_id = questions.id')
            ->select('page_id')->distinct()
            ->andWhere(['right' => ModuleExecutionAnswers::ANSWER_RIGHT]);
//            ->count();
//            ->asArray()->all();

        //количество страниц с вопросами
        $sub = Questions::find()->select('page_id')->distinct();
        $pagesCount = Module::findOne($this->module_id)->getPages()
            ->andWhere(['in', 'id', $sub])
            ->count();

        //все отвечено, возвращаем true
        if ($countAnswers->count() == $pagesCount)
            return true;

        //иначе ищем номер первой страницы без ответа и возвращаем его
        $new = $sub->asArray()->all();

        foreach ($countAnswers->asArray()->all() as $k1 => $v1) {
            foreach ($new as $k2 => $v2) {
                if ($v1 == $v2)
                    unset($new[$k2]);
            }
        }
        $new = array_values($new);

        $page = Pages::findOne($new[0]['page_id']);

        return $page->getNumberPage();
    }


    public function getUserAnswers()
    {
        $return = [];

        foreach ($this->module->pages as $i => $page) {
            $query = $this->getAnswers()
                ->joinWith(['question'])
                ->andWhere(['page_id' => $page->id]);

            if (!$query->exists()) {
                $return[] = 0;
//                $return[] = 0;
            } elseif ($query->andWhere(['right' => ModuleExecutionAnswers::ANSWER_RIGHT])->exists()) {
                $return[] = 1;
//                $return[] = 1;
            } else {
                $return[] = -1;
//                $return[] = -1;
            }
        }


        return $return;
    }


    /**
     * @inheritdoc
     * @return ModuleExecutionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ModuleExecutionQuery(get_called_class());
    }

}
