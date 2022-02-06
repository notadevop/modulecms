<?php 

defined('ROOTPATH') or die();

/**
 *
 *  Сайдбар для шаблона!
 */


$authorize = array(

    str_replace(':num', PROFILE['userid'] ,$this->allRoutes['/profile/:num']['url']) 
                        => $this->allRoutes['/profile/:num']['urltitle'].': '.PROFILE['username'],
                        
    $this->allRoutes['/admin/notifications']['url'] 
                        => $this->allRoutes['/admin/notifications']['urltitle'].' '.$this->result['permanetRes']['notifications'],
    '/admin/users/sessions' => 'Активные пользователя сессии',
    '#' => 'Написать сообщение',

    $this->allRoutes['/logout']['url'] 
                        => 'Cтатус: '.$this->allRoutes['/logout']['urltitle'],
);

$profile = array(
    
    $this->allRoutes['/admin/users/online']['url'] 
                        => $this->allRoutes['/admin/users/online']['urltitle'].': ('.$this->result['permanetRes']['online'].')',

    $this->allRoutes['/admin/users']['url']         => $this->allRoutes['/admin/users']['urltitle'],

);


$settings = array(
    $this->allRoutes['/admin/settings/website']['url'] => $this->allRoutes['/admin/settings/website']['urltitle'],
    '4433' => 'Добавить общие привелегии',
    '128f' => 'Редактирование общих привелегий',
);

$tools = array(

    HOST.'/phpMyAdmin5/'        => 'PHPMyAdmin',
    'http://phptester.net/'     => 'PHPTester',
);

?>
    <aside>
        <div>
            <h3>Профиль</h3>
            <ul><?php 
                foreach ($authorize as $key => $value) {
                    echo '<li><a href="'.$key.'" style=" text-decoration: none;">'.$value.'</a></li>';
                } ?>
            </ul>
        </div>
        <div>
            <h3>Пользователи</h3>
            <ul><?php 
                foreach ($profile as $key => $value) {
                    echo '<li><a href="'.$key.'" style="text-decoration: none;">'.$value.'</a></li>';
                } ?>
            </ul>
        </div>
        <div>
            <h3>Настройки</h3>
            <ul><?php 
                foreach ($settings as $key => $value) {
                    echo '<li><a href="'.$key.'" style="text-decoration: none;">'.$value.'</a></li>';
                } ?>
            </ul>
        </div>

        <div>
            <h3>Инструменты</h3>
            <ul><?php 
                foreach ($tools as $key => $value) {
                    echo '<li><a href="'.$key.'" style="text-decoration: none;" target="_blank">'.$value.'</a></li>';
                } ?>
            </ul>
        </div>

    </aside>
</section>