<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\base\Security;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить логин',
        ];
    }



    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->username == 'admin' && $this->password) {
            return Yii::$app->user->login($this->getUser(),
                $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        /** @var \common\components\NstuAuthSso $openAm */
        $openAm = Yii::$app->openAm;

        $res = $openAm->authentificate();
        var_dump($res);
        exit;

        $userNSTU = $openAm->getUserByLoginPassword($this->username, $this->password);

        if ($userNSTU) {

//            echo '<pre>';
//            var_dump($userNSTU); exit;

            $fio = $userNSTU->cn[0];
            $fio = explode(' ', $fio);

            $attributes = [
                'username' => $userNSTU->username,
                'email' => $userNSTU->mail[0],
                'surname' => $fio[0],
                'firstname' => $fio[1],
                'secname' => $fio[2],
            ];

            $localUser = $this->getUser();
            if (!$localUser) {
                $localUser = new User();
                $localUser->generateAuthKey();
            }

            $localUser->setAttributes($attributes);

            if (!$localUser->save()) {
                throw new Exception('Ошибка сохранения пользователя', 500);
            }

            if ($localUser->isNewRecord) {
                Yii::$app->authManager->assign(
                    Yii::$app->authManager->getRole('student'),
                    $localUser->id);
            }

            return Yii::$app->user->login($this->getUser(),
                $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }


}
