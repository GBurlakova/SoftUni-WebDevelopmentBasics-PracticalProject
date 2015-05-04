<?php
class HomeModel extends BaseModel {
    public function getCategories()
    {
        $statement = self::$db->query(
            "SELECT * FROM categories ORDER BY id");
        $categories = array();
        while($category = $statement->fetch_assoc()) {
            array_push($categories, $category);
        }

        return $categories;
    }

    public function getMostLikedAlbums($username, $startPage = 1, $categoryId = null) {
        $userId = $this->getUserId($username);
        $averageLikesCount = $this->estimateAverageLikesCount();
        $query = "SELECT a.id, a.name, COUNT(al.album_id) as likes, a.id
                  NOT IN (SELECT album_id FROM album_likes WHERE user_id = ?)
                  AS canBeLiked FROM albums a LEFT OUTER JOIN album_likes al ON a.id = al.album_id
                  WHERE is_public = 1 AND a.user_id <> ?";
        if($categoryId) {
            $query .= " AND category_id = ? GROUP BY id, name HAVING likes >= ? ORDER BY COUNT(al.album_id) DESC";
            $statement = self::$db->prepare($query);
            $statement->bind_param("iiii", $userId, $userId, $categoryId, $averageLikesCount);
            $albumsCountBeforePaging = $this->estimateAlbumsCountBeforePaging($statement);

            $query .= " LIMIT ?, ?";
            $statement = self::$db->prepare($query);
            $startPageParam = $startPage - 1;
            $pageSizeParam = DEFAULT_PAGE_SIZE;
            $statement->bind_param("iiiiii", $userId, $userId, $categoryId, $averageLikesCount, $startPageParam, $pageSizeParam);
        } else {
            $query .= " GROUP BY id, name HAVING likes >= ? ORDER BY COUNT(al.album_id) DESC";
            $statement = self::$db->prepare($query);
            $statement->bind_param("iii", $userId, $userId, $averageLikesCount);
            $albumsCountBeforePaging = $this->estimateAlbumsCountBeforePaging($statement);

            $query .= " LIMIT ?, ?";
            $statement = self::$db->prepare($query);
            $startPageParam = $startPage - 1;
            $pageSizeParam = DEFAULT_PAGE_SIZE;
            $statement->bind_param("iiiii", $userId, $userId, $averageLikesCount, $startPageParam, $pageSizeParam);
        }

         $resultData = $this->prepareResultData($statement, $albumsCountBeforePaging);
         return $resultData;
    }

    public function getPublicAlbums($username, $startPage, $categoryId = null) {
        $userId = $this->getUserId($username);
        $query = "SELECT a.id, a.name, COUNT(al.album_id) as likes,
                  a.id NOT IN (SELECT album_id FROM album_likes WHERE user_id = ?) AS canBeLiked
                  FROM albums a left outer JOIN album_likes al ON a.id = al.album_id
                  WHERE is_public = 1 AND a.user_id <> ?";
        if($categoryId) {
            $query .= " AND category_id = ? GROUP BY id, name ORDER BY likes DESC";
            $statement = self::$db->prepare($query);
            $statement->bind_param("iii", $userId, $userId, $categoryId);
            $albumsCountBeforePaging = $this->estimateAlbumsCountBeforePaging($statement);

            $query .= " LIMIT ?, ?";
            $statement = self::$db->prepare($query);
            $startPageParam = DEFAULT_PAGE_SIZE * ($startPage - 1);
            $pageSizeParam = DEFAULT_PAGE_SIZE;
            $statement->bind_param("iiiii", $userId, $userId, $categoryId, $startPageParam, $pageSizeParam);
        } else {
            $query .= " GROUP BY id, name ORDER BY likes DESC";
            $statement = self::$db->prepare($query);
            $statement->bind_param("ii", $userId, $userId);
            $albumsCountBeforePaging = $this->estimateAlbumsCountBeforePaging($statement);

            $query .= " LIMIT ?, ?";
            $statement = self::$db->prepare($query);
            $startPageParam = DEFAULT_PAGE_SIZE * ($startPage - 1);
            $pageSizeParam = DEFAULT_PAGE_SIZE;
            $statement->bind_param("iiii", $userId, $userId, $startPageParam, $pageSizeParam);
        }

        $resultData = $this->prepareResultData($statement, $albumsCountBeforePaging);
        return $resultData;
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
            $commentsQuery->bind_result($id, $text, $username, $date);
            $comments = array();
            while($commentsQuery->fetch()) {
                $comment = array
                ('id' => $id, 'text' => $text, 'username' => $username, 'date' => $date );
                array_push($comments, $comment);
            }

            $albums[$album]['comments'] = $comments;
        }

        return $albums;
    }

    private function getUserId($username) {
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

    private function estimateAverageLikesCount() {
        $likesQuery = self::$db->query("SELECT IFNULL(COUNT(album_id) / COUNT(id), 0)  as averageLikes FROM albums INNER JOIN album_likes ON id = album_id WHERE is_public = 1");
        $averageLikesCount = $likesQuery->fetch_assoc()['averageLikes'];
        return $averageLikesCount;
    }

    private function estimateAlbumsCountBeforePaging($statement) {
        $statement->execute();
        $albums = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $albumsCount = sizeof($albums);
        return $albumsCount;
    }

    private function prepareResultData($statement, $albumsCountBeforePaging) {
        $statement->execute();
        $statement->bind_result($id, $name, $likes, $canBeLiked);
        $albums = array();
        while($statement->fetch()) {
            $album = array
            ('id' => $id, 'name' => $name, 'likes' => $likes, 'canBeLiked' => $canBeLiked );
            array_push($albums, $album);
        }

        $albums = $this->getAlbumsComments($albums);
        $pagesCount = ($albumsCountBeforePaging + DEFAULT_PAGE_SIZE - 1) / DEFAULT_PAGE_SIZE;
        $pagesCount = floor($pagesCount);
        $resultData = array('albums' => $albums, 'pagesCount' => $pagesCount);
        return $resultData;
    }
}