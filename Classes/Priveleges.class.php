<?php 

/**
 * 
 */
class Priveleges extends Database {
	
	function __construct() {

        parent::__construct(true);
        $this->roles = array();
	}

    private $pdo;
	private $roles;

    // populate roles with their associated permissions
    // Достаем все роли которые привязанны к указанному пользователю
    public function initRoles(int $user_id): void {

        $sql = "SELECT t1.role_id, t2.role_name 
        FROM user_role as t1 JOIN roles as t2 ON t1.role_id = t2.role_id 
        WHERE t1.user_id = :user_id";

        $binder = array(":user_id" => $user_id);

        $this->preAction($sql, $binder);
        $this->doAction();

        $role = new Role();

        while($row = $this->postAction()->fetch()) {

            $this->roles[$row['role_name']] = $role->getRolePerms($row['role_id']);
        }
    }

    // check if user has a specific privilege
    // Проверяем существуют ли привелегии у пользователя
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

        $sql = 'INSERT INTO role_perm (role_id, perm_id) VALUES (:role_id, :perm_id)';

        $binder = array(
                ':role_id' => $role_id, 
                ':perm_id' => $perm_id
            );

        $this->preAction($sql, $binder);

        return $this->doAction();
    }   

    // delete ALL role permissions
    public static function deletePerms(): bool  {
        
        $sql = 'TRUNCATE role_perm';
        $sth = $this
            ->get_con()
            ->prepare($sql);
        
        return $sth->execute();
    }
}