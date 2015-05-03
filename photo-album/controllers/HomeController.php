<?php
class HomeController extends  BaseController {
    private $db;

    public function onInit() {
        $this->title = "Home";
        $this->db = new HomeModel();
        $this->categories = $this->db->getCategories();
    }

    public function index() {
        $categoryId = null;
        if(isset($_GET['categoryId'])) {
            $categoryId = $_GET['categoryId'];
        }

        $username = '';
        if(isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        }

        $this->mostLikedAlbums = $this->db->getMostLikedAlbums($username, $categoryId);
        $this->renderView();
    }

    public function publicAlbums(){
        $categoryId = null;
        if(isset($_GET['categoryId'])) {
            $categoryId = $_GET['categoryId'];
        }

        $username = '';
        if(isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        }

        $this->publicAlbums = $this->db->getPublicAlbums($username, $categoryId);
        $this->renderView('publicAlbums');
    }
}