<?php
class AccountModel extends BaseModel {
    public function register($firstName, $lastName, $username, $password) {
        $statement = self::$db->prepare("SELECT COUNT(Id) FROM users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $response = array();
        if ($result['COUNT(Id)']) {
            $response = array();
            $response['statusCode'] = 400;
            $response['message'] = 'Username is already taken';
        } else {
            $hash_pass = password_hash($password, PASSWORD_BCRYPT);
            $registerStatement = self::$db->prepare("INSERT INTO Users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
            $registerStatement->bind_param("ssss", $firstName, $lastName, $username, $hash_pass);
            $registerStatement->execute();
            $successfulRegister = $statement->affected_rows > 0;
            if($successfulRegister) {
                $response['statusCode'] = 201;
            } else {
                $response['statusCode'] = 400;
            }
        }

        return $response;
    }

    public function login($username, $password) {
        $statement = self::$db->prepare(
            "SELECT u.id, u.username, u.password, r.name as role
             FROM users u LEFT OUTER JOIN user_roles ur ON u.id = ur.user_id
             LEFT OUTER JOIN roles r ON ur.role_id = r.id WHERE u.username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();

        $response = array();
        if(password_verify($password, $result['password'])) {
            $response['statusCode'] = 200;
            if($result['role'] == 'administrator') {
                $response['isAdmin'] = true;
            } else {
                $response['isAdmin'] = false;
            }
        } else {
            $response['statusCode'] = 400;
        }

        return $response;
    }

    public function verifyUserRole($username, $role) {
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

    public function profile($username) {
        $statement = self::$db->prepare("SELECT username, first_name, last_name FROM users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $profileInformation = $statement->get_result()->fetch_assoc();
        return $profileInformation;
    }

    public function editProfile($currentUsername, $newFirstName, $newLastName, $newUsername, $newPassword){
        $statement = self::$db->prepare("SELECT COUNT(Id) FROM users WHERE username = ?");
        $statement->bind_param("s", $newUsername);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $response = array();
        if ($result['COUNT(Id)'] && $currentUsername != $newUsername) {
            $response = array();
            $response['statusCode'] = 400;
            $response['message'] = 'Username is already taken';
        } else {
            $hash_pass = password_hash($newPassword, PASSWORD_BCRYPT);
            $updateProfileStatement = self::$db->prepare(
                "UPDATE users SET first_name = ?, last_name = ?, username = ?, password = ?
                 WHERE username = ?");
            $updateProfileStatement->bind_param("sssss", $newFirstName, $newLastName, $newUsername, $hash_pass, $currentUsername);
            $updateProfileStatement->execute();
            $successfulEdit = $statement->affected_rows > 0;
            if($successfulEdit) {
                $response['statusCode'] = 200;
            } else {
                $response['statusCode'] = 400;
            }
        }

        return $response;
    }
}