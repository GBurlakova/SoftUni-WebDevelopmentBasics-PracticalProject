<?php
class AllAlbumsController extends BaseController {
    private $db;

    public function onInit() {
        $this->db = new AllAlbumsModel();
        $this->categories = $this->db->getCategories();
    }

    public function index($startPage = 1){
        $this->authorize();

        $this->title = "All Albums";
        $username = '';

        if(isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        }

        $categoryId = null;
        if(isset($_GET['categoryId'])) {
            $categoryId = $_GET['categoryId'];
        }

        $allAlbums = $this->db->getAllAlbums($username, $startPage, $categoryId);
        $this->allAlbums = $allAlbums['allAlbums'];
        $this->pagesCount = $allAlbums['pagesCount'];
        $this->renderView();
    }

    public function like($albumId){
        $this->authorize();
        $username = $_SESSION['username'];
        $isLiked = $this->db->like($username, $albumId);
        if(!$isLiked) {
            $this->addErrorMessage("Cannot like album.");
        }

        $this->redirect('allAlbums');
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
                    $this->redirect('allAlbums');
                } else {
                    $this->addErrorMessage('Comment cannot be added. Please try again');
                    $this->redirect('allAlbums');
                }
            } else {
                $this->addErrorMessage('Please add non-empty comments');
                $this->redirect('allAlbums');
            }
        }
    }
}