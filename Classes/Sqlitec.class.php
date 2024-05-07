<?php

namespace Database\SQLite3;


class Sqlitec {

	function __construct(string $pathtodb, string $mode=SQLITE3_ASSOC, bool $multiconn=false) {

		if ($multiconn) {

			if(!defined('SQLITE3_OPEN_SHAREDCACHE'))
				define( 'SQLITE3_OPEN_SHAREDCACHE' , 0x00020000 );
		}

		$this->mode = $mode;
		$this->sqlite = new SQLite3($pathtodb, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE | SQLITE3_OPEN_SHAREDCACHE );
	}

	private $sqlite;
	private $mode;

	function __destruct() {

		$this->sqlite->close();
	}

	function clean(string  $str): string {

		return $this->sqlite->escapeString($str);
	}

	function getLastID(): int{
		return $this->sqlite->lastInsertRowID();
	}

	// TODO: Как то использвовать или удалить

	function query(string $query ): bool {

		$res = $this->sqlite->query( $query );

		if ( !$res )
		  	throw new Exception( $this->sqlite->lastErrorMsg() );
		return $res;
	}

	function query_exec(string $query, string $value): void {

		$stmt = $this->sqlite->prepare($query);
		$stmt = bindValue('');
		return ;
	}

	function queryRow( $query ) {

		$res = $this->query( $query );
		$row = $res->fetchArray( $this->mode );
		return $row;
	}

	function queryOne( $query ){

		$res = $this->sqlite->querySingle( $query );
		return $res;
	}

	function queryAll( $query ){
		$rows = array();
		if( $res = $this->query( $query ) ){
		  while($row = $res->fetchArray($this->mode)){
		    $rows[] = $row;
		  }
		}
		return $rows;
	}
}
