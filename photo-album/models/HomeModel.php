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

        $query = "SELECT a.id, a.name, COUNT(al.album_id) as likes, a.id NOT IN (SELECT album_id FROM album_likes WHERE user_id = ?) AS canBeLiked FROM albums a left outer JOIN album_likes al ON a.id = al.album_id WHERE is_public = 1 AND a.user_id <> ? AND likes >= " . $averageLikesCount;
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
        return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
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
        return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}