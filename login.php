<?php
    session_start();
    if(!empty($_SESSION['auth'])) $_SESSION['auth'] = null;

    $mesage_error_login = "";
    if(!empty($_SESSION['error-login'])){
        if($_SESSION['error-login']==true){
            $mesage_error_login = "Занят!";
            $_SESSION['error-login'] = null;
        }
    }

    require_once "vendor/autoload.php";

    $loader = new Twig_Loader_Filesystem('templates'); // создание загрузчика
    $twig = new Twig_Environment($loader); // инициализация

    $temp = $_GET['page'];
    if($temp == 'signin') $temp .= ".html.twig";
    else  $temp .= ".html.twig";


    $template = $twig->loadTemplate($temp); // загрузка шаблона
    echo $template->render(array("mesage_error_login"=>$mesage_error_login)); // вывод шаблона