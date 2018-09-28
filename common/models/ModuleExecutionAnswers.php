<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "module_execution_answers".
 *
 * @property integer $id
 * @property integer $exec_id
 * @property integer $question_id
 * @property integer $useranswer
 * @property integer $right
 * @property integer $time
 */
class ModuleExecutionAnswers extends \yii\db\ActiveRecord
{
    const ANSWER_RIGHT = 1;
    const ANSWER_WRONG = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'module_execution_answers';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'time',
                'updatedAtAttribute' => 'time',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exec_id', 'question_id', 'useranswer', 'right'], 'required'],
            [['exec_id', 'question_id', 'useranswer', 'right', 'time'], 'integer'],
//            ['right', 'default', 'value' => self::ANSWER_RIGHT],
            ['right', 'in', 'range' => [self::ANSWER_RIGHT, self::ANSWER_WRONG]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exec_id' => 'Exec ID',
            'question_id' => 'Вопрос',
            'useranswer' => 'Ответ пользователя',
            'right' => 'Right',
            'time' => 'Время',
        ];
    }


    public function getExecution()
    {
        return $this->hasOne(ModuleExecution::className(), ['id' => 'exec_id']);
    }


    public function getQuestion()
    {
        return $this->hasOne(Questions::className(), ['id' => 'question_id']);
    }


    public function getUserAnswer()
    {
        return $this->hasOne(Answers::className(), ['id' => 'useranswer']);
    }


    public function getPage()
    {
//        return $this->question->getPage();
        return $this->hasOne(Pages::className(), ['id' => 'page_id'])
            ->via('question');
    }


}
