<?php
    //--------------- настройки области---------

    session_start();
    if(empty($_SESSION['auth'])) header("Location: index.php"); // если пользователь не авторизирован, то переход на главную
    
    $id = $_SESSION['id']; // получение id пользователя
    $id_place = $_GET['id']; // получение id области

    require_once "database.php";
    use \RedBeanPHP\R as R;

     // если пользователь не является админом, то переход в профиль
     $admin = R::find('userplace', '`place`=? AND `user`=? AND `admin`=1', array($id_place, $id));
     if(empty($admin)) header("Location: profile.php");

    // поиск всех администраторов области
    $user_place = R::find('userplace', '`place`=? AND `admin`=1', array($id_place));
    $admins = [];
    foreach ($user_place as $v) {
        $admins [] = (int) $v->user; 
    }
    $admin_users = R::loadAll('users', $admins); // получение данных об администраторах
    

    
    // получение данных об области
    $place = R::load('places', $id_place);
    $title = $place->title;
    $description = $place->description;

    // изменение названия и описания
    if(isset($_POST['change_title_or_description'])){
        $new_title = $_POST['title'];
        $new_description = $_POST['description'];
        $place->title = $new_title;
        $place->description = $new_description;
        $place->date_update = date("d:m:y", time());
        R::store($place);
        $title = $new_title;
        $description = $new_description;
    }

    // удаление администратора (лишение прав на администрирование данной области)
    if(isset($_POST['delete_admin'])){
        $id_admin = $_POST['delete_id_admin'];
        $admin_place = R::find('userplace', '`place`=? AND `user`=?', array($id_place, $id_admin)); // найти этого админа
        foreach ($admin_place as $v) { // получить id записи
            $id_row_user_place = $v->id;
        }
        $delete_admin = R::load('userplace',$id_row_user_place); // найти запись
        $delete_admin->admin = 0; // установить права
        R::store($delete_admin);
        header("Location: settings.php?id=$id_place");
    }

    // удаление участника
    if(isset($_POST['delete_user'])){
        $id_user = $_POST['delete_id_user'];
        $delete_user_place = R::find('userplace', '`place`=? AND `user`=?', array($id_place, $id_user)); // найти этого юзера
        foreach ($delete_user_place as $v) { // получить id записи
            $id_row_user_place = $v->id;
        }
        $delete_user = R::load('userplace',$id_row_user_place); // найти запись
        R::trash($delete_user); // удалить запись
    }

    // поиск участников области
    $user_place = R::find('userplace', '`place`=?', array($id_place)); // найти участников
    $users = [];
    foreach ($user_place as $v) { // запомнить их id
        $users [] = (int) $v->user; 
    }
    $all_users = R::loadAll('users', $users); // получить данные об участниках

    $message = "";
    // добавление нового участника
    if(isset($_POST['added_new_user'])){
        $login = $_POST['login_new_user'];
        // проверка существования пользователя
        $user = R::find('users', '`login`=?', array($login));
        if(!empty($user)){
            foreach ($user as $v) { // получаем id пользователя
                $id_user = $v->id;
            }
            $new_userplace = R::dispense('userplace');
            $new_userplace->user = $id_user;
            $new_userplace->place = $id_place;
            if(isset($_POST['added_admin'])){ // если выбран пункт добавления админа
                $new_userplace->admin = 1;
            }
            R::store($new_userplace);
            header("Location: settings.php?id=$id_place");
        }else{
            $message = "Пользователь не найден";
        }
    }
    // удаление области
    if(isset($_POST['delete'])){
        $this_userplace = R::find('userplace', '`place`=?', array($id_place)); // найти все записи связывающие участников и область
        $this_userplace_id = [];
        foreach ($this_userplace as $v) {
            $this_userplace_id [] = (int) $v->id; // получение id записей
        }
        $drop_rows = R::loadAll('userplace',$this_userplace_id);
        var_dump($drop_rows);
        R::trashAll($drop_rows); // удаление записей userplace

        $drop_place = R::load('places', $id_place); // найти область
        R::trash($drop_place); // удалить

        header("Location: profile.php");
    }
    $place->date_update = date("d:m:y", time());
    R::store($place);

    require_once "vendor/autoload.php";

    $loader = new Twig_Loader_Filesystem('templates'); // создание загрузчика
    $twig = new Twig_Environment($loader); // инициализация

    $template = $twig->loadTemplate("settings.html.twig"); // загрузка шаблона
    echo $template->render(array("id_place"=>$id_place,"title"=>$title, "description"=>$description, "message"=>$message, "admin_users"=>$admin_users,
                                "id"=>$id, "all_users"=>$all_users)); // вывод шаблона