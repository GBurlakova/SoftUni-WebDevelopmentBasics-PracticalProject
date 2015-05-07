<?php
class UserAlbumsModel extends BaseModel{
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

    public function getUserAlbums($startPage, $username) {
        $query = "SELECT a.id, a.name, COUNT(al.album_id) as likes
            FROM albums a INNER JOIN users u ON a.user_id = u.id
            LEFT OUTER JOIN album_likes al ON a.id = al.album_id
            WHERE u.username = ?
            GROUP BY a.id, a.name
            ORDER BY a.id";
        $statement = self::$db->prepare($query);
        $statement->bind_param("s", $username);
        $statement->execute();
        $userAlbums = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $albumsCountBeforePaging = sizeof($userAlbums);
        $query .= " LIMIT ?, ?";
        $statement = self::$db->prepare($query);
        $startPageParam = DEFAULT_PAGE_SIZE * ($startPage - 1);
        $pageSizeParam = DEFAULT_PAGE_SIZE;
        $statement->bind_param("sii", $username, $startPageParam, $pageSizeParam);
        $statement->execute();
        $userAlbums = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $pagesCount = ($albumsCountBeforePaging + DEFAULT_PAGE_SIZE - 1) / DEFAULT_PAGE_SIZE;
        $pagesCount = floor($pagesCount);
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

        $resultData = array('userAlbums' => $userAlbums, 'pagesCount' => $pagesCount);
        return $resultData;
    }

    public function createNewAlbum($albumName, $userId, $categoryId){
        $query = 'INSERT INTO albums (name, user_id, category_id) VALUES(?, ?, ?)';
        $statement = self::$db->prepare($query);
        $statement->bind_param('sii', $albumName, $userId, $categoryId);
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
            "SELECT count(id) as albumsCount FROM allAlbums WHERE id = ? and user_id <> ?");
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

    public function getAlbums($username){
        $userId = $this->getUserId($username);
        $query = "SELECT a.id, a.name
                  FROM albums a
                  WHERE a.user_id = ?";
        $statement = self::$db->prepare($query);
        $statement->bind_param("i", $userId);
        $statement->execute();
        $statement->bind_result($id, $name);
        $albums = array();
        while($statement->fetch()) {
            $album = array
            ('id' => $id, 'name' => $name);
            array_push($albums, $album);
        }
        return $albums;
    }

    public function addPhoto($photoName, $albumId, $username){
        $userId = $this->getUserId($username);
        $albumOwnerIdQuery = 'SELECT user_id FROM albums WHERE id = ?';
        $albumOwnerIdStatement = self::$db->prepare($albumOwnerIdQuery);
        $albumOwnerIdStatement->bind_param('i', $albumId);
        $albumOwnerIdStatement->execute();
        $albumOwnerId = $albumOwnerIdStatement->get_result()->fetch_assoc()['user_id'];
        $currentUserIsOwner = $albumOwnerId == $userId;
        if($currentUserIsOwner) {
            $query = 'INSERT INTO photos (name, album_id) VALUES(?, ?)';
            $statement = self::$db->prepare($query);
            $statement->bind_param('si', $photoName, $albumId);
            $statement->execute();
            return $statement->affected_rows > 0;
        } else {
            return false;
        }
    }

    public function getAlbumPhotos($albumId, $username){
        $userId = $this->getUserId($username);
        $albumOwnerIdQuery = 'SELECT user_id FROM albums WHERE id = ?';
        $albumOwnerIdStatement = self::$db->prepare($albumOwnerIdQuery);
        $albumOwnerIdStatement->bind_param('i', $albumId);
        $albumOwnerIdStatement->execute();
        $albumOwnerId = $albumOwnerIdStatement->get_result()->fetch_assoc()['user_id'];
        $currentUserIsOwner = $albumOwnerId == $userId;
        if($currentUserIsOwner){
            $query = 'SELECT p.id, p.name, u.id as userId
                  FROM photos p
                  INNER JOIN albums a ON a.id = p.album_id
                  INNER JOIN users u ON a.user_id = u.id WHERE album_id = ?';
            $statement = self::$db->prepare($query);
            $statement->bind_param('i', $albumId);
            $statement->execute();
            $photos = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
            $photos = $this->getPhotosComments($photos);
            return $photos;
        } else {
            return false;
        }
    }

    public function comment($commentText, $albumsId, $username){
        $userId = $this->getUserId($username);
        $query = 'INSERT INTO album_comments (text, album_id, user_id, date) VALUES(?, ?, ?, ?)';
        $statement = self::$db->prepare($query);
        $date = date('Y-m-d');
        $statement->bind_param('siis', $commentText, $albumsId, $userId, $date);
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
}