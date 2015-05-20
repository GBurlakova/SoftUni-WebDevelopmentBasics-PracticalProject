<?php
class AllAlbumsModel extends BaseModel{
    public function getCategories() {
        $statement = self::$db->query(
            "SELECT * FROM categories ORDER BY id");
        $categories = array();
        while($category = $statement->fetch_assoc()) {
            array_push($categories, $category);
        }

        return $categories;
    }

    public function getAllAlbums($username, $startPage, $categoryId = null) {
        $userId = $this->getUserId($username);
        $query = "SELECT a.id, a.name, COUNT(al.album_id) as likes,
                  a.id NOT IN (SELECT album_id FROM album_likes WHERE user_id = ?) AS canBeLiked,
                  (SELECT count(p.id) FROM photos p WHERE p.album_id = a.id) AS photosCount,
                  c.name as category
                  FROM albums a LEFT OUTER JOIN album_likes al ON a.id = al.album_id
                  INNER JOIN categories c ON a.category_id = c.id
                  WHERE a.user_id <> ?";
        if($categoryId) {
            $query .= " AND category_id = ? GROUP BY id, name ORDER BY likes DESC, id";
            $statement = self::$db->prepare($query);
            $statement->bind_param("iii", $userId, $userId, $categoryId);
            $albumsCountBeforePaging = $this->estimateAlbumsCountBeforePaging($statement);

            $query .= " LIMIT ?, ?";
            $statement = self::$db->prepare($query);
            $startPageParam = DEFAULT_PAGE_SIZE * ($startPage - 1);
            $pageSizeParam = DEFAULT_PAGE_SIZE;
            $statement->bind_param("iiiii", $userId, $userId, $categoryId, $startPageParam, $pageSizeParam);
        } else {
            $query .= " GROUP BY id, name ORDER BY likes DESC, id";
            $statement = self::$db->prepare($query);
            $statement->bind_param("ii", $userId, $userId);
            $albumsCountBeforePaging = $this->estimateAlbumsCountBeforePaging($statement);
            $query .= " LIMIT ?, ?";

            $statement = self::$db->prepare($query);
            $startPageParam = DEFAULT_PAGE_SIZE * ($startPage - 1);
            $pageSizeParam = DEFAULT_PAGE_SIZE;
            $statement->bind_param("iiii", $userId, $userId, $startPageParam, $pageSizeParam);
        }

        $resultData = $this->prepareAlbumsResultData($statement, $albumsCountBeforePaging);
        return $resultData;
    }

    public function like($username, $albumId) {
        $userIdStatement = self::$db->prepare("SELECT id FROM users WHERE username = ?");
        $userIdStatement->bind_param("s", $username);
        $userIdStatement->execute();
        $userIdStatement->bind_result($id);
        $result = array();
        while($userIdStatement->fetch()) {
            $result['id'] = $id;
        }

        $userId = $result['id'];
        $statement = self::$db->prepare(
            "SELECT COUNT(id) as albumsCount FROM albums WHERE id = ? and user_id <> ?");
        $statement->bind_param("ii", $albumId, $userId);
        $statement->execute();
        $statement->bind_result($albumsCount);
        $result = array();
        while($statement->fetch()) {
            $result['albumsCount'] = $albumsCount;
        }

        $albumExists = $result['albumsCount'] > 0;
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

            $userId = $result['id'];
        }

        return $userId;
    }

    public function getAlbumPhotos($albumId) {
        $query = 'SELECT p.id, p.name, u.id as userId
                  FROM photos p
                  INNER JOIN albums a ON a.id = p.album_id
                  INNER JOIN users u ON a.user_id = u.id WHERE album_id = ?';
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $albumId);
        $statement->execute();
        $statement->bind_result($id, $name, $userId);
        $photos = array();
        while($statement->fetch()){
            $tempResult = array();
            $tempResult['id'] = $id;
            $tempResult['name'] = $name;
            $tempResult['userId'] = $userId;
            array_push($photos, $tempResult);
        }

        $photos = $this->getPhotosComments($photos);
        return $photos;
    }

    public function comment($commentText, $albumId, $username) {
        $userId = $this->getUserId($username);
        $query = 'INSERT INTO album_comments (text, album_id, user_id, date) VALUES(?, ?, ?, ?)';
        $statement = self::$db->prepare($query);
        $date = date('Y-m-d');
        $statement->bind_param('siis', $commentText, $albumId, $userId, $date);
        $statement->execute();
        return $statement->affected_rows > 0;
    }

    private function getPhotosComments($photos){
        for ($photo = 0; $photo < sizeof($photos); $photo++){
            $commentsQuery = self::$db->prepare(
                "SELECT c.id, c.text, u.username, c.date
            FROM photo_comments c INNER JOIN users u ON c.user_id = u.id
            WHERE c.photo_id = ?
            ORDER BY c.id");
            $photoId = $photos[$photo]['id'];
            $commentsQuery->bind_param("i", $photoId);
            $commentsQuery->execute();
            $commentsQuery->bind_result($id, $text, $username, $date);
            $comments = array();
            while($commentsQuery->fetch()) {
                $comment = array
                ('id' => $id, 'text' => $text, 'username' => $username, 'date' => $date );
                array_push($comments, $comment);
            }

            $photos[$photo]['comments'] = $comments;
        }

        return $photos;
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
        $albumsCount = 0;
        while($statement->fetch()) {
            $albumsCount++;
        }

        return $albumsCount;
    }

    private function prepareAlbumsResultData($statement, $albumsCountBeforePaging) {
        $statement->execute();
        $statement->bind_result($id, $name, $likes, $canBeLiked, $photosCount, $category);
        $albums = array();
        while($statement->fetch()) {
            $tempResult = array();
            $tempResult['id'] = $id;
            $tempResult['name'] = $name;
            $tempResult['likes'] = $likes;
            $tempResult['canBeLiked'] = $canBeLiked;
            $tempResult['photosCount'] = $photosCount;
            $tempResult['category'] = $category;
            array_push($albums, $tempResult);
        }

        $albums = $this->getAlbumsComments($albums);
        $pagesCount = ($albumsCountBeforePaging + DEFAULT_PAGE_SIZE - 1) / DEFAULT_PAGE_SIZE;
        $pagesCount = floor($pagesCount);
        $resultData = array('allAlbums' => $albums, 'pagesCount' => $pagesCount);
        return $resultData;
    }
}