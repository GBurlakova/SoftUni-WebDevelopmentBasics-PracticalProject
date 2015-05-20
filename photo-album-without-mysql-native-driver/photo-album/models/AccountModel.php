<?php
class AccountModel extends BaseModel {
    public function register($firstName, $lastName, $username, $password) {
        $statement = self::$db->prepare("SELECT COUNT(id) as usernameExists FROM users WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $statement->bind_result($usernameExists);
        $result = array();
        while($statement->fetch()) {
            $result['usernameExists'] = $usernameExists;
        }

        if ($result['usernameExists']) {
            $response = array();
            $response['statusCode'] = 400;
            $response['message'] = 'Username is already taken';
        } else {
            $hash_pass = password_hash($password, PASSWORD_BCRYPT);
            $registerStatement = self::$db->prepare("INSERT INTO Users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
            $registerStatement->bind_param("ssss", $firstName, $lastName, $username, $hash_pass);
            $registerStatement->execute();
            $successfulRegister = $registerStatement->affected_rows > 0;
            if($successfulRegister) {
                $response['statusCode'] = 201;
            } else {
                $response['statusCode'] = 400;
            }
        }

        return $response;
    }

    public function login($usernameInput, $passwordInput) {
        $statement = self::$db->prepare(
            "SELECT u.id, u.username, u.password, r.name as role
             FROM users u LEFT OUTER JOIN user_roles ur ON u.id = ur.user_id
             LEFT OUTER JOIN roles r ON ur.role_id = r.id WHERE u.username = ?");
        $statement->bind_param("s", $usernameInput);
        $statement->execute();
        $statement->bind_result($id, $username, $password, $role);
        $result = array();
        while($statement->fetch()) {
            $result['id'] = $id;
            $result['username'] = $username;
            $result['password'] = $password;
            $result['role'] = $role;
        }

        $response = array();
        if(password_verify($passwordInput, $result['password'])) {
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

    public function verifyUserRole($usernameInput, $roleInput) {
        $statement = self::$db->prepare(
            "SELECT COUNT(u.id) userIsInRole FROM users u
              INNER JOIN user_roles ur ON u.id = ur.user_id
              INNER JOIN roles r ON r.id = ur.role_id
              WHERE u.username = ? AND r.name = ?");
        $statement->bind_param("ss", $usernameInput, $roleInput);
        $statement->execute();
        $statement->bind_result($userIsInRole);
        $result = array();
        while($statement->fetch()) {
            $result['userIsInRole'] = $userIsInRole;
        }

        if (!$result['userIsInRole']) {
            return false;
        }

        return true;
    }

    public function profile($usernameInput) {
        $statement = self::$db->prepare("SELECT username, first_name, last_name FROM users WHERE username = ?");
        $statement->bind_param("s", $usernameInput);
        $statement->execute();
        $statement->bind_result($username, $first_name, $last_name);
        $profileInformation = array();
        while($statement->fetch()) {
            $profileInformation['username'] = $username;
            $profileInformation['first_name'] = $first_name;
            $profileInformation['last_name'] = $last_name;
        }

        return $profileInformation;
    }

    public function editProfile($currentUsername, $newFirstName, $newLastName, $newUsername, $newPassword){
        $statement = self::$db->prepare("SELECT COUNT(id) as userExists FROM users WHERE username = ?");
        $statement->bind_param("s", $newUsername);
        $statement->execute();
        $statement->bind_result($userExists);
        $result = array();
        while($statement->fetch()) {
            $result['userExists'] = $userExists;
        }

        $response = array();
        if ($result['userExists'] && $currentUsername != $newUsername) {
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
            $successfulEdit = $updateProfileStatement->affected_rows > 0;
            if($successfulEdit) {
                $response['statusCode'] = 200;
            } else {
                $response['statusCode'] = 400;
            }
        }

        return $response;
    }
}