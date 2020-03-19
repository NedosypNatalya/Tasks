<?php
    session_start();
    if(empty($_SESSION['auth'])) header("Location: index.php"); // если пользователь не авторизирован, то переход на главную

    $id = $_SESSION['id']; // получение id пользователя
    $id_task = $_GET['task-id']; // получение id задачи

    require_once "database.php";
    use \RedBeanPHP\R as R;

    $task = R::load('tasks', $id_task);

    $title = $task->title;
    $text = $task->text;
    $id_place = $task->place;
    $status = $task->complete;
    $date_complete = $task->date_complete;
    $user_do = $task->user_do;

    //данные о поьзователе, который выполняет задание
    $user = R::load('users', $user_do);
    $user_do_login = $user->login;

    // закрытие задачи
    if(isset($_POST['close-task'])){
        $task->date_complete = date("Y-m-d", time());
        $task->complete = 1;
        R::store($task);
        $_POST['close-task'] = null;
        header("Location: task.php?task-id=$id_task");
    }

    require_once "vendor/autoload.php";

    $loader = new Twig_Loader_Filesystem('templates'); // создание загрузчика
    $twig = new Twig_Environment($loader); // инициализация

    $template = $twig->loadTemplate("task.html.twig"); // загрузка шаблона
    echo $template->render(array("id_place"=>$id_place,"title"=>$title, "text"=>$text, "status"=>$status, "date_complete"=>$date_complete,
                                "user_do"=>$user_do, "user_do_login"=>$user_do_login, "id_place"=>$id_place)); // вывод шаблона