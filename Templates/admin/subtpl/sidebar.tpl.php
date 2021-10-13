<?php 
/**
 *
 *  Сайдбар для шаблона!
 */

if($this->regOk) {

    $name   = '<a href="/admin/profile/'.PROFILE['userid'].'">'.PROFILE['username'].'</a>';
    $logout = '<a href="/logout">Выйти?</a>';
    $online = '<a href="/admin/usersonline">('.$this->result['permanetCtrlResult']['online']['result'].')</a>';
    $notifs = '<a href="/admin/notifications">(0)</a>';
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
        <div>
            <ul><label>Профиль</label><hr />
                <li><a href="/admin/profile/<?=PROFILE['userid'];?>">Профиль пользователь</a></li>
                <li>Редактировать профиль</li>
                <li>Активные сессии</li>
                <li>Добавить привелегии</li>
                <li>Редактирование привелегий</li>
                <li><a href="/admin/users">Список пользователей</a></li>
                <li>Редактировать пользователей</li>
                <li><a href="/admin/usersonline">Пользователи онлайн</a></li>
            </ul>
        </div>
        <div>  
            <ul><label></label><hr />
                <li>Почта пользователя</li>
                <li>Уведомления</li>
                <li>Активные сессии</li>
            </ul></div>
        <div>
            <ul><label>Настройки вебсайта</label><hr />
                <li>Загаловки</li>
                <li>Авторизация</li>
                <li></li>
            </ul>
        </div>
    </aside>
</section>