<?php
class AdminModel extends BaseModel {
    public function getAllAlbums($startPage) {
        $query = "SELECT a.id, a.name, COUNT(al.album_id) as likes,
                  (SELECT count(p.id) FROM photos p WHERE p.album_id = a.id) AS photosCount,
                  c.name as category
                  FROM albums a LEFT OUTER JOIN album_likes al ON a.id = al.album_id
                  LEFT OUTER JOIN categories c ON a.category_id = c.id";
        $query .= " GROUP BY id, name ORDER BY likes DESC";
        $statement = self::$db->prepare($query);
        $albumsCountBeforePaging = $this->estimateAlbumsCountBeforePaging($statement);
        $query .= " LIMIT ?, ?";
        $statement = self::$db->prepare($query);
        $startPageParam = DEFAULT_PAGE_SIZE * ($startPage - 1);
        $pageSizeParam = DEFAULT_PAGE_SIZE;
        $statement->bind_param("ii", $startPageParam, $pageSizeParam);
        $statement->execute();
        $statement->bind_result($id, $name, $likes, $photosCount, $category);
        $albums = array();
        while ($statement->fetch()) {
            $tempResult = array();
            $tempResult['id'] = $id;
            $tempResult['name'] = $name;
            $tempResult['likes'] = $likes;
            $tempResult['photosCount'] = $photosCount;
            $tempResult['category'] = $category;
            array_push($albums, $tempResult);
        }

        $albums = $this->getAlbumsComments($albums);
        $pagesCount = ($albumsCountBeforePaging + DEFAULT_PAGE_SIZE - 1) / DEFAULT_PAGE_SIZE;
        $pagesCount = floor($pagesCount);
        $resultData = array('albums' => $albums, 'pagesCount' => $pagesCount);
        return $resultData;
    }

    private function estimateAlbumsCountBeforePaging($statement) {
        $statement->execute();
        $statement->bind_result($id, $name, $likes, $photosCount, $category);
        $albums = array();
        while ($statement->fetch()) {
            $tempResult = array();
            $tempResult['id'] = $id;
            $tempResult['name'] = $name;
            $tempResult['likes'] = $likes;
            $tempResult['photosCount'] = $photosCount;
            $tempResult['category'] = $category;
            array_push($albums, $tempResult);
        }

        $albumsCount = sizeof($albums);
        return $albumsCount;
    }

    public function getAlbumPhotos($albumId) {
        $query = 'SELECT p.id, p.name, u.id as userId, a.id as albumId
                  FROM photos p
                  INNER JOIN albums a ON a.id = p.album_id
                  INNER JOIN users u ON a.user_id = u.id WHERE album_id = ?';
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $albumId);
        $statement->execute();
        $statement->bind_result($id, $name, $userId, $albumId);
        $photos = array();
        while($statement->fetch()) {
            $tempResult = array();
            $tempResult['id'] = $id;
            $tempResult['name'] = $name;
            $tempResult['userId'] = $userId;
            $tempResult['albumId'] = $albumId;
            array_push($photos, $tempResult);
        }

        $photos = $this->getPhotosComments($photos);
        return $photos;
    }

    public function getCategories() {
        $statement = self::$db->query(
            "SELECT id, name FROM categories ORDER BY id");
        $categories = array();
        while($category = $statement->fetch_assoc()) {
            array_push($categories, $category);
        }

        return $categories;
    }

    public function getCategoryName($categoryId) {
        $query = "SELECT name FROM categories WHERE id = ?";
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $categoryId);
        $statement->execute();
        $statement->bind_result($name);
        $categories = array();
        while($statement->fetch()) {
            $categories['name'] = $name;
        }

        $categoryName = $categories['name'];
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

    private function getPhotosComments($photos) {
        for ($photo = 0; $photo < sizeof($photos); $photo++){
            $query = "SELECT c.id, c.text, u.username, c.date
            FROM photo_comments c INNER JOIN users u ON c.user_id = u.id
            WHERE c.photo_id = ?
            ORDER BY c.id";
            $statement = self::$db->prepare($query);
            $photoId = $photos[$photo]['id'];
            $statement->bind_param("i", $photoId);
            $statement->execute();
            $statement->bind_result($id, $text, $username, $date);
            $comments = array();
            while($statement->fetch()) {
                $tempResult = array();
                $tempResult['id'] = $id;
                $tempResult['text'] = $text;
                $tempResult['username'] = $username;
                $tempResult['date'] = $date;
                array_push($comments, $tempResult);
            }

            $photos[$photo]['comments'] = $comments;
        }
        return $photos;
    }

    public function editCategory($categoryId, $categoryName) {
        $statement = self::$db->prepare("SELECT COUNT(id) as categoryExists FROM categories WHERE name = ?");
        $statement->bind_param("s", $categoryName);
        $statement->execute();
        $statement->bind_result($categoryExists);
        $result = array();
        while($statement->fetch()) {
            $result['categoryExists'] = $categoryExists;
        }

        $response = array();
        if ($result['categoryExists']) {
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

    public function deleteCategory($categoryId) {
        $statement = self::$db->prepare(
            "SELECT COUNT(c.id) as albumsCount FROM categories c INNER JOIN albums a ON c.id = a.category_id WHERE c.id = ?");
        $statement->bind_param("i", $categoryId);
        $statement->execute();
        $statement->bind_result($albumsCount);
        $result = array();
        while($statement->fetch()) {
            $result['albumsCount'] = $albumsCount;
        }

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

    public function newCategory($categoryName) {
        $statement = self::$db->prepare(
            "SELECT COUNT(c.id) as categoriesCount FROM categories c WHERE c.name = ?");
        $statement->bind_param("s", $categoryName);
        $statement->bind_result($categoriesCount);
        $statement->execute();
        $result = array();
        while($statement->fetch()) {
            $result['categoriesCount']  = $categoriesCount;
        }

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

    public function deleteAlbum($albumId) {
        $statement = self::$db->prepare(
            "SELECT COUNT(c.id) as commentsCount
            FROM albums a LEFT OUTER JOIN album_comments c ON a.id = c.album_id
            WHERE a.id = ?");
        $statement->bind_param("i", $albumId);
        $statement->execute();
        $statement->bind_result($commentsCount);
        $result = array();
        while($statement->fetch()) {
            $result['commentsCount'] = $commentsCount;
        }

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
            $statement->bind_result($photosCount);
            $result = array();
            while($statement->fetch()) {
                $result['photosCount'] = $photosCount;
            }

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

    public function getAlbumName($albumId) {
        $query = "SELECT name FROM albums WHERE id = ?";
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $albumId);
        $statement->execute();
        $statement->bind_result($name);
        $result = array();
        while($statement->fetch()) {
            $result['name'] = $name;
        }

        $albumName = $result['name'];
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

    public function getAlbumId($photoId){
        $query = "SELECT album_id FROM albums a WHERE id = ?";
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $photoId);
        $statement->execute();
        $statement->bind_result($album_id);
        $result = array();
        while ($statement->fetch()) {
            $result['album_id'] = $album_id;
        }

        $albumId = $result['album_id'];
        return $albumId;
    }

    public function getAlbumComment($commendId){
        $query = "SELECT c.id, c.text, u.username, c.date
            FROM album_comments c INNER JOIN users u ON c.user_id = u.id
            INNER JOIN albums a ON a.id = c.album_id
            WHERE c.id = ?";
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $commendId);
        $statement->execute();
        $statement->bind_result($id, $text, $username, $date);
        $comment = array();
        while($statement->fetch()) {
            $comment['id'] = $id;
            $comment['text'] = $text;
            $comment['username'] = $username;
            $comment['date'] = $date;
        }

        return $comment;
    }

    public function deletePhoto($photoId){
        $commentCountQuery = 'SELECT COUNT(c.id) as commentsCount
                  FROM photos p LEFT OUTER JOIN photo_comments c ON p.id = c.photo_id
                  WHERE p.id = ?';
        $statement = self::$db->prepare($commentCountQuery);
        $statement->bind_param('i', $photoId);
        $statement->execute();
        $statement->bind_result($commentsCount);
        $result = array();
        while($statement->fetch()) {
            $result['commentsCount'] = $commentsCount;
        }

        $response = array();
        if($result['commentsCount'] > 0) {
            $response['statusCode'] = 400;
            $response['message'] = 'Attempt to delete photo with comments.
                                    Please delete corresponding comments first.';
        } else {
            $userIdQuery = 'SELECT u.id as userId, p.name as photoName FROM photos p
                        INNER JOIN albums a ON a.id = p.album_id
                        INNER JOIN users u ON u.id = a.user_id
                        WHERE p.id = ?';
            $statement = self::$db->prepare($userIdQuery);
            $statement->bind_param('i', $photoId);
            $statement->execute();
            $statement->bind_result($userId, $photoName);
            $photoInfo = array();
            while($statement->fetch()) {
                $photoInfo['userId'] = $userId;
                $photoInfo['photoName'] = $photoName;
            }

            $deletePhotoQuery = 'DELETE FROM photos WHERE id = ?';
            $statement = self::$db->prepare($deletePhotoQuery);
            $statement->bind_param('i', $photoId);
            $statement->execute();
            if($statement->affected_rows > 0) {
                $response['photoInfo'] = $photoInfo;
                $response['statusCode'] = 200;
            } else {
                $response['statusCode'] = 400;
            }
        }

        return $response;
    }

    public function editAlbumComment($commentId, $commentText){
        $query = 'UPDATE album_comments SET text = ? WHERE id = ?';
        $statement = self::$db->prepare($query);
        $statement->bind_param('si', $commentText, $commentId);
        $statement->execute();
        $response = array();
        if($statement->affected_rows > 0) {
            $response['statusCode'] = 200;
        } else {
            $response['statusCode'] = 400;
        }

        return $response;
    }

    public function deleteAlbumComment($commentId){
        $query = 'DELETE FROM album_comments WHERE id = ?';
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $commentId);
        $statement->execute();
        $response = array();
        if($statement->affected_rows > 0) {
            $response['statusCode'] = 200;
        } else {
            $response['statusCode'] = 400;
        }

        return $response;
    }

    public function getAlbumIdByPhotoCommentId($photoCommentId){
        $query = "SELECT a.id FROM albums a
                  INNER JOIN photos p ON a.id = p.album_id
                  INNER JOIN photo_comments c ON p.id = c.photo_id
                  WHERE c.id = ?";
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $photoCommentId);
        $statement->execute();
        $statement->bind_result($id);
        $result = array();
        while($statement->fetch()) {
            $result['id'] = $id;
        }

        $albumId = $result['id'];
        return $albumId;
    }

    public function getPhotoComment($commendId){
        $query = "SELECT c.id, c.text, u.username, c.date
            FROM photo_comments c INNER JOIN users u ON c.user_id = u.id
            WHERE c.id = ?";
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $commendId);
        $statement->execute();
        $statement->bind_result($id, $text, $username, $date);
        $comment = array();
        while($statement->fetch()){
            $comment['id'] = $id;
            $comment['text'] = $text;
            $comment['username'] = $username;
            $comment['date'] = $date;
        }

        return $comment;
    }

    public function editPhotoComment($commentId, $commentText){
        $query = 'UPDATE photo_comments SET text = ? WHERE id = ?';
        $statement = self::$db->prepare($query);
        $statement->bind_param('si', $commentText, $commentId);
        $statement->execute();
        $response = array();
        if($statement->affected_rows > 0) {
            $response['statusCode'] = 200;
        } else {
            $response['statusCode'] = 400;
        }

        return $response;
    }

    public function deletePhotoComment($commentId) {
        $query = 'DELETE FROM photo_comments WHERE id = ?';
        $statement = self::$db->prepare($query);
        $statement->bind_param('i', $commentId);
        $statement->execute();
        $response = array();
        if($statement->affected_rows > 0) {
            $response['statusCode'] = 200;
        } else {
            $response['statusCode'] = 400;
        }

        return $response;
    }

    public function getUserProfile($username){
        $statement = self::$db->prepare(
            "SELECT username, first_name, last_name, r.name as role FROM users u
            LEFT OUTER JOIN user_roles ur ON u.id = ur.user_id
            LEFT OUTER JOIN roles r ON ur.role_id = r.id
            WHERE username = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $statement->bind_result($username, $first_name, $last_name, $role);
        $profileInformation = array();
        while($statement->fetch()) {
            $profileInformation['username'] = $username;
            $profileInformation['first_name'] = $first_name;
            $profileInformation['last_name'] = $last_name;
            $profileInformation['role']  = $role;
        }

        return $profileInformation;
    }

    public function editUsername($currentUsername, $newUsername){
        $response = array();
        if($currentUsername != $newUsername) {
            $statement = self::$db->prepare("SELECT COUNT(id) as usernameTaken FROM users WHERE username = ?");
            $statement->bind_param("s", $newUsername);
            $statement->execute();
            $statement->bind_result($usernameTaken);
            $result = array();
            while($statement->fetch()) {
                $result['usernameTaken'] = $usernameTaken;
            }

            if ($result['usernameTaken']) {
                $response = array();
                $response['statusCode'] = 400;
                $response['message'] = 'Username is already taken';
                return $response;
            } else {
                $query = 'UPDATE users SET username = ? WHERE username = ?';
                $statement = self::$db->prepare($query);
                $statement->bind_param('ss', $newUsername, $currentUsername);
                $statement->execute();
                if ($statement->affected_rows > 0) {
                    $response['statusCode'] = 200;
                } else {
                    $response['statusCode'] = 400;
                }
            }
        } else {
            $response['statusCode'] = 200;
        }

        return $response;
    }

    public function editFirstName($currentFirstName, $newFirstName, $username){
        $response = array();
        if($currentFirstName != $newFirstName) {
            $query = 'UPDATE users SET first_name = ? WHERE username = ?';
            $statement = self::$db->prepare($query);
            $statement->bind_param('ss', $newFirstName, $username);
            $statement->execute();
            if ($statement->affected_rows > 0) {
                $response['statusCode'] = 200;
            } else {
                $response['statusCode'] = 400;
            }
        } else {
            $response['statusCode'] = 200;
        }

        return $response;
    }

    public function editLastName($currentLastName, $newLastName, $username){
        $response = array();
        if($currentLastName != $newLastName) {
            $query = 'UPDATE users SET last_name = ? WHERE username = ?';
            $statement = self::$db->prepare($query);
            $statement->bind_param('ss', $newLastName, $username);
            $statement->execute();
            if ($statement->affected_rows > 0) {
                $response['statusCode'] = 200;
            } else {
                $response['statusCode'] = 400;
            }
        } else {
            $response['statusCode'] = 200;
        }

        return $response;
    }

    public function editRole($username, $isAdminInput){
        $checkQuery = 'SELECT COUNT(u.id) as isAdmin FROM users u
              INNER JOIN user_roles ur ON u.id = ur.user_id
              INNER JOIN roles r ON r.id = ur.role_id
              WHERE u.username = ? AND r.name = ?';
        $statement = self::$db->prepare($checkQuery);
        $adminRole = ADMIN_ROLE;
        $statement->bind_param('ss', $username, $adminRole);
        $statement->execute();
        $statement->bind_result($isAdmin);
        $result = array();
        while($statement->fetch()){
            $result['isAdmin'] = $isAdmin;
        }

        $hasAdminRole = $result['isAdmin'];
        $response = array();
        if($isAdminInput == 1) {
            if(!$hasAdminRole) {
                $query =
                  'INSERT INTO user_roles (user_id, role_id) VALUES((SELECT id FROM users WHERE username = ?),
                                            (SELECT id FROM roles WHERE name = ?))';
                $statement = self::$db->prepare($query);
                $adminRole = ADMIN_ROLE;
                $statement->bind_param('ss', $username, $adminRole);
                $statement->execute();
                if($statement->affected_rows > 0) {
                    $response['statusCode'] = 200;
                } else {
                    $response['statusCode'] = 400;
                }

            } else {
                $response['statusCode'] = 200;
            }
        } else {
            if($hasAdminRole){
                $query = 'DELETE FROM user_roles
                          WHERE user_id = (SELECT id FROM users WHERE username = ?)
                          AND role_id = (SELECT id FROM roles WHERE name = ?)';
                $statement = self::$db->prepare($query);
                $adminRole = ADMIN_ROLE;
                $statement->bind_param('ss', $username, $adminRole);
                $statement->execute();
                if($statement->affected_rows > 0) {
                    $response['statusCode'] = 200;
                } else {
                    $response['statusCode'] = 400;
                }
            } else {
                $response['statusCode'] = 200;
            }
        }

        return $response;
    }
}