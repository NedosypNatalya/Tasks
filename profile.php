<?php
    session_start();
    if(empty($_SESSION['auth'])) header("Location: index.php"); // если пользователь не авторизирован, то переход на главную

    $id = $_SESSION['id']; // получение id пользователя

    require_once "database.php";
    use \RedBeanPHP\R as R;
    // отправка запроса по получение всех данных о пользователе

    $user = R::load('users', $id);
    $name = $user->name;
    $surname = $user->surname;
    $login = $user->login;

    $fullname = $name." ".$surname;

    // получение списка областей
    $find_user_place = R::find('userplace', '`user`=?', array($id));
    $id_place = [];
    foreach ($find_user_place as $key => $value) {
        $id_place [] = (int) $value->place; // запись id областей в массив
    }
    $place = R::loadAll('places', $id_place); // получение всех данных об областях с которыми связан данный пользователь

    // создание новой области
    if(!empty($_POST['create_place_title'])){
        $new_place_title = $_POST['create_place_title'];
        $new_place_description = $_POST['create_place_description'];
        $new_place_date_create = date("d:m:y_h.i.s",time()); // создание уникальной даты и времени, чтобы можно было найти сразу 
        $new_place_date_update =  date("d:m:y",time());      // найти эту область и назвачить пользователя администратором

        echo "<br>дата время создания - ".$new_place_date_create."<br>Описание - ".$new_place_description."<br>";

        $places = R::dispense('places');
        $places->title = $new_place_title;
        $places->description = $new_place_description;
        $places->date_create = $new_place_date_create;
        $places->date_update = $new_place_date_update;
        R::store($places);
        // привязка области и создателя (администраторские права)
                // найденна область, которая только что была создана
        $find_place = R::find('places', '`date_create`=?', array($new_place_date_create));
        $find_place_id = 0;
        foreach ($find_place as $key => $value) {
            $find_place_id = $value->id;
        }

        echo "<br> id только что созданой области ".$find_place_id."<br>";
               // создание привязки
        $user_place = R::dispense('userplace');
        $user_place->user = $id;
        $user_place->place = $find_place_id;
        $user_place->admin = 1; // назначение администратором

        R::store($user_place);

        $_POST['create_place_title'] = null;
        $_POST['create_place_description'] = null;
        header("Location: profile.php"); // обновление страницы
    }
    require_once "vendor/autoload.php";

    $loader = new Twig_Loader_Filesystem('templates'); // создание загрузчика
    $twig = new Twig_Environment($loader); // инициализация

    $template = $twig->loadTemplate("profile.html.twig"); // загрузка шаблона
    $arr = ['1'=>'odin', '3'=>'tree'];
    echo $template->render(array("login"=>$login, "fullname"=>$fullname, "place"=>$place, "arr"=>$arr)); // вывод шаблона

