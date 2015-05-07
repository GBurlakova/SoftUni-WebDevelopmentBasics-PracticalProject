<?php
class UserAlbumsController extends BaseController {
    private $db;

    public function onInit() {
        $this->db = new UserAlbumsModel();
        $this->categories = $this->db->getCategories();
    }

    public function index($startPage = 1) {
        $this->authorize();
        $this->title = "My Albums";
        $username = '';
        if(isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        }

        if(isset($_POST['albumsSearchCondition'])) {
            $albumsSearchCondition = $_POST['albumsSearchCondition'];
        } else {
            $albumsSearchCondition = null;
        }

        $userAlbums = $this->db->getUserAlbums($startPage, $username, $albumsSearchCondition);
        $this->userAlbums = $userAlbums['userAlbums'];
        $this->pagesCount = $userAlbums['pagesCount'];
        $this->renderView();
    }

    public function newAlbum() {
        $this->authorize();
        $this->title = "New album";
        if(isset($_SESSION['emptyFields'])) {
            $this->emptyFields = $_SESSION['emptyFields'];
            unset($_SESSION['emptyFields']);
        }

        if($this->isPost) {
            if(isset($_POST['submit'])) {
                // Check for empty fields
                if($_POST['albumName']) {
                    $albumName = $_POST['albumName'];
                } else {
                    $_SESSION['emptyFields']['albumName'] = true;
                }

                if($_POST['categoryId']) {
                    $categoryId = $_POST['categoryId'];
                } else {
                    $_SESSION['emptyFields']['categoryId'] = true;
                }

                if (!$_POST['albumName'] || !$_POST['categoryId']) {
                    $this->redirect('userAlbums', 'newAlbum');
                }

                $userId = $this->db->getUserId($_SESSION['username']);

                $albumCreated = $this->db->createNewAlbum($albumName, $userId, $categoryId);
                if($albumCreated) {
                    $this->addSuccessMessage("Album created");
                    $this->redirect('userAlbums');
                } else {
                    $this->addErrorMessage("Album cannot be created. Please try again!");
                    $this->redirect('userAlbums', 'newAlbum');
                }
            }
        }

        $this->categories = $this->db->getCategories();
        $this->renderView(__FUNCTION__);
        unset($this->emptyFields);
    }

    public function delete($id){
        $this->authorize();

        if ($this->db->deleteTodo($id)) {
            $this->addSuccessMessage("Todo deleted.");
        } else {
            $this->addErrorMessage("Cannot delete todo.");
        }

        $this->redirect('allAlbums');
    }

    public function like($albumId){
        $this->authorize();
        $username = $_SESSION['username'];
        $isLiked = $this->db->like($username, $albumId);
        if(!$isLiked) {
            $this->addErrorMessage("Cannot like album.");
        }

        $this->redirect('allAlbums', 'publicAlbums');
    }

    public function upload(){
        $this->authorize();

        if(isset($_SESSION['errors'])) {
            $this->erros = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        if($this->isPost) {
            if(isset($_POST['submit'])) {
                // Check for empty fields
                $hasUploadedPhoto = is_uploaded_file($_FILES['photo']['tmp_name']);
                if(!$hasUploadedPhoto){
                    $_SESSION['errors']['emptyPhoto'] = true;
                }

                if(!$_POST['albumId']){
                    $_SESSION['errors']['emptyAlbumId'] = true;
                }

                if (!$hasUploadedPhoto || !$_POST['albumId']) {
                    $this->redirect('userAlbums', 'upload');
                }

                if($_FILES['photo']['tmp_name']){
                    if($_FILES['photo']['size'] > 2097152){
                        $_SESSION['errors']['notAllowedPhotoSize'] = true;
                        $this->redirect('userAlbums', 'upload');
                    }

                    if($_FILES['photo']['type']!='image/gif' &&
                        $_FILES['photo']['type']!='image/jpeg' &&
                        $_FILES['photo']['type']!='image/pjerg') {
                        $_SESSION['errors']['notAllowedPhotoType'] = true;
                        $this->redirect('userAlbums', 'upload');
                    }

                    $userId = $this->db->getUserId($_SESSION['username']);
                    $userPhotosDirectory = './content/user-photos/user'.$userId;

                    if(!is_dir($userPhotosDirectory)){
                        mkdir($userPhotosDirectory, null, true);
                    }

                    $photoName = time().'_user'.$userId;
                    $filePath = $userPhotosDirectory.'/'.$photoName;
                    if(move_uploaded_file($_FILES['photo']['tmp_name'],
                        $filePath)) {
                        $albumId = $_POST['albumId'];
                        if($this->db->addPhoto($photoName, $albumId, $_SESSION['username'])) {
                            $this->addSuccessMessage('Photo uploaded successfully');
                            $this->redirect('userAlbums');
                        } else {
                            $this->addErrorMessage('Cannot upload photo. Please try again.');
                            $this->redirect('userAlbums', 'upload');
                        };
                    } else {
                        $this->addErrorMessage('Cannot upload photo. Please try again.');
                        $this->redirect('userAlbums', 'upload');
                    }
                }
            }
        }

        $this->albums = $this->db->getAlbums($_SESSION['username']);
        $this->renderView(__FUNCTION__);
        unset($this->errors);
    }

    public function photos($albumId){
        $this->authorize();
        $this->albumPhotos = $this->db->getAlbumPhotos($albumId, $_SESSION['username']);
        $this->renderView(__FUNCTION__);
    }

    public function commentForm($albumId) {
        $this->authorize();
        $this->albumId = $albumId;
        $this->renderView(__FUNCTION__, false);
    }

    public function comment(){
        $this->authorize();
        if($this->isPost) {
            $commentText = $_POST['comment'];
            if($commentText) {
                $albumId = $_POST['albumId'];
                if($this->db->comment($commentText, $albumId, $_SESSION['username'])) {
                    $this->addSuccessMessage('Comment added successfully');
                    $this->redirect('userAlbums');
                } else {
                    $this->addErrorMessage('Comment cannot be added. Please try again');
                    $this->redirect('userAlbums');
                }
            } else {
                $this->addErrorMessage('Please add non-empty comments');
                $this->redirect('allAlbums');
            }
        }
    }
}