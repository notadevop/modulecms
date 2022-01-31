<?php 
/**
 * 
 */
class UsersOnline extends Visitor {
	
	function __construct(){ 
		parent::__construct(true);

		// init users who online
		$this->updateUsersOnline();
	}


	function viewOnlineUsers(){

		return $this->getOnlineUsers();
	}


	function countOnlineUsers(): int {

		return count($this->viewOnlineUsers());
	}
}