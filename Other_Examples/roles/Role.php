<?php

class Role {

    private $permissions; // В этой переменной храняться все права  

    protected function __construct(): void {

        $this->permissions = array();
    }

    // Возвращает объект роли с ассоциирующимися правами
    public static function getRolePerms(int $role_id): Role {

        $role = new Role();
        $sql = "SELECT t2.perm_desc 
        FROM role_perm as t1 JOIN permissions as t2 ON t1.perm_id = t2.perm_id 
        WHERE t1.role_id = :role_id";

        $sth = $GLOBALS["DB"]->prepare($sql);
        $sth->execute(array(":role_id" => $role_id));

        while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $role->permissions[$row["perm_desc"]] = true;
        }

        return $role; // Возвращает права роли
    }

    // Проверяет, если права установленны
    public function hasPerm(string $permission): bool {

        return isset($this->permissions[$permission]);
    }

    // Добавляем новую роль
    public static function insertRole(string $role_name): bool {

        $sql = "INSERT INTO roles (role_name) VALUES (:role_name)";
        $sth = $GLOBALS["DB"]->prepare($sql);
        return $sth->execute(array(":role_name" => $role_name));
    }

    // delete array of roles, and all associations Удаляем массив ролей и все ассоциации с ним 
    public static function deleteRoles(string $roles): bool {

        $sql = "DELETE t1, t2, t3 
        FROM roles as t1 JOIN user_role as t2 on t1.role_id = t2.role_id
        JOIN role_perm as t3 on t1.role_id = t3.role_id 
        WHERE t1.role_id = :role_id";

        $sth = $GLOBALS["DB"]->prepare($sql);

        $sth->bindParam(":role_id", $role_id, PDO::PARAM_INT);
        foreach ($roles as $role_id) {
            $sth->execute();
        }
        return true;
    }

    // insert array of roles for specified user id добавляем массив ролей для указанного user_id
    public static function insertUserRoles(int $user_id, string $roles): bool {

        $sql = "INSERT INTO user_role (user_id, role_id) VALUES (:user_id, :role_id)";
        
        $sth = $GLOBALS["DB"]->prepare($sql);
        $sth->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $sth->bindParam(":role_id", $role_id, PDO::PARAM_INT);
        
        foreach ($roles as $role_id) {
            $sth->execute();
        }
        return true;
    }

    // Удаляем все роли для указанного user_id 
    public static function deleteUserRoles(int $user_id): bool {

        $sql = "DELETE FROM user_role WHERE user_id = :user_id";
        $sth = $GLOBALS["DB"]->prepare($sql);
        return $sth->execute(array(":user_id" => $user_id));
    }

}
