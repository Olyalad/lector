<?php

namespace common\components;

use Adldap\Adldap;
use Adldap\AdldapException;
use common\models\User;
use Yii;
use yii\base\Component;
use yii\base\Exception;

class NstuAuthSso extends Component
{
    /** @var OpenAmConfig */
    public $config = null;

    /** @var OpenAmParams */
    public $params = null;

    /** @var OpenAmDesc */
    public $desc = null;

    /** @var OpenAMAuth */
    private $_am_auth;

    /** @var User */
    private $_user;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!empty($this->config)) {
            $this->config = Yii::createObject($this->config);
        }
        if (!empty($this->params)) {
            $this->params = Yii::createObject($this->params);
        }
        if (!empty($this->desc)) {
            $this->desc = Yii::createObject($this->desc);
        }

        $this->_am_auth = new OpenAMAuth($this->config, $this->params, $this->desc);
    }


    /**
     * Запускаем процедуру аутентификации пользователя с редиректом на форму логина
     */
    public function authenticate()
    {
        if (!$this->_am_auth->openam_authenticate($this->config['redirect'])) {
            echo "not authenticated!";
            die();
        }
        return true;
    }


    /**
     * @return array|null
     */
    private function getUserInfo()
    {
//        $un = 'ladygina.2012@stud.nstu.ru';
//
//        // Create a configuration array.
//        $config = [
//            'default' => [
//                'domain_controllers' => ['nstu-dc1.corp.nstu.ru', 'nstu-dc2.corp.nstu.ru'],
//                'base_dn' => 'o=login,ou=services,dc=openam,dc=ciu,dc=nstu,dc=ru',
//                'admin_username' => 'ladygina.2012@stud.nstu.ru',
//                'admin_password' => 'wind0fChange',
//            ]];
//        $ad = new \Adldap\Adldap($config);
//
//        echo '<pre>';
//
//        try {
//            // If a successful connection is made to your server, the provider will be returned.
//            $provider = $ad->connect();
//
//            $search = $provider->search();
//
//            $results = $search->all();
//            var_dump($results);
//
//            $record = $search->find($un);
//            var_dump($record);
//
//            $record = $search->find('Ладыгина Ольга Сергеевна');
//            var_dump($record);
//
//            $record = $search->findBy('userprincipalname', $un);
//            var_dump($record);
//
//            $record = $search->findBy('samaccountname', $un);
//            var_dump($record);
//
//            $record = $search->findByDn('uid=ladygina.2012@stud.nstu.ru,o=login,ou=services,dc=openam,dc=ciu,dc=nstu,dc=ru');
//            var_dump($record);
//
//
//            $result = $provider->search()->read(true)->where('objectClass', '*')->get();
//            var_dump($result);
//
//        } catch (\Adldap\Auth\BindException $e) {
//
//            // There was an issue binding / connecting to the server.
//            die("Can't connect / bind to the LDAP server! Error: $e");
//
//        } catch (\Adldap\Models\ModelNotFoundException $e) {
//            // Record wasn't found!
//            die(" Error: $e");
//        }
//
//        exit;


        $amAuth = $this->_am_auth;

        // get token
        $token = $amAuth->openam_token();
        // validate token with openam request and redirect to login page if enabled
        $session = $amAuth->openam_validate($token);

        if ($session['valid']) {

            // configure session validation url and make http request
            $url = $amAuth->getConf('url') . $amAuth->getParam('attr') . $session['uid'];

            // Configure http context
            $context = array(
                "http" => array(
                    "method" => "GET",
                    "ignore_errors" => true,
                    "header" =>
                        "Content-Type: application/json\r\n" .
                        "Connection: close\r\n" .
                        $amAuth->getConf('cookie') . ': ' . $token,
                ),
            );
            // Send http request, verify and return result
            $response = file_get_contents($url, false, stream_context_create($context));

            return json_decode($response);
        }

        return null;
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function loginUser()
    {
        $userNSTU = $this->getUserInfo();
        $localUser = $this->getYiiUser($userNSTU->username);

        $fio = $userNSTU->cn[0];
        $fio = explode(' ', $fio);

        $attributes = [
            'username' => $userNSTU->username,
            'email' => isset($userNSTU->mail[0]) ? $userNSTU->mail[0] : null,
            'surname' => $fio[0],
            'firstname' => $fio[1],
            'secname' => $fio[2],
        ];

        if (!$localUser) {
            $localUser = new User();
            $localUser->generateAuthKey();
        }

        $localUser->setAttributes($attributes);

        if ($localUser->getDirtyAttributes() and !$localUser->save()) {
            throw new Exception('Ошибка сохранения пользователя', 500);
        }


//        $this->findUserFromAd();

        // Все пользователи по умолчанию имеют доступ к прохождению модулей
        if (!Yii::$app->authManager->checkAccess($localUser->id, 'student')) {
            Yii::$app->authManager->assign(
                Yii::$app->authManager->getRole('student'),
                $localUser->id);
        }

        return Yii::$app->user->login($localUser, 3600 * 24);
    }


    /**
     * Finds user by [[username]]
     * @return User|null
     */
    private function getYiiUser($username)
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($username);
        }

        return $this->_user;
    }


    public function logout()
    {
        Yii::$app->user->logout();

        $openAm = $this->_am_auth;
        $openAm->openam_logout();
    }

    /**
     * Поиск в Active Directory
     */
    private function findUserFromAd()
    {

    }


}