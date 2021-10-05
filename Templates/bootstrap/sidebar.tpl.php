<?php 
/**
 *
 *  Сайдбар для шаблона!
 */

if($this->regOk) {

    $name   = '<a href="/profile/'.PROFILE['userid'].'">'.PROFILE['username'].'</a>';
    $logout = '<a href="/logout">Выйти?</a>';
    $online = '<a href="/usersonline">('.$this->result['permanetCtrlResult']['online']['result'].')</a>';
    $notifs = '<a href="/notifications">(0)</a>';
} else {
    $name = 'Гость';
    $logout = '<a href="/login">Авторизоваться</a>';
    $online = '('.$this->result['permanetCtrlResult']['online']['result'].')';
    $notifs = '0';
}
?>
    <aside>
        <div>
            <h3>Авторизайия</h3>
            <ul>
            <?php if($this->regOk) { ?>

                    <hr />
                    <li>Пользователь: <?=$name;?></li>
                    <li>Cтатус: <?=$logout;?></li>
                    <li>Пользователей онлайн: <?=$online;?></li>
                    <li>Уведомления: <?=$notifs;?></li>
            <?php } else { ?>
                    <li><a href="/login">Логин</a></li>
                    <li><a href="/register">Регистрация</a></li>
                    <li><a href="/restore">Восстановление</a></li>  
            <?php } ?>
            </ul>
        </div>
        <div>Sidebar 2</div>
        <div>Sidebar 3</div>
    </aside>
</section>