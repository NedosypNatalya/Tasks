<?php
    session_start();
    $_SESSION['error-login'] = null;
    require_once "database.php";
    use \RedBeanPHP\R as R;

    if(!empty($_POST['login'])){

        $login = strip_tags($_POST['login']);
        $password = strip_tags($_POST['password']);

        if(!empty($_POST['name'])){
            // создание нового пользователя

            $user = R::find('users','`login`=?', array($login));
            if(!empty($user)){ // если такой логин уже существует, то уведомить об этом пользователя
                $_SESSION['error-login'] = true;
                header("Location: login.php?page=register");
            }else{ 
                $users = R::dispense('users');
            
                $name = strip_tags($_POST['name']);
                $surname = strip_tags($_POST['surname']);
                $email = strip_tags($_POST['email']);

                $users->name = $name;
                $users->surname = $surname;
                $users->email = $email;
                $users->login = $login;
                $users->password = password_hash($password, PASSWORD_DEFAULT);
                R::store($users); // отправка новых данных
            }
        }

        $user = R::find('users','`login`=?', array($login)); // поиск логина
        $id="";
        if (!empty($user)) { // если логин существует
            foreach ($user as $k => $v) { // проходимся по всем пользвателям с этим логином
                $pas = $v->password;
                if(password_verify($password, $pas)){ // сравниваем пароли
                    $id = $v->id; // если совпадение есть, то запоминаем id
                    $_SESSION['id'] = $id;
                    $_SESSION['auth'] = true;
                    //после успешной авторизации переходит на страницу профиля
                    header ("Location: profile.php");
                }
            }
        }else{
            header("Location: index.php");
        }

    }else{
        // переадресация на главную для случано-зашедших
        $_SESSION['auth'] = null;
        $_SESSION['id'] = null;
        header("Location: index.php");
    }