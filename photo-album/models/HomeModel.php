<?php
class HomeModel extends BaseModel {
    public function getCategories()
    {
        $statement = self::$db->query(
            "SELECT * FROM categories ORDER BY id");
        return $statement->fetch_all(MYSQLI_ASSOC);
    }

    public function getMostLikedAlbums($categoryId = null) {
        $likesQuery = self::$db->query("SELECT SUM(likes) / COUNT(id)  as averageLikes FROM albums WHERE is_public = 1");
        $averageLikesCount = $likesQuery->fetch_assoc()['averageLikes'];

        $query = "SELECT * FROM albums WHERE is_public = 1 and likes > " . $averageLikesCount;
        if($categoryId) {
            $query .= " AND category_id = ? ORDER BY likes";
            $statement = self::$db->prepare($query);
            $statement->bind_param("i", $categoryId);
        } else {
            $query .= "ORDER BY likes";
            $statement = self::$db->prepare($query);
        }

        $statement->execute();
        return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPublicAlbums($categoryId = null) {
        $query = "SELECT * FROM albums WHERE is_public = 1";
        if($categoryId) {
            $query .= " AND category_id = ? ORDER BY likes";
            $statement = self::$db->prepare($query);
            $statement->bind_param("i", $categoryId);
        } else {
            $query .= "ORDER BY likes";
            $statement = self::$db->prepare($query);
        }

        $statement->execute();
        return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}