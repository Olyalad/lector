<?php

use common\models\User;
use yii\db\Migration;

class m170418_070022_add_rule extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Создание ролей
        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';

        $teacher = $auth->createRole('teacher');
        $teacher->description = 'Преподаватель';

        $user = $auth->createRole('student');
        $user->description = 'Студент';

        // запись в БД
        $auth->add($admin);
        $auth->add($user);
        $auth->add($teacher);

        // Создание разрешений
        $createModule = $auth->createPermission('createModule');
        $createModule->description = 'Создание модулей';

        $viewModule = $auth->createPermission('viewModule');
        $viewModule->description = 'Прохождение модулей';

        // запись в БД
        $auth->add($createModule);
        $auth->add($viewModule);

        // наследования разрешений
        $auth->addChild($user, $viewModule);
        $auth->addChild($teacher, $createModule);
        // наследования ролей
        $auth->addChild($admin, $user);
        $auth->addChild($admin, $teacher);
        $auth->addChild($teacher, $user);


        //добавление пользователей
        $man = new User();
        $man->username = 'root';
        $man->email = 'root@root.com';
        $man->setPassword('root');
        $man->generateAuthKey();

        if ($man->save())
            $auth->assign($admin, $man->id);

        $man = new User();
        $man->username = 'student';
        $man->email = 'stud@stud.com';
        $man->setPassword('student');
        $man->generateAuthKey();

        if ($man->save())
            $auth->assign($user, $man->id);

        $man = new User();
        $man->username = 'teacher';
        $man->email = 't@teach.com';
        $man->setPassword('teacher');
        $man->generateAuthKey();

        if ($man->save())
            $auth->assign($teacher, $man->id);
    }

    public function safeDown()
    {
        User::findOne(['username'=>'root'])->delete();
        User::findOne(['username'=>'student'])->delete();
        User::findOne(['username'=>'teacher'])->delete();

        $auth = Yii::$app->authManager;
        $auth->removeAll(); //удаляем старые данные из БД
    }
}
