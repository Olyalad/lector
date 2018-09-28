<?php

namespace common\models;

use Yii;
use common\models\Module;
use yii\web\UploadedFile;
use himiklab\sortablegrid\SortableGridBehavior;

/**
 * This is the model class for table "pages".
 *
 * @property integer $id
 * @property string $name
 * @property string $text
 * @property string $image
 */
class Pages extends \yii\db\ActiveRecord
{
    public $imageFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages';
    }

    public function behaviors()
    {
        return [
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
            [['name', 'text'], 'required'],
            [['text', 'js_code', 'css_code'], 'string'],
            [['name', 'image'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => ['png', 'jpg', 'gif'], 'maxSize' => 3*1024*1024],
            ['sort_order', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Заголовок',
            'text' => 'Текст',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'questions' => 'Контрольные вопросы',
            'js_code' => 'Код JavaScript',
            'css_code' => 'Код Css',
        ];
    }


    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            foreach ($this->questions as $question) {
                $question->unlinkAll('answers', true);
            }
            $this->unlinkAll('questions', true);

            unlink(Yii::getAlias('@frontend') . '/web' . $this->image);

            return true;
        }
        return false;
    }


    /**
     * Модуль, к которому относится страница
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }


    /**
     * Вопросы к странице
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Questions::className(), ['page_id' => 'id']);
    }


    /**
     * Загрузка картинки
     * @return bool
     */
    public function upload()
    {
        $this->imageFile = UploadedFile::getInstance($this, 'imageFile');

        if (!$this->validate(['imageFile']))
            return false;

        if ($this->imageFile) {
            $name = uniqid();
            $path = Yii::getAlias('@frontend') . '/web/uploads/' . $name . '.' . $this->imageFile->extension;

            if (!$this->imageFile->saveAs($path))
                return false;

            $this->image = '/uploads/' . $name . '.' . $this->imageFile->extension;
            $this->imageFile = null;
        }
        return true;
    }


    /**
     * @return mixed
     */
    public function getImageurl()
    {
        return Yii::$app->urlManagerFrontEnd->CreateUrl($this->image);
    }


    /**
     * Возвращает номер слайда в модуле
     * @return int
     */
    public function getNumberPage()
    {
        foreach ($this->module->pages as $key => $pg) {
            if ($pg->id == $this->id) {
                return $key;
                break;
            }
        }
        return 1;
    }


    /**
     * Копирование страниц в модуль
     * @param $moduleId int id of Module
     * @param array $selection ids of Pages
     * @return bool
     */
    public static function copyPages($moduleId, array $selection)
    {
        $modelModule = Module::findOne($moduleId);
        foreach ($selection as $pageId) {
            $page = Pages::findOne($pageId);
            $newPage = new Pages();
            $newPage->attributes = [
                'name' => $page->name,
                'text' => $page->text,
//                'image' => $page->image,
                'js_code' => $page->js_code,
                'css_code' => $page->css_code,
            ];
            if ($page->image) {
                $extension = pathinfo($page->image, PATHINFO_EXTENSION);
                $newName = '/uploads/' . uniqid() . '.' . $extension;
                $path = Yii::getAlias('@frontend') . '/web';
                if (copy($path . $page->image, $path . $newName)) {
                    $newPage->image = $newName;
                }
            }
            $modelModule->link('pages', $newPage);
        }
        return true;
    }
}
