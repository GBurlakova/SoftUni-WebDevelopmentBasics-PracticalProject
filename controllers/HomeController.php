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
        if(isset($_GET['categoryId'])) {
            $categoryId = $_GET['categoryId'];
        }

        $username = '';
        if(isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        }

        $this->mostLikedAlbums = $this->db->getMostLikedAlbums($username, $startPage, $categoryId)['albums'];
        $this->pagesCount = $this->db->getMostLikedAlbums($username, $startPage, $categoryId)['pagesCount'];
        $this->renderView();
    }

    public function publicAlbums($startPage = 1){
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
}