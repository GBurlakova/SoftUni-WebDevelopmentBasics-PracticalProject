<?php
class HomeModel extends BaseModel {
    public function getCategories()
    {
        $statement = self::$db->query(
            "SELECT * FROM categories ORDER BY id");
        return $statement->fetch_all(MYSQLI_ASSOC);
    }

    public function getMostLikedAlbums($username, $categoryId = null) {
        if($username == "") {
            $userId = "";
        } else {
            $userIdQuery = self::$db->prepare("SELECT id FROM users WHERE username = ?");
            $userIdQuery->bind_param("s", $username);
            $userIdQuery->execute();
            $userId = $userIdQuery->get_result()->fetch_assoc()['id'];
        }

        $likesQuery = self::$db->query("SELECT IFNULL(COUNT(album_id) / COUNT(id), 0)  as averageLikes FROM albums INNER JOIN album_likes ON id = album_id WHERE is_public = 1");
        $averageLikesCount = $likesQuery->fetch_assoc()['averageLikes'];

        $query = "SELECT a.id, a.name, COUNT(al.album_id) as likes, a.id
                  NOT IN (SELECT album_id FROM album_likes WHERE user_id = ?)
                  AS canBeLiked FROM albums a LEFT OUTER JOIN album_likes al ON a.id = al.album_id
                  WHERE is_public = 1 AND a.user_id <> ?";
        if($categoryId) {
            $query .= " AND category_id = ? GROUP BY id, name HAVING likes >= ? ORDER BY COUNT(al.album_id) DESC";
            $statement = self::$db->prepare($query);
            $statement->bind_param("iiii", $userId, $userId, $categoryId, $averageLikesCount);
        } else {
            $query .= " GROUP BY id, name HAVING likes >= ? ORDER BY COUNT(al.album_id) DESC";
            $statement = self::$db->prepare($query);
            $statement->bind_param("iii", $userId, $userId, $averageLikesCount);
        }

        $statement->execute();
        $albums = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $albums = $this->getAlbumsComments($albums);
        return $albums;
    }

    public function getPublicAlbums($username, $categoryId = null) {
        if($username == "") {
            $userId = "";
        } else {
            $userIdQuery = self::$db->prepare("SELECT id FROM users WHERE username = ?");
            $userIdQuery->bind_param("s", $username);
            $userIdQuery->execute();
            $userId = $userIdQuery->get_result()->fetch_assoc()['id'];
        }

        $query = "SELECT a.id, a.name, COUNT(al.album_id) as likes, a.id NOT IN (SELECT album_id FROM album_likes WHERE user_id = ?) AS canBeLiked FROM albums a left outer JOIN album_likes al ON a.id = al.album_id WHERE is_public = 1 AND a.user_id <> ?";
        if($categoryId) {
            $query .= " AND category_id = ? GROUP BY id, name ORDER BY likes DESC";
            $statement = self::$db->prepare($query);
            $statement->bind_param("iii", $userId, $userId, $categoryId);
        } else {
            $query .= " GROUP BY id, name ORDER BY likes DESC";
            $statement = self::$db->prepare($query);
            $statement->bind_param("ii", $userId, $userId);
        }

        $statement->execute();
        $albums = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $albums = $this->getAlbumsComments($albums);
        return $albums;
    }

    private function getAlbumsComments($albums) {
        for ($album = 0; $album < sizeof($albums); $album++){
            $commentsQuery = self::$db->prepare(
                "SELECT c.id, c.text, u.username, c.date
            FROM album_comments c INNER JOIN users u ON c.user_id = u.id
            INNER JOIN albums a ON a.id = c.album_id
            WHERE a.id = ?
            ORDER BY a.id");
            $commentsQuery->bind_param("i", $albums[$album]['id']);
            $commentsQuery->execute();
            $comments = $commentsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
            $albums[$album]['comments'] = $comments;
        }

        return $albums;
    }
}