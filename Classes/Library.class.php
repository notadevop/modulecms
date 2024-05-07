<?php

namespace ToolLibrary;

class Tools {

  function convert($size): ?string {

      $unit=array('b','Kb','Mb','Gb','Tb','Pb');
      return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
      
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
}
