<?php 

/**
 *
 * 
 */


class Languages {

    public function __construct(){ 

    	$l = new Visitor();

    	$this->curLang 		= $l->get_data()['lang'];
    	$this->languages 	= array();
    	$this->defLang 		= DEFLANGUAGE;
    }

    private $defLang;
    private $curLang;
    private $languages; 



    public static function initUserLang() {}
}