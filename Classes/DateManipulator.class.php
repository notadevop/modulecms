<?php 


/**
 * 
 */
class DateManipulator {
	
	function __construct() { }

	function dateToday(string $mask = 'Y.m.d') {

		return date($mask);
	}


	function differenctTime($start, $stop, $diffIn) {

			$start = strtotime($start);
			$stop = strtotime($stop);

			$dteStart = new DateTime($start);
   			$dteEnd   = new DateTime($stop);

   			$dteDiff = $dteStart->diff($dteEnd);

   			//debugger($dteDiff->format("%H:%I:%S"), __METHOD__);
	}


	function s_datediff(string $str_interval, $dt_menor, $dt_maior,bool $relative=false){

       if( is_string( $dt_menor)) $dt_menor = date_create( $dt_menor);
       if( is_string( $dt_maior)) $dt_maior = date_create( $dt_maior);

       $diff = date_diff($dt_menor, $dt_maior, !$relative);
      
       switch( $str_interval){
	           case "y":
	               $total = $diff->y + $diff->m / 12 + $diff->d / 365.25; 
	               break;
	           case "m":
	               $total= $diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24;
	               break;
	           case "d":
	               $total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h/24 + $diff->i / 60;
	               break;
	           case "h":
	               $total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i/60;
	               break;
	           case "i":
	               $total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
	               break;
	           case "s":
	               $total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;
	               break;
	          }

       	if( $diff->invert)
            return -1 * $total;
       	else    
       		return $total;
   }
}