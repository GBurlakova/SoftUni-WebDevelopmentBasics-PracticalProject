<?php

class AccountModel extends BaseModel {
    public function register($username, $password) {
        $statement = self::$db->prepare("SELECT COUNT(Id) FROM users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        if ($result['COUNT(Id)']) {
            return false;
        }

        $hash_pass = password_hash($password, PASSWORD_BCRYPT);

        $registerStatement = self::$db->prepare("INSERT INTO Users (username, password) VALUES (?, ?)");
        $registerStatement->bind_param("ss", $username, $hash_pass);
        $registerStatement->execute();

        return true;
    }

    public function login($username, $password) {
        $statement = self::$db->prepare("SELECT Id, username, password FROM users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();

        if(password_verify($password, $result['password'])) {
            return true;
        }

        return false;
    }

    public function verifyUserRole($username, $role){
        $statement = self::$db->prepare(
            "SELECT COUNT(u.id) FROM users u
              INNER JOIN user_roles ur ON u.id = ur.user_id
              INNER JOIN roles r ON r.id = ur.role_id
              WHERE u.username = ? AND r.name = ?");
        $statement->bind_param("ss", $username, $role);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        if (!$result['COUNT(u.id)']) {
            return false;
        }

        return true;
    }

    public function profile($username){
        $statement = self::$db->prepare("SELECT username, first_name, last_name FROM users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $profileInformation = $statement->get_result()->fetch_assoc();
        return $profileInformation;
    }
}