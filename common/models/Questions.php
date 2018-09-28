<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "questions".
 *
 * @property integer $id
 * @property integer $page_id
 * @property string $question
 */
class Questions extends \yii\db\ActiveRecord
{
    public $useranswer;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'question'], 'required'],
            [['page_id', 'answer_id'], 'integer'],
            [['question'], 'string', 'max' => 255],
            [['answer_id'], 'required', 'on' => 'update']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page_id' => 'Page ID',
            'question' => 'Вопрос',
            'answer_id' => 'Ответ',
        ];
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->unlinkAll('answers', true);
            return true;
        }
        return false;
    }


    /**
     * Страница, которой принадлежит вопрос
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Pages::className(), ['id' => 'page_id']);
    }


    /**
     * Варианты ответов к вопросу
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answers::className(), ['question_id' => 'id']);
    }


    /**
     * Правильный ответ
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(Answers::className(), ['id' => 'answer_id']);
    }



    public function getUserAnswer()
    {
        return null;//$this->hasOne(Answers::className(), ['id' => 'answer_id']);
    }    
}
