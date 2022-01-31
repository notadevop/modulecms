<?php 
/**
 *
 * 
 */

class Notifications extends Database  {

    public function __construct() { 

         parent::__construct(true);
    }

    function addNewNotif(int $userid, string $title, string $body): bool {

        $sql = 'INSERT INTO ';
        return false;
    }


    function getAllNotifs(int $userid, bool $readOnly=false): ?array {


        $sql = 'SELECT notif_title, notif_meta, notif_date, notif_read 
        FROM user_notifications 
        WHERE user_id = :userid ';

        if ($readOnly) {
            $sql .= ' and notif_read = :notifread ';
            $binder[':notifread']   = '0';
        }

        $sql .= 'ORDER BY notif_date DESC';

        $binder[':userid'] = $userid;

        $this->preAction($sql, $binder);

        if(!$this->doAction()) {

            return null;
        }

        return $this->postAction()->fetchAll();
    }

    function changeStatusNotif(int $nofitid): bool {

    	return false;
    }

    function removeNotif(int $id, bool $tempOnly=true): bool {

    	return false;
    }
}

