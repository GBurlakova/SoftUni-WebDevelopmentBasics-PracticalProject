<?php
class AdminController extends BaseController
{
    private $db;

    public function onInit()
    {
        $this->title = 'Admin panel';
        $this->db = new AdminModel();
    }

    public function index($startPage = 1){
        $this->authorize('administrator');

        $this->title = "All Albums";
        $albums = $this->db->getAllAlbums($startPage);
        $this->albums = $albums['albums'];
        $this->pagesCount = $albums['pagesCount'];
        $this->renderView();
    }

    public function photos($albumId){
        $this->authorize('administrator');
        $this->title = "Photos";
        $photos = $this->db->getAlbumPhotos($albumId);
        $this->photos = $photos;
        $this->renderView(__FUNCTION__);
    }

    public function categories(){
        $this->authorize('administrator');
        $this->title = 'Categories';
        $this->categories = $this->db->getCategories();
        $this->renderView(__FUNCTION__);
    }

    public function editCategoryForm($categoryId){
        $this->authorize('administrator');
        $this->categoryId = $categoryId;
        $this->categoryName = $this->db->getCategoryName($categoryId);
        $this->renderView(__FUNCTION__, false);
    }

    public function editCategory(){
        $this->authorize('administrator');

        if($this->isPost) {
            if($_POST['categoryName']) {
                $categoryName = $_POST['categoryName'];
            } else {
               $this->addErrorMessage('Please enter non-empty category name');
                $this->redirect('admin', 'categories');
            }

            $categoryId =  $_POST['categoryId'];
            $response = $this->db->editCategory($categoryId, $categoryName);
            if($response['statusCode'] == 200) {
                $this->addSuccessMessage('Category edited successfully');
                $this->redirect('admin', 'categories');
            } else {
                if(isset($response['message'])) {
                    $this->addErrorMessage("Category name is already taken!");
                } else {
                    $this->addErrorMessage("Edit category failed!");
                }

                $this->redirect("admin", "categories");
            }
        }
    }

    public function deleteCategory($categoryId){
        $this->authorize('administrator');
        $response = $this->db->deleteCategory($categoryId);
        if($response['statusCode'] == 200) {
            $this->addSuccessMessage('Category deleted successfully');
            $this->redirect('admin', 'categories');
        } else {
            if(isset($response['message'])) {
                $this->addErrorMessage($response['message']);
                $this->redirect("admin", "categories");
            } else {
                $this->addErrorMessage("Delete category failed!");
                $this->redirect("admin", "categories");
            }
        }
    }

    public function newCategory()
    {
        $this->authorize('administrator');
        if(isset($_SESSION['emptyFields'])) {
            $this->emptyFields = $_SESSION['emptyFields'];
            unset($_SESSION['emptyFields']);
        }

        if(isset($_SESSION['createCategoryErrors'])) {
            $this->createCategoryErrors = $_SESSION['createCategoryErrors'];
            unset($_SESSION['createCategoryErrors']);
        }

        if($this->isPost) {
            if($_POST['categoryName']) {
                $categoryName = $_POST['categoryName'];
            } else {
                $_SESSION['emptyFields']['categoryName'] = true;
                $this->redirect('admin', 'newCategory');
            }

            $response = $this->db->newCategory($categoryName);
            if($response['statusCode'] == 200) {
                $this->addSuccessMessage('Category created successfully');
                $this->redirect('admin', 'categories');
            } else {
                if(isset($response['message'])) {
                    $_SESSION['createCategoryErrors']['categoryNameTaken'] = true;
                    $this->redirect("admin", "newCategory");
                } else {
                    $this->addErrorMessage("Create category failed!");
                    $this->redirect("admin", "newCategory");
                }
            }

        }

        $this->renderView(__FUNCTION__);
        unset($this->emptyFields);
        unset($this->createCategoryErrors);
    }
}