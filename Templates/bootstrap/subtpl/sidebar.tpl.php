<?php 

defined('ROOTPATH') or die();

/**
 *
 *  Сайдбар для шаблона!
 */




//<?=$Allroutes[$value]['url'];?>

    <aside>
        <div>
            <h3>Авторизайия</h3>
            <ul>
            <?php if($this->regOk) { ?>
                    <hr />
                    
                    <li>Пользователь: 
                        <a href="<?=str_replace(':num', PROFILE['userid'] ,$this->allRoutes['/profile/:num']['url']);?>">
                            <?=PROFILE['username'];?></a></li>

                    <li>Cтатус: <a href="<?=$this->allRoutes['/logout']['url'];?>"><?=$this->allRoutes['/logout']['urltitle'];?></a></li>
                    
                    <li><?=$this->allRoutes['/admin/users/online']['urltitle'];?>: 
                        <a href="<?=$this->allRoutes['/admin/users/online']['url'];?>">
                        (<?=$this->result['permanetRes']['online'];?>)
                        </a>
                    </li>

                    <li><?=$this->allRoutes['/admin/notifications']['urltitle'];?>: <a href="<?=$this->allRoutes['/admin/notifications']['url'];?>">(<?=$this->result['permanetRes']['notifications'];?>)</a></li>
            <?php } else { ?>
                    <li><a href="<?=$this->allRoutes['/login']['url'];?>"><?=$this->allRoutes['/login']['urltitle'];?></a></li>
                    <li><a href="<?=$this->allRoutes['/register']['url'];?>"><?=$this->allRoutes['/register']['urltitle'];?></a></li>
                    <li><a href="<?=$this->allRoutes['/restore']['url'];?>"><?=$this->allRoutes['/restore']['urltitle'];?></a></li>
            <?php } ?>
            </ul>
        </div>
        <div>Sidebar 2</div>
        <div>Sidebar 3</div>
    </aside>
</section>