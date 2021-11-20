<?php 
/**
 *
 *  Сайдбар для шаблона!
 */



if($this->regOk) {

    $authorize = array(

        str_replace(':num', PROFILE['userid'] ,$this->allRoutes['/admin/profile/:num']['url']) 
                            => $this->allRoutes['/admin/profile/:num']['urltitle'].': '.PROFILE['username'],
        $this->allRoutes['/logout']['url'] 
                            => 'Cтатус: '.$this->allRoutes['/logout']['urltitle'],
        $this->allRoutes['/usersonline']['url'] 
                            => $this->allRoutes['/usersonline']['urltitle'].': '.$this->result['permanetRes']['online'],
        $this->allRoutes['/admin/notifications']['url'] 
                            => $this->allRoutes['/admin/notifications']['urltitle'].' '.$this->result['permanetRes']['notifications'],
    );

    $profile = array(
        str_replace(':num', PROFILE['userid'] ,$this->allRoutes['/admin/profile/:num']['url']) 
                            => 'Показать '.$this->allRoutes['/admin/profile/:num']['urltitle'],
        '' => 'Редактировать профиль',
        '' => 'Активные сессии',
        '' => 'Почта пользователя',
        '' => 'Добавить привелегии',
        '' => 'Редактирование привелегий',
        $this->allRoutes['/admin/users']['url'] => $this->allRoutes['/admin/users']['urltitle'],
        '' => 'Редактировать пользователей',
        $this->allRoutes['/usersonline']['url'] => $this->allRoutes['/usersonline']['urltitle'],
    );


    $settings = array(
        $this->allRoutes['/admin/settings/website']['url'] 
                    => $this->allRoutes['/admin/settings/website']['urltitle'],
    );
}



?>
    <aside>
        <div>
            <h3>Авторизация</h3>
            <ul>
                <?php 

                foreach ($authorize as $key => $value) {
                    
                    echo '<li><a href="'.$key.'" style="color: inherit; text-decoration: none;">'.$value.'</a></li>';
                }
                ?>
            </ul>
        </div>

        <div>
            <h3>Профиль и Пользователи</h3>
            <ul>
                <?php 

                foreach ($profile as $key => $value) {
                    
                    echo '<li><a href="'.$key.'" style="color: inherit; text-decoration: none;">'.$value.'</a></li>';
                }
                ?>
            </ul>
        </div>

        <div>
            <h3>Настройки</h3>
            <ul>
                <?php 

                foreach ($settings as $key => $value) {
                    
                    echo '<li><a href="'.$key.'" style="color: inherit; text-decoration: none;">'.$value.'</a></li>';
                }
                ?>
            </ul>
        </div>

    </aside>
</section>