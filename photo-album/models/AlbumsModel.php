<?php
class AlbumsModel extends BaseModel{
    // Nothing works !!!
    public function getAll() {
        $statement = self::$db->query(
            "SELECT * FROM photo-album ORDER BY id");
        return $statement->fetch_all(MYSQLI_ASSOC);
    }

    public function addTodoItem($todoItem, $userId = 1){
        if ($todoItem == '') {
            return false;
        }

        $statement = self::$db->prepare(
            "INSERT INTO photo-album (user_id, todo_item) VALUES(?, ?)");
        $statement->bind_param('is', $userId, $todoItem);
        $statement->execute();
        return $statement->affected_rows > 0;
    }

    public function deleteTodo($id) {
        $statement = self::$db->prepare(
            "DELETE FROM photo-album WHERE id = ?");
        $statement->bind_param("i", $id);
        $statement->execute();
        return $statement->affected_rows > 0;
    }
}