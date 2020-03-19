<?php
    session_start();
    if(empty($_SESSION['auth'])) header("Location: index.php"); // если пользователь не авторизирован, то переход на главную

    $id = $_SESSION['id']; // получение id пользователя
    $id_place = $_GET['id']; // получение id области

    require_once "database.php";
    use \RedBeanPHP\R as R;

    // получение данных об области
    $place = R::load('places', $id_place);
    $title = $place->title;
    $description = $place->description;

    // если пользователь администратор, то ему доступны настройки области
    $user_place = R::find('userplace', '`user`=? AND `place`= ?', array($id, $id_place));

    if(empty($user_place)) header("Location: profile.php"); // если пользователь не найден, то переход обратно в профиль

    $admin = null;
    foreach ($user_place as $v) {
        $admin = $v->admin;
    }
    $settings = "<a href=\"settings.php?id=$id_place\">Настройки</a> ";

    // поиск всех администраторов области
    $user_place = R::find('userplace', '`place`=? AND `admin`=1', array($id_place));
    $admins = [];
    foreach ($user_place as $v) {
        $admins [] = (int) $v->user; 
    }
    $admin_users = R::loadAll('users', $admins); // получение данных об администраторах

    // поиск участников области
    $user_place = R::find('userplace', '`place`=?', array($id_place));
    $users = [];
    foreach ($user_place as $v) {
        $users [] = (int) $v->user; 
    }
    $all_users = R::loadAll('users', $users);


    //=> работа с задачами
    // добавление задачи
    if(isset($_POST['added-task'])){
        $title_task = $_POST['title-task'];
        $text_task = $_POST['text-task'];
        $date_create_task = date("d:m:y_h.i.s", time());
        $user_create_task = $id;
        $user_do_task = $_POST['user-do'];
        $status_complete = 0;

        $task = R::dispense('tasks');
        $task->title = $title_task;
        $task->text = $text_task;
        $task->place = $id_place;
        $task->date_create = $date_create_task;
        $task->user_create = $user_create_task;
        $task->user_do = $user_do_task;
        $task->complete = $status_complete;
        R::store($task);
    }
    // вывод задач
    $tasks = R::find('tasks', '`place`=?', array($id_place));
    // удаление задачи
    if(isset($_POST['delete-task'])){
        $id_task = $_POST['delete-id-task'];
        $delete_task = R::load('tasks', $id_task);
        R::trash($delete_task);
        $_POST['delete-task']=null;
        header("Location: area.php?id=$id_place");
    }

    require_once "vendor/autoload.php";

    $loader = new Twig_Loader_Filesystem('templates'); // создание загрузчика
    $twig = new Twig_Environment($loader); // инициализация

    $template = $twig->loadTemplate("area.html.twig"); // загрузка шаблона
    echo $template->render(array("title"=>$title,"description"=>$description, "admin_users"=>$admin_users, "id"=>$id, "admin"=>$admin,
                                "all_users"=>$all_users, "tasks"=>$tasks, "id_place"=>$id_place)); // вывод шаблона