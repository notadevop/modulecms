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


function generateRandomString($length = 25, $numberOnly=true) {
    $characters = '0123456789';

    if (!$numberOnly) 
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


/*
*   Дебаггер для отладки кода
*
*/

function vardump($input) {

    echo '<pre>';
    var_dump($input);
    echo '</pre>';
}

function debugger($input='emptyOutput', $category=DEBUG, $params=array()): void {

    // Для массива  array $params использовать ниже указанные параметры: 

    /*
    __FILE__ – The full path and filename of the file.
    __DIR__ – The directory of the file.
    __FUNCTION__ – The function name.
    __CLASS__ – The class name.
    __METHOD__ – The class method name.
    __LINE__ – The current line number of the file.
    __NAMESPACE__ – The name of the current namespace
    */

    //$category = DEBUG;
    

    $preb = '<h3><pre style="margin: 45px; padding: 40px; color: blue;">';
    $pree = '</pre></h3>';

    echo $preb; 

    $userfunc = !true;

    if ($category) {

        echo '<span style="color: red;">DEBUG OUTPUT: </span><br/>';
        echo '<hr />';

        $debug = debug_backtrace();
        //debug_print_backtrace();
        if (!empty($debug)){
            for ($i=0; $i < count($debug); $i++) { 
                if($debug[$i]['function'] == 'debugger' ) {
                    
                    echo 'Error File: '.$debug[$i]['file'].'<br/>';
                    echo 'Error Line: '. $debug[$i]['line'].'<br/>';
                    
                    if(!empty($debug[$i]['args'])) {
                        foreach ($debug[$i]['args'] as $key => $value) {
                            print("Error Name: ".$value);
                        }
                    }
                } 
            }

            echo '<p>Var Type: '.strtoupper(gettype($input)).' </p>';
            echo '<hr />';
            
            if($userfunc) {
                echo '<span style="color: red;">Пользовательские функции: </span><br/>';
                $functions = get_defined_functions();
                $r = array_keys($functions['user']);
                print_r(array_values($functions['user']));
            }

        } else { echo 'debug is empty!'; }
        
    } else { 
        print_r($input); 
    }

    echo $pree;
}


function genCallTrace(){

    $e = new Exception();
    $trace = explode("\n", $e->getTraceAsString());
    // reverse array to make steps line up chronologically
    $trace = array_reverse($trace);
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();
   
    for ($i = 0; $i < $length; $i++) {

        $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }
   
    return "\t" . implode("\n\t", $result);
}

function debug($string) {
    echo '<pre>';
    print_r($string);
    echo '</pre>';
}