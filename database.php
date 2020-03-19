<?php
    
    require_once __DIR__ . '/vendor/autoload.php';
    use \RedBeanPHP\R as R;

    R::setup('mysql:host = 127.0.0.1; dbname=tasks', 'root', '');
    if(!R::testConnection()){
        exit('Нет подключения!');
    }
