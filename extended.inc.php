<?php 



function convert($size): ?string {

    $unit=array('b','Kb','Mb','Gb','Tb','Pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

function languages(): string {

    // проверка не указал ли пользователь свой язык, 
    // если нет смотрим на браузер и по нему определяем есть ли такой язык
    // если нет устанавливаем язык который стоит по умолчанию (!)


    $known_langs = array('en','fr','de','es');
    $user_pref_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

    foreach($user_pref_langs as $idx => $lang) {
        $lang = substr($lang, 0, 2);
        if (in_array($lang, $known_langs)) {
            echo 'Preferred language is '.$lang;
            break;
        }
    }
}