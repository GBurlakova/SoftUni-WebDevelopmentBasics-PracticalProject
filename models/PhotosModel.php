<?php
class PhotosModel extends BaseModel{
    public function comment($commentText, $photoId, $username){
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
            $userIdQuery = self::$db->prepare("SELECT id FROM users WHERE username = ?");
            $userIdQuery->bind_param("s", $username);
            $userIdQuery->execute();
            $userId = $userIdQuery->get_result()->fetch_assoc()['id'];
        }
        return $userId;
    }
}