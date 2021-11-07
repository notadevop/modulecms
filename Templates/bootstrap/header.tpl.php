<?php 
/**
 *
 *	Это заголовок шаблона!
 * 
 */

$stLinks = array(

    '/'         => 'Главная',
    '/admin'    => 'Админка',
    /*'/test' => array(
        '/submenu' => 'submenu1',
        '/submenu1' => 'submenu2',
        '/submenu2' => 'submenu3',
        '/submenu3' => 'submenu4',
        '/submenu4' => 'submenu5',
    ),*/
);

?>

<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="canonical" href="https://html5-templates.com/" />
    <title> %title% </title>
    <meta name="description" content="Simple HTML5 Page">
    <script type="text/javascript" src="../../Templates/<?=$this->params['website_template'];?>/js/script.js"></script>
    <link rel="stylesheet" href="../../Templates/<?=$this->params['website_template'];?>/css/style.css">
    <script src="script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <div id="logo"><img src="../../Templates/bootstrap/img/logo.png"> %sitetitle% </div>

        <p> %site_description% </p>
       
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="container-fluid" style="background-color: #AEC6CF;">

            <!--
            <a class="navbar-brand" href="#">!</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            -->

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <?php 

                foreach ($stLinks as $key => $value) {
                    if(is_array($value)) {
                        ?>
                        <li class="nav-item dropdown">
                          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                           <?=$key;?>
                          </a>
                          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php 
                            foreach ($value as $j => $i) {
                                echo '<li><a class="dropdown-item" href="'.$j.'">'.$i.'</a></li>';
                            }
                            ?>
                          </ul>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li class="nav-item">
                          <a class="nav-link active" aria-current="page" href="<?=$key;?>"><?=$value;?></a>
                        </li>
                        <?php 
                    }
                }
                ?>
              </ul>

              <form class="d-flex" action="/" method="get">
                <input class="form-control me-2" type="search" placeholder="Поиск" aria-label="Search">
                <input type="submit" name="search" value="Поиск" class="btn btn-outline-success">
              </form>
            </div>
          </div>
        </nav>
    </header>


    <!-- LOGGER -->
    <section>
        <strong>
            <div class="mb-3">
                <?php 
                    $alerts = array(

                        Logger::PRIMARY         => 'alert alert-primary',
                        Logger::SECONDARY       => 'alert alert-secondary',
                        Logger::SUCCESS         => 'alert alert-success',
                        Logger::ATTENTIONS      => 'alert alert-danger',
                        Logger::WARNING         => 'alert alert-warning',
                        Logger::INFORMATION     => 'alert alert-info',
                        Logger::LIGHT           => 'alert alert-light',
                        Logger::DARK            => 'alert alert-dark',

                    );
                    foreach ($alerts as $key => $value) {
                        if (Logger::alertKeyExist($key)) {
                            echo '<div class="'.$value.'" role="alert"><ul>';
                            foreach (Logger::getAlerts($key) as $j => $i) {
                                echo '<li>'.$i.'</li>';   
                            }
                            echo '</ul></div>';
                        }
                    }
                ?>
            </div>
        </strong>
    </section>



