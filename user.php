<?php
    //----------- профиль не авторизированного пользователя -----------------
    session_start();
    if(empty($_SESSION['auth'])) header("Location: index.php"); // если пользователь не авторизирован, то переход на главную

    $id_user = $_GET['user'];

    require_once "database.php";
    use \RedBeanPHP\R as R;

    $user = R::load('users', $id_user); // получение данных о пользователе (не авторизированном)
    $login = $user->login;
    $name = $user->name;
    $surname = $user->surname;
    $fullname = $name." ".$surname;

    require_once "vendor/autoload.php";

    $loader = new Twig_Loader_Filesystem('templates'); // создание загрузчика
    $twig = new Twig_Environment($loader); // инициализация

    $template = $twig->loadTemplate("user.html.twig"); // загрузка шаблона
    echo $template->render(array("login"=>$login,"fullname"=>$fullname)); // вывод шаблона

