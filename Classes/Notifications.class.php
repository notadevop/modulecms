<?php 
/**
 *
 * 
 */

class Notifications extends Database  {

    public function __construct() { 

         parent::__construct(true);
    }

    function getAllNotifs(int $getById): ?array {

        if($getById == 0) {
            $sql = 'SELECT notif_title, notif_meta, notif_date, notif_read 
            FROM user_notifications 
            ORDER BY notif_date DESC';
        } else {
            $sql = 'SELECT notif_title, notif_meta, notif_date, notif_read 
            FROM user_notifications 
            WHERE user_id = %user_id% 
            ORDER BY notif_date DESC';
        }


    	return [];
    }

    function changeStatusNotif(int $id): bool {

    	return false;
    }

    function removeNotif(int $id): bool {

    	return false;
    }
}

