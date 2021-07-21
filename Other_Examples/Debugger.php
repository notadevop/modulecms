<?php

define('TRACES_MODE', 'TEXTAREA');//'TEXTAREA' or 'FIREPHP'

$GLOBALS['traces.pre']=array();
$GLOBALS['traces'] = array();

function my_array_diff($arr1, $arr2) {

  foreach ($arr1 as $k=>$v) {
            if (in_array($v, $arr2, true)) { unset($arr1[$k]); }
  }
  return $arr1;
}

function my_var_export($var, $is_str=false) {

  $rtn=preg_replace(
      array('/Array\s+\(/', 
            '/\[(\d+)\] => (.*)\n/', 
            '/\[([^\d].*)\] => (.*)\n/'), 
            array(
              'array (', 
              '\1 => \'\2\''."\n", 
              '\'\1\' => \'\2\''."\n"
            ), 
      
            substr(print_r($var, true), 0, -1));

            $rtn=strtr($rtn, array("=> 'array ('"=>'=> array ('));
            $rtn=strtr($rtn, array(")\n\n"=>")\n"));
            $rtn=strtr($rtn, array("'\n"=>"',\n", ")\n"=>"),\n"));
            $rtn=preg_replace(
                              array('/\n +/e'), 
                              array('strtr(\'\0\', array(\'    \'=>\'  \'))'), $rtn);
            $rtn=strtr($rtn, array(" Object',"=>" Object'<-")
      );
          
  if ($is_str) {
    return $rtn;
  } else {
    echo $rtn;
  }
}
function tick_handler() {

  $tmp    = debug_backtrace();
  $trace  = my_array_diff($tmp, $GLOBALS['traces.pre']);
  //echo '<pre>';var_export($trace);echo '</pre>';echo '<br/>'; //for debug diyism_trace.php
  $trace  = array_values($trace);
  $GLOBALS['traces.pre']=$tmp;


  //filter empty array and rearrange array_values(), because some lines will trigger two tick events per line, 
  //for example: 1.last line is "some code;questmark>" 2.error_reporting(...
  
  if (count($trace) > 0 && $trace[0]['file'] . '/' . @$tmp[1]['function'] !== @$GLOBALS['traces'][count($GLOBALS['traces'])-1]['key']) {

    for ($i=count($trace)-1; $i>=0; --$i) {

      $GLOBALS['traces'][]=$tmp_fb=array_merge(array(
                                                  'key'=>$trace[$i]['file'].'/'.@$tmp[$i+1]['function']), 
                                                  $trace[$i], 
                                                  array(
                                                    'function'=>strtr($trace[$i]['function'], 
                                                      array('tick_handler'=>'CONTINUE')
                                                  ), 
                                                  'in_function' => @$tmp[$i+1]['function']
                                                )
                                              );
      
      TRACES_MODE === 'FIREPHP' ? fb(trace_output($tmp_fb), 'diyism_trace:'.++$GLOBALS['diyism_trace_no']) : '';
    }
  }
}


function trace_output($trace) {

    $trace['in_function'] = strtr(@$trace['in_function'], array(
                                                            'require'       => '', 
                                                            'require_once'  => '', 
                                                            'include'       => '', 
                                                            'include_once'  =>''
                                                          )
    );
    $trace['args'] = $trace['args'] ? strtr(preg_replace(
                                                  array('/\n +/'), 
                                                  array(''), 
                                                  preg_replace(array('/\n  \d+ => /'), array(''), 
                                                  substr(my_var_export($trace['args'], true), 7, -3))), 
                                                  array("\r"=>'\r', "\n"=>'\n')
                                          ) : '';

    return $trace['file'].($trace['in_function'] ? '/'.$trace['in_function'].'()' : '' ).'/'.$trace['line'].': '.$trace['function'].'('.$trace['args'].')';
}

function traces_output() {

    echo '<textarea style="width:100%;height:300px;">';
    $GLOBALS['traces'] = array_slice($GLOBALS['traces'], 2);//remove registering tick line and requiring 'diyism_trace.php' line

    foreach ($GLOBALS['traces'] as $k=>$trace) {

      echo htmlentities($k . ':' . trace_output($trace)."\n");
    }
    echo '</textarea>';
}


register_tick_function('tick_handler');

TRACES_MODE==='TEXTAREA' ? register_shutdown_function('traces_output') : '';




declare(ticks=1);
//require 'diyism_trace.php';

a('a', array('hello'));
1+2;
b();

function a() { 
  $d=1;
  b();
  $d=2;
}

function b() {
  1+1;
}