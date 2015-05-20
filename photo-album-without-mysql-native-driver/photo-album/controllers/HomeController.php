<?php
class HomeController extends  BaseController {
    private $db;

    public function onInit() {
        $this->title = "Home";
        $this->db = new HomeModel();
        $this->categories = $this->db->getCategories();
    }

    public function index($startPage = 1) {
        $categoryId = null;
        $this->title = 'Home';
        if(isset($_GET['categoryId'])) {
            $categoryId = $_GET['categoryId'];
        }

        $mostLikedAlbums = $this->db->getMostLikedAlbums($startPage, $categoryId);
        $this->mostLikedAlbums = $mostLikedAlbums['allAlbums'];
        $this->pagesCount = $mostLikedAlbums['pagesCount'];
        $this->renderView();
    }

    public function photos($albumId){
        $this->albumPhotos = $this->db->getAlbumPhotos($albumId);
        $this->renderView(__FUNCTION__);
    }
}