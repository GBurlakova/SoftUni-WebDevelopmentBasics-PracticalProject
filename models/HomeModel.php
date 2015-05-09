<?php
class HomeModel extends BaseModel {
    public function getCategories() {
        $statement = self::$db->query(
            "SELECT * FROM categories ORDER BY id");
        $categories = array();
        while($category = $statement->fetch_assoc()) {
            array_push($categories, $category);
        }

        return $categories;
    }

    public function getMostLikedAlbums($startPage, $categoryId = null) {
        $averageLikesCount = $this->estimateAverageLikesCount();
        $query = "SELECT a.id, a.name, COUNT(al.album_id) as likes,
                  (SELECT count(p.id) FROM photos p WHERE p.album_id = a.id) AS photosCount,
                  c.name as category
                  FROM albums a LEFT OUTER JOIN album_likes al ON a.id = al.album_id
                  LEFT OUTER JOIN categories c ON a.category_id = c.id";
        if($categoryId) {
            $query .= " AND category_id = ? GROUP BY id, name HAVING likes >= ? ORDER BY COUNT(al.album_id) DESC";
            $statement = self::$db->prepare($query);
            $statement->bind_param("ii", $categoryId, $averageLikesCount);
            $albumsCountBeforePaging = $this->estimateAlbumsCountBeforePaging($statement);

            $query .= " LIMIT ?, ?";
            $statement = self::$db->prepare($query);
            $startPageParam = DEFAULT_PAGE_SIZE * ($startPage - 1);
            $pageSizeParam = DEFAULT_PAGE_SIZE;
            $statement->bind_param("iiii", $categoryId, $averageLikesCount, $startPageParam, $pageSizeParam);
        } else {
            $query .= " GROUP BY id, name HAVING likes >= ? ORDER BY COUNT(al.album_id) DESC";
            $statement = self::$db->prepare($query);
            $statement->bind_param("i", $averageLikesCount);
            $albumsCountBeforePaging = $this->estimateAlbumsCountBeforePaging($statement);

            $query .= " LIMIT ?, ?";
            $statement = self::$db->prepare($query);
            $startPageParam = DEFAULT_PAGE_SIZE * ($startPage - 1);
            $pageSizeParam = DEFAULT_PAGE_SIZE;
            $statement->bind_param("iii", $averageLikesCount, $startPageParam, $pageSizeParam);
        }

         $resultData = $this->prepareResultData($statement, $albumsCountBeforePaging);
         return $resultData;
    }

    public function getAlbumPhotos($albumId) {
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

    private function getPhotosComments($photos) {
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

    private function estimateAverageLikesCount() {
        $likesQuery = self::$db->query("SELECT IFNULL(COUNT(album_id) / COUNT(id), 0)  as averageLikes FROM albums INNER JOIN album_likes ON id = album_id");
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
        $albums = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $albums = $this->getAlbumsComments($albums);
        $pagesCount = ($albumsCountBeforePaging + DEFAULT_PAGE_SIZE - 1) / DEFAULT_PAGE_SIZE;
        $pagesCount = floor($pagesCount);
        $resultData = array('allAlbums' => $albums, 'pagesCount' => $pagesCount);
        return $resultData;
    }
}