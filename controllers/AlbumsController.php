<?php
class AlbumsController extends BaseController {
    private $db;

    public function onInit() {
        $this->db = new AlbumsModel();
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

    public function add() {
        $this->authorize();
        $this->title = "Add album";

        if($this->isPost) {
            $todoIsAdded = $this->db->addTodoItem($_POST['todo-item']);
            if($todoIsAdded) {
                $this->addSuccessMessage("Todo added");
                $this->redirect('albums');
            } else {
                $this->addErrorMessage("Todo cannot be added. Please try again!");
                $this->redirect('photo-album/addTodo');
            }
        }

        $this->renderView(__FUNCTION__);
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
}