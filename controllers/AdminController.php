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
        $this->authorize(ADMIN_ROLE);

        $this->title = "All Albums";
        $albums = $this->db->getAllAlbums($startPage);
        $this->albums = $albums['albums'];
        $this->pagesCount = $albums['pagesCount'];
        $this->renderView();
    }

    // Photos actions
    public function photos($albumId){
        $this->authorize(ADMIN_ROLE);
        $this->title = "Photos";
        $photos = $this->db->getAlbumPhotos($albumId);
        $this->photos = $photos;
        $this->renderView(__FUNCTION__);
    }

    // Categories actions
    public function categories(){
        $this->authorize(ADMIN_ROLE);
        $this->title = 'Categories';
        $this->categories = $this->db->getCategories();
        $this->renderView(__FUNCTION__);
    }

    public function editCategoryForm($categoryId){
        $this->authorize(ADMIN_ROLE);
        $this->categoryId = $categoryId;
        $this->categoryName = $this->db->getCategoryName($categoryId);
        $this->renderView(__FUNCTION__, false);
    }

    public function editCategory(){
        $this->authorize(ADMIN_ROLE);

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
        $this->authorize(ADMIN_ROLE);
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
        $this->authorize(ADMIN_ROLE);
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

    // Albums actions
    public function deleteAlbum($albumId){
        $this->authorize(ADMIN_ROLE);
        if($this->isPost) {
            $response = $this->db->deleteAlbum($albumId);
            if($response['statusCode'] == 200) {
                $this->addSuccessMessage('Album deleted successfully');
                $this->redirect('admin');
            } else {
                if(isset($response['message'])) {
                    $this->addErrorMessage($response['message']);
                } else {
                    $this->addErrorMessage('Delete album failed');
                }

                $this->redirect('admin');
            }
        }
    }

    public function editAlbumForm($albumId){
        $this->authorize(ADMIN_ROLE);
        $this->albumId = $albumId;
        $this->albumName = $this->db->getAlbumName($albumId);
        $this->renderView(__FUNCTION__, false);
    }

    public function editAlbum(){
        $this->authorize(ADMIN_ROLE);
        if($this->isPost) {
            if($_POST['albumName']) {
                $albumName = $_POST['albumName'];
            } else {
                $this->addErrorMessage('Please enter non-empty category name');
                $this->redirect('admin');
            }

            $albumId = $_POST['albumId'];
            $response = $this->db->editAlbum($albumId, $albumName);
            if($response['statusCode'] == 200) {
                $this->addSuccessMessage('Album edited successfully');
            } else {
                $this->addErrorMessage('Edit album failed. Please try again');
            }

            $this->redirect('admin');
        }

    }

    // Photos actions
    public function deletePhoto($photoId){
        $this->authorize(ADMIN_ROLE);
        if($this->isPost) {
            $response = $this->db->deletePhoto($photoId);
            if($response['statusCode'] == 200) {
                $photoName = $response['photoInfo']['photoName'];
                $userId = $response['photoInfo']['userId'];
                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/photo-album/content/user-photos/user'.$userId . '/'. $photoName;
                unlink($filePath);
                $this->addSuccessMessage('Photo deleted successfully');
            } else {
                if(isset($response['message'])) {
                    $this->addErrorMessage($response['message']);
                } else {
                    $this->addErrorMessage("Delete photo failed!");
                }
            }

            $albumId = $this->db->getAlbumId($photoId);
            $this->redirectToUrl('/photo-album/admin/photos/' . $albumId);
        }
    }

    // Album comments actions
    public function editAlbumCommentForm($commentId){
        $this->authorize(ADMIN_ROLE);
        $this->comment = $this->db->getAlbumComment($commentId);
        $this->renderView(__FUNCTION__, false);
    }

    public function editAlbumComment(){
        $this->authorize(ADMIN_ROLE);
        if($this->isPost) {
            if($_POST['commentText']) {
                $commentText = $_POST['commentText'];
            } else {
                $this->addErrorMessage('Please enter non-empty comment');
                $this->redirect('admin');
            }

            $commentId = $_POST['commentId'];
            $response = $this->db->editAlbumComment($commentId, $commentText);
            if($response['statusCode'] == 200) {
                $this->addSuccessMessage('Album comment edited successfully');
            } else {
                $this->addErrorMessage('Edit album comment failed. Please try again');
            }

            $this->redirect('admin');
        }
    }

    public function deleteAlbumComment($commentId){
        $this->authorize(ADMIN_ROLE);
        if($this->isPost) {
            $response = $this->db->deleteAlbumComment($commentId);
            if($response['statusCode'] == 200) {
                $this->addSuccessMessage('Album comment deleted successfully');
            } else {
                $this->addSuccessMessage('Album comment deleted successfully');
            }

            $this->redirect('admin');
        }
    }

    // Photo comments actions
    public function editPhotoCommentForm($commentId){
        $this->authorize(ADMIN_ROLE);
        $this->comment = $this->db->getPhotoComment($commentId);
        $this->albumId = $this->db->getAlbumIdByPhotoCommentId($commentId);
        $this->renderView(__FUNCTION__, false);
    }

    public function editPhotoComment(){
        $this->authorize(ADMIN_ROLE);
        if($this->isPost) {
            $commentId = $_POST['commentId'];
            $albumId = $this->db->getAlbumIdByPhotoCommentId($commentId);
            if($_POST['commentText']) {
                $commentText = $_POST['commentText'];
            } else {
                $this->addErrorMessage('Please enter non-empty comment');
                $this->redirect('admin/photos/' . $albumId);
            }

            $response = $this->db->editPhotoComment($commentId, $commentText);
            if($response['statusCode'] == 200) {
                $this->addSuccessMessage('Photo comment edited successfully');
            } else {
                $this->addErrorMessage('Edit photo comment failed. Please try again');
            }

            $this->redirect('admin/photos/' . $albumId);
        }
    }

    public function deletePhotoComment($commentId){
        $this->authorize(ADMIN_ROLE);
        if($this->isPost) {
            $response = $this->db->deletePhotoComment($commentId);
            if($response['statusCode'] == 200) {
                $this->addSuccessMessage('Photo comment deleted successfully');
            } else {
                $this->addSuccessMessage('Photo comment deleted successfully');
            }

            $albumId = $this->db->getAlbumId($commentId);
            $this->redirect('/photo-album/admin/photos' . $albumId);
        }
    }
}