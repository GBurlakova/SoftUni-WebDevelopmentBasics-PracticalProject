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

        $this->mostLikedAlbums = $this->db->getMostLikedAlbums($categoryId);
        $this->renderView();
    }

    public function publicAlbums(){
        $categoryId = null;
        if(isset($_GET['categoryId'])) {
            $categoryId = $_GET['categoryId'];
        }

        $this->publicAlbums = $this->db->getPublicAlbums($categoryId);
        $this->renderView('publicAlbums');
    }
}