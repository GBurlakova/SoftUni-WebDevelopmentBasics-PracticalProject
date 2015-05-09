<?php
class AdminModel extends BaseModel {
    public function getAllAlbums($startPage) {
        $query = "SELECT a.id, a.name, COUNT(al.album_id) as likes
                  FROM albums a left outer JOIN album_likes al ON a.id = al.album_id";
        $query .= " GROUP BY id, name ORDER BY likes DESC";
        $statement = self::$db->prepare($query);
        $albumsCountBeforePaging = $this->estimateAlbumsCountBeforePaging($statement);
        $query .= " LIMIT ?, ?";
        $statement = self::$db->prepare($query);
        $startPageParam = DEFAULT_PAGE_SIZE * ($startPage - 1);
        $pageSizeParam = DEFAULT_PAGE_SIZE;
        $statement->bind_param("ii", $startPageParam, $pageSizeParam);
        $statement->execute();
        $albums = $statement->get_result()->fetch_all(MYSQL_ASSOC);
        $albums = $this->getAlbumsComments($albums);
        $pagesCount = ($albumsCountBeforePaging + DEFAULT_PAGE_SIZE - 1) / DEFAULT_PAGE_SIZE;
        $pagesCount = floor($pagesCount);
        $resultData = array('albums' => $albums, 'pagesCount' => $pagesCount);
        return $resultData;
    }

    private function estimateAlbumsCountBeforePaging($statement) {
        $statement->execute();
        $albums = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $albumsCount = sizeof($albums);
        return $albumsCount;
    }

    public function getAlbumPhotos($albumId){
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
    }

    public function getCategories()
    {
        $statement = self::$db->query(
            "SELECT * FROM categories ORDER BY id");
        $categories = $statement->fetch_all(MYSQL_ASSOC);
        return $categories;
    }

    public function getCategoryName($categoryId){
        $query = "SELECT name FROM categories WHERE id = ?";
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $categoryId);
        $statement->execute();
        $categoryName = $statement->get_result()->fetch_assoc()['name'];
        return $categoryName;
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
            $comments = $commentsQuery->get_result()->fetch_all(MYSQL_ASSOC);
            $photos[$photo]['comments'] = $comments;
        }
        return $photos;
    }

    public function editCategory($categoryId, $categoryName){
        $statement = self::$db->prepare("SELECT COUNT(id) FROM categories WHERE name = ?");
        $statement->bind_param("s", $categoryName);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $response = array();
        if ($result['COUNT(id)']) {
            $response['statusCode'] = 400;
            $response['message'] = 'Category name is already taken';
        } else {
            $query = 'UPDATE categories SET name = ? WHERE id = ?';
            $statement = self::$db->prepare($query);
            $statement->bind_param('si', $categoryName, $categoryId);
            $statement->execute();
            $response = array();
            if($statement->affected_rows > 0) {
                $response['statusCode'] = 200;
            } else {
                $response['statusCode'] = 400;
            }
        }

        return $response;
    }

    public function deleteCategory($categoryId){
        $statement = self::$db->prepare(
            "SELECT COUNT(c.id) as albumsCount FROM categories c INNER JOIN albums a ON c.id = a.category_id WHERE c.id = ?");
        $statement->bind_param("i", $categoryId);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $response = array();
        if ($result['albumsCount']) {
            $response['statusCode'] = 400;
            $response['message'] = 'Attempt to delete non-empty category. Please delete all albums first.';
        } else {
            $query = "DELETE FROM categories WHERE id = ?";
            $statement = self::$db->prepare($query);
            $statement->bind_param('i', $categoryId);
            $statement->execute();
            $response = array();
            if($statement->affected_rows > 0) {
                $response['statusCode'] = 200;
            } else {
                $response['statusCode'] = 400;
            }
        }

        return $response;
    }

    public function newCategory($categoryName){
        $statement = self::$db->prepare(
            "SELECT COUNT(c.id) as categoriesCount FROM categories c WHERE c.name = ?");
        $statement->bind_param("s", $categoryName);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $response = array();
        if ($result['categoriesCount']) {
            $response['statusCode'] = 400;
            $response['message'] = 'Attempt to delete non-empty category. Please delete all albums first.';
        } else {
            $query = "INSERT INTO categories (name) VALUES(?)";
            $statement = self::$db->prepare($query);
            $statement->bind_param('s', $categoryName);
            $statement->execute();
            if($statement->affected_rows > 0){
                $response['statusCode'] = 200;
            } else {
                $response['statusCode'] = 400;
            }
        }

        return $response;
    }

    public function deleteAlbum($albumId){
        $statement = self::$db->prepare(
            "SELECT COUNT(c.id) as commentsCount
            FROM albums a LEFT OUTER JOIN album_comments c ON a.id = c.album_id
            WHERE a.id = ?");
        $statement->bind_param("i", $albumId);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $response = array();
        if($result['commentsCount'] > 0) {
            $response['statusCode'] = 400;
            $response['message'] =
                'Attempt to delete album with comments. Please delete corresponding comments first';
        } else {
            $statement = self::$db->prepare(
                "SELECT COUNT(p.id) as photosCount
            FROM albums a LEFT OUTER JOIN photos p ON a.id = p.album_id
            WHERE a.id = ?");
            $statement->bind_param("i", $albumId);
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            if($result['photosCount'] > 0) {
                $response['statusCode'] = 400;
                $response['message'] =
                    'Attempt to delete album with photos. Please delete corresponding photos first';
            } else {
                $deleteLikesQuery = 'DELETE FROM album_likes WHERE album_id = ?';
                $statement = self::$db->prepare($deleteLikesQuery);
                $statement->bind_param('i', $albumId);
                $statement->execute();

                $query = 'DELETE FROM albums WHERE id = ?';
                $statement = self::$db->prepare($query);
                $statement->bind_param('i', $albumId);
                $statement->execute();
                $response = array();
                if($statement->affected_rows > 0) {
                    $response['statusCode'] = 200;
                } else {
                    $response['statusCode'] = 400;
                }
            }
        }

        return $response;
    }

    public function getAlbumName($albumId){
        $query = "SELECT name FROM albums WHERE id = ?";
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $albumId);
        $statement->execute();
        $albumName = $statement->get_result()->fetch_assoc()['name'];
        return $albumName;
    }

    public function editAlbum($albumId, $albumName){
        $query = 'UPDATE albums SET name = ? WHERE id = ?';
        $statement = self::$db->prepare($query);
        $statement->bind_param('si', $albumName, $albumId);
        $statement->execute();
        $response = array();
        if($statement->affected_rows > 0) {
            $response['statusCode'] = 200;
        } else {
            $response['statusCode'] = 400;
        }

        return $response;
    }
}