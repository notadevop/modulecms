<?php


class PrivilegedUser {
    private $roles;

    function __construct() {  }

    // override User method
    public static function getByUsername(string $username): ?PrivilegedUser {

        // Вытаскиваем пользователя
        $sql = "SELECT * FROM users WHERE username = :username";
        $sth = $GLOBALS["DB"]->prepare($sql);
        $sth->execute(array(":username" => $username));
        $result = $sth->fetchAll();

        if (!empty($result)) {

            $privUser = new PrivilegedUser();
            $privUser->user_id = $result[0]["user_id"];
            $privUser->username = $username;
            $privUser->password = $result[0]["password"];
            $privUser->email_addr = $result[0]["email_addr"];
            $privUser->initRoles();
            
            return $privUser;
        } else {
            
            return false;
        }
    }

    // populate roles with their associated permissions
    protected function initRoles(): void {

        $this->roles = array();
        $sql = "SELECT t1.role_id, t2.role_name 
        FROM user_role as t1 JOIN roles as t2 ON t1.role_id = t2.role_id 
        WHERE t1.user_id = :user_id";

        $sth = $GLOBALS["DB"]->prepare($sql);
        $sth->execute(array(":user_id" => $this->user_id));

        while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $this->roles[$row["role_name"]] = Role::getRolePerms($row["role_id"]);
        }
    }

    // check if user has a specific privilege
    public function hasPrivilege(string $perm): bool {

        foreach ($this->roles as $role) {
            
            if ($role->hasPerm($perm)) {
                return true;
            }
        }
        return false;
    }

    // check if a user has a specific role
    public function hasRole(string $role_name): bool {

        return isset($this->roles[$role_name]);
    }

    // insert a new role permission association
    public static function insertPerm(int $role_id, int $perm_id): bool {

        $sql = "INSERT INTO role_perm (role_id, perm_id) VALUES (:role_id, :perm_id)";
        $sth = $GLOBALS["DB"]->prepare($sql);
        $r = $sth->execute(array(":role_id" => $role_id, ":perm_id" => $perm_id));

        return $r;
    }

    // delete ALL role permissions
    public static function deletePerms(): bool  {
        
        $sql = "TRUNCATE role_perm";
        $sth = $GLOBALS["DB"]->prepare($sql);
        
        return $sth->execute();
    }

}