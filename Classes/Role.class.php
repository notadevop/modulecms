<?php 

//class Role extends Database {
class Role extends Database {
	
	function __construct() { 

		parent::__construct(true);

        //$this->pdo = new Database(true);

		$this->permission = array();
	}

    private $pdo;
	private $permission;

	// Возвращает пояснение привелегии по указанной роли 

    public function getRolePerms(int $role_id): ?Role {

        $role = new Role();

        $sql = "SELECT t2.perm_desc 
        FROM role_perm as t1 JOIN permissions as t2 ON t1.perm_id = t2.perm_id 
        WHERE t1.role_id = :role_id";

        $binder = array(":role_id" => $role_id);

        $this->preAction($sql, $binder);

        if (!$this->doAction()) 
            return $this->resetAction() && $role;
     
        while($row = $this->postAction()->fetch()) {

            $role->permissions[$row["perm_desc"]] = true;
        }
        
        return $role; // Возвращает права роли
    }



    // Проверяет, если права установленны
    public function hasPerm(string $permission): bool {

        return isset($this->permissions[$permission]);
    }

    // Добавляем имя новой роли для например:  Администратор
    // 
    public function insertRole(string $role_name): bool {

        $sql = "INSERT INTO roles (role_name) VALUES (:role_name)";

        $binder = array(":role_name" => $role_name);

        $this->preAction($sql, $binder);

        return $this->doAction();
    }

    // delete array of roles, and all associations 
    // Удаляем массив ролей и все ассоциации с ним 
    // Удалит так же и таблицу где указаны роли для пользователя, Пользователь останеться без роли и привелегий
    public function deleteRoles(string $roles): bool {

        $sql = "DELETE t1, t2, t3 
        FROM roles as t1 JOIN user_role as t2 on t1.role_id = t2.role_id
        JOIN role_perm as t3 on t1.role_id = t3.role_id 
        WHERE t1.role_id = :role_id";

        $binder = array(":role_id" => $role_id);

        $this->preAction($sql, $binder);

        return $this->doAction();
    }

    // insert array of roles for specified user id добавляем массив ролей для указанного user_id
    public function insertUserRoles(int $user_id, string $roles): bool {

        $sql = "INSERT INTO user_role (user_id, role_id) VALUES (:user_id, :role_id)";
        
        $binder = array(
            ':user_id'  => $user_id,
            ':role_id'  => $role_id 
        );

        $this->preAction($sql, $binder);

        return $this->doAction();
    }

    // Удаляем все роли для указанного user_id 
    public function deleteUserRoles(int $user_id): bool {

        $sql = "DELETE FROM user_role WHERE user_id = :user_id";

        $binder = array(":user_id" => $user_id);

        $this->preAction($sql, $binder);

        return $this->doAction();
    }
	
}