<?php
class PhotosModel extends BaseModel{
    public function comment($commentText, $photoId, $username) {
        $userId = $this->getUserId($username);
        $query = 'INSERT INTO photo_comments (text, photo_id, user_id, date) VALUES(?, ?, ?, ?)';
        $statement = self::$db->prepare($query);
        $date = date('Y-m-d');
        $statement->bind_param('siis', $commentText, $photoId, $userId, $date);
        $statement->execute();
        return $statement->affected_rows > 0;
    }

    public function getUserId($username) {
        if($username == "") {
            $userId = "";
        } else {
            $userIdStatement = self::$db->prepare("SELECT id FROM users WHERE username = ?");
            $userIdStatement->bind_param("s", $username);
            $userIdStatement->execute();
            $userIdStatement->bind_result($id);
            $result = array();
            while($userIdStatement->fetch()) {
                $result['id'] = $id;
            }

            $userId = $result['id'];;
        }

        return $userId;
    }

    public function getPhotoInformation($photoId){
        $query = 'SELECT p.name, u.id as userId FROM photos p
                  INNER JOIN albums a ON a.id = p.album_id
                  INNER JOIN users u ON a.user_id = u.id
                  WHERE p.id = ?';

        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $photoId);
        $statement->execute();
        $statement->bind_result($name, $userId);
        $photo = array();
        while($statement->fetch()) {
            $photo['name'] = $name;
            $photo['userId'] = $userId;
        }

        return $photo;
    }
}