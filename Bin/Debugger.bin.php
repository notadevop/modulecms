<?php

namespace Debug;

final class DebuggerСlass {

  private $start;

  private function timeStamp() {

    $time = microtime();
  	$time = explode(' ', $time);
  	$time = $time[1] + $time[0];
  	return $time;
  }

  function startTimer() {
    $this->start = $this->timeStamp();
  }

  function stopTimer() {

    $total_time = round(($this->timeStamp() - $this->start), 4);
    return 'Загрузка: ' . $total_time . ' cекунд.';
  }

  static function debugger($input='empty_output', $category = 'DEBUG', $params = Array()): void {

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


      $bhtml = '<h3><pre style="margin: 45px; padding: 40px; color: blue;">';
      $pree = '</pre></h3>';

      echo $bhtml;

      $userfunc = !true;

      if ($category) {

          echo '<span style="color: red;">Вывод отладчика: </span><br/>';
          echo '<hr />';

          $debug = debug_backtrace();
          //debug_print_backtrace();
          if (!empty($debug)){
              for ($i=0; $i < count($debug); $i++) {
                  if($debug[$i]['function'] == 'debugger' ) {

                      print('Файл запуска: <span style="color: #AF4052">'.$debug[$i]['file'].'</span><br/>');
                      print('Линия ошибки: <span style="color: #AF4052">'. $debug[$i]['line'].'</span><br/>');

                      if(!empty($debug[$i]['args'])) {

                          print('<pre>');
                          print('Данные ошибки!');

                          foreach ($debug[$i]['args'] as $key => $value) {
                            if(is_array($value)) {
                              print_r($value);
                            } else {
                              print('<br /><span color: blue;>&emsp; Линия: <span style="color: #AF4052">'.$value.'</span>');
                            }

                          }
                          echo '</pre>';
                      }
                  }
              }

              echo '<p>Тип переменной: <span style="color: #AF4052">'.strtoupper(gettype($input)).' </span></p>';
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

  /*
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
  */

}
