<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 18.04.2017
 * Time: 14:35
 */

use common\models\Answers;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $id integer */
/* @var $slide common\models\Pages */
/* @var $pagination \yii\data\Pagination */

?>


<?php
$questions = $slide->questions;

if ($questions) {
    $question = $questions[array_rand($questions)];

    $form = ActiveForm::begin();

    echo Html::activeHiddenInput($question, 'id');

    echo "<br><h4>" . $question->question . "</h4>";

    $answers = Answers::find()->where(['question_id' => $question->id])->all();
    shuffle($answers);

    echo $form->field($question, 'useranswer', ['inline' => false])
        ->label(false)
        ->radioList(ArrayHelper::map($answers, 'id', 'answer'));

    echo Html::submitButton('Продолжить', ['class' => 'btn btn-primary']);

    if (Yii::$app->controller->action->id == 'test') {
        echo Html::a('Редактировать этот вопрос', Yii::$app->urlManagerBackend->createUrl(['questions/update',
            'id' => $question->id]), ['class' => 'btn btn-info pull-right']);
    }

    ActiveForm::end();
}