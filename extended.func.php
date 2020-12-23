<?php 



function convert($size): string {

    $unit=array('b','Kb','Mb','Gb','Tb','Pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
