<?php 
/**
 *
 *	Это заголовок шаблона!
 * 
 */

$stLinks = array(

    '/'         => 'Главная',
    '/admin'    => 'Админка'
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
    <meta name="description" content="Simple HTML5 Page layout template with header, footer, sidebar etc.">
    <script type="text/javascript" src="../../Templates/bootstrap/js/script.js"></script>
    <link rel="stylesheet" href="../../Templates/bootstrap/css/style.css">
    <script src="script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <div id="logo"><img src="../../Templates/bootstrap/img/logo.png"> %sitetitle% </div>

        <p> %site_description% </p>
        <nav>
            <ul>
                <?php 
                foreach ($stLinks as $key => $value) {
                    echo '<li><a href="'.$key.'">'.$value.'</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>

    <section>
        <strong>
            <div class="mb-3">
                <?php 
                    $alerts = array(

                        'primary'       => 'alert alert-primary',
                        'secondary'     => 'alert alert-secondary',
                        'success'       => 'alert alert-success',
                        'attentions'    => 'alert alert-danger',
                        'warnings'      => 'alert alert-warning',
                        'information'   => 'alert alert-info',
                        'light'         => 'alert alert-light',
                        'dark'          => 'alert alert-dark',
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



