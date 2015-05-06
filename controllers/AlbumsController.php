<?php
class AlbumsController extends BaseController {
    private $db;

    public function onInit() {
        $this->db = new AlbumsModel();
        $this->categories = $this->db->getCategories();
    }

    public function index() {
        $this->authorize();
        $this->title = "Albums";
        $username = '';
        if(isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        }

        $this->userAlbums = $this->db->all($username);
        $this->renderView();
    }

    public function publicAlbums($startPage = 1){
        $this->authorize();
        $categoryId = null;
        if(isset($_GET['categoryId'])) {
            $categoryId = $_GET['categoryId'];
        }

        $username = '';
        if(isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        }

        $this->publicAlbums = $this->db->getPublicAlbums($username, $startPage, $categoryId)['albums'];
        $this->pagesCount = $this->db->getPublicAlbums($username, $startPage, $categoryId)['pagesCount'];
        $this->renderView('publicAlbums');
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
                    $this->redirect('albums', 'newAlbum');
                }

                $isPublic = isset($_POST['isPublic']) ?  1 : 0;
                $userId = $this->db->getUserId($_SESSION['username']);

                $albumCreated = $this->db->newAlbum($albumName, $isPublic, $userId, $categoryId);
                if($albumCreated) {
                    $this->addSuccessMessage("Album created");
                    $this->redirect('albums');
                } else {
                    $this->addErrorMessage("Album cannot be created. Please try again!");
                    $this->redirect('albums', 'newAlbum');
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

        $this->redirect('albums');
    }

    public function like($albumId){
        $this->authorize();
        $username = $_SESSION['username'];
        $isLiked = $this->db->like($username, $albumId);
        if(!$isLiked) {
            $this->addErrorMessage("Cannot like album.");
        }

        $this->redirect('albums', 'publicAlbums');
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
                    $this->redirect('albums', 'upload');
                }

                if($_FILES['photo']['tmp_name']){
                    if($_FILES['photo']['size'] > 2097152){
                        $_SESSION['errors']['notAllowedPhotoSize'] = true;
                        $this->redirect('albums', 'upload');
                    }

                    if($_FILES['photo']['type']!='image/gif' &&
                        $_FILES['photo']['type']!='image/jpeg' &&
                        $_FILES['photo']['type']!='image/pjerg') {
                        $_SESSION['errors']['notAllowedPhotoType'] = true;
                        $this->redirect('albums', 'upload');
                    }

                    $userPhotosDirectory = './content/user-photos/'.$_SESSION['username'];

                    if(!is_dir($userPhotosDirectory)){
                        mkdir($userPhotosDirectory, null, true);
                    }

                    $photoName = time().'_'.$_FILES['photo']['name'];
                    $filePath = $userPhotosDirectory.'/'.$photoName;
                    if(move_uploaded_file($_FILES['photo']['tmp_name'],
                        $filePath)) {
                        $albumId = $_POST['albumId'];
                        if($this->db->addPhoto($photoName, $albumId, $_SESSION['username'])) {
                            $this->addSuccessMessage('Photo uploaded successfully');
                            $this->redirect('albums');
                        } else {
                            $this->addErrorMessage('Cannot upload photo. Please try again.');
                            $this->redirect('albums', 'upload');
                        };
                    } else {
                        $this->addErrorMessage('Cannot upload photo. Please try again.');
                        $this->redirect('albums', 'upload');
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
}