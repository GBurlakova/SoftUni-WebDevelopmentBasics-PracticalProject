<?php

class AccountModel extends BaseModel {
    public function register($firstName, $lastName, $username, $password) {
        $statement = self::$db->prepare("SELECT COUNT(Id) FROM users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $respond = array();
        if ($result['COUNT(Id)']) {
            $respond = array();
            $respond['statusCode'] = 400;
            $respond['message'] = 'Username is already taken';
        } else {
            $hash_pass = password_hash($password, PASSWORD_BCRYPT);
            $registerStatement = self::$db->prepare("INSERT INTO Users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
            $registerStatement->bind_param("ssss", $firstName, $lastName, $username, $hash_pass);
            $registerStatement->execute();
            $successfulRegister = $statement->affected_rows > 0;
            if($successfulRegister) {
                $respond['statusCode'] = 201;
            } else {
                $respond['statusCode'] = 400;
            }
        }

        return $respond;
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