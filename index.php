<?php
    session_start();
    if(!empty($_SESSION['auth'])) $_SESSION['auth'] = null;

    require_once "vendor/autoload.php"; // для работы с twig

    $loader = new Twig_Loader_Filesystem('templates'); // создание загрузчика
    $twig = new Twig_Environment($loader); // инициализация
    $template = $twig->loadTemplate('index.html.twig'); // загрузка шаблона
    echo $template->render(array()); // вывод шаблона