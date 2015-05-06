<?php
class AlbumsModel extends BaseModel{
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

    public function all($username) {
        $statement = self::$db->prepare(
            "SELECT a.id, a.name, COUNT(al.album_id) as likes
            FROM albums a INNER JOIN users u ON a.user_id = u.id
            LEFT OUTER JOIN album_likes al ON a.id = al.album_id
            WHERE u.username = ?
            GROUP BY a.id, a.name
            ORDER BY a.id");
        $statement->bind_param("s", $username);
        $statement->execute();
        $userAlbums = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        for ($album = 0; $album < sizeof($userAlbums); $album++){
            $commentsQuery = self::$db->prepare(
                "SELECT c.id, c.text, u.username, c.date
            FROM album_comments c INNER JOIN users u ON c.user_id = u.id
            INNER JOIN albums a ON a.id = c.album_id
            WHERE a.id = ?
            ORDER BY a.id");
            $commentsQuery->bind_param("i", $userAlbums[$album]['id']);
            $commentsQuery->execute();
            $comments = $commentsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
            $userAlbums[$album]['comments'] = $comments;
        }

        return $userAlbums;
    }

    public function newAlbum($albumName, $isPublic, $userId, $categoryId){
        $query = 'INSERT INTO albums (name, is_public, user_id, category_id) VALUES(?, ?, ?, ?)';
        $statement = self::$db->prepare($query);
        $statement->bind_param('siii', $albumName, $isPublic, $userId, $categoryId);
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