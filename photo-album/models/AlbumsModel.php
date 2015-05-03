<?php
class AlbumsModel extends BaseModel{
    public function all() {
        $statement = self::$db->query(
            "SELECT * FROM albums ORDER BY id");
        return $statement->fetch_all(MYSQLI_ASSOC);
    }

    public function create($todoItem, $userId = 1){
        if ($todoItem == '') {
            return false;
        }

        $statement = self::$db->prepare(
            "INSERT INTO photo-album (user_id, todo_item) VALUES(?, ?)");
        $statement->bind_param('is', $userId, $todoItem);
        $statement->execute();
        return $statement->affected_rows > 0;
    }

    public function delete($id) {
        $statement = self::$db->prepare(
            "DELETE FROM photo-album WHERE id = ?");
        $statement->bind_param("i", $id);
        $statement->execute();
        return $statement->affected_rows > 0;
    }

    public function like($username, $albumId){
        $userIdQuery = self::$db->prepare("SELECT id FROM users WHERE username = ?");
        $userIdQuery->bind_param("s", $username);
        $userIdQuery->execute();
        $userId = $userIdQuery->get_result()->fetch_assoc()['id'];

        $statement = self::$db->prepare(
            "SELECT count(id) as albumsCount FROM albums WHERE id = ? and is_public = 1 and user_id <> ?");
        $statement->bind_param("ii", $albumId, $userId);
        $statement->execute();
        $albumExists = $statement->get_result()->fetch_all(MYSQLI_ASSOC)[0]['albumsCount'] > 0;
        if($albumExists) {
            $statement = self::$db->prepare(
                "INSERT INTO album_likes (album_id, user_id) VALUES(?, ?)");
            $statement->bind_param("ii", $albumId, $userId);
            $statement->execute();
            return $statement->affected_rows > 0;
        } else {
            return false;
        }
    }
}