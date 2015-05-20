<?php
class PhotosController extends BaseController
{
    private $db;

    public function onInit() {
        $this->db = new PhotosModel();
    }

    public function commentForm() {
        $this->authorize();
        if(isset($_GET['photoId']) && isset($_GET['albumId']) && isset($_GET['controller'])) {
            $photoId = $_GET['photoId'];
            $albumId = $_GET['albumId'];
            $controller = $_GET['controller'];
            $this->photoId = $photoId;
            $this->albumId = $albumId;
            $this->controller = $controller;
            $this->renderView(__FUNCTION__, false);
        }
    }

    public function comment() {
        $this->authorize();
        if($this->isPost) {
            $commentText = $_POST['comment'];
            $albumId = $_POST['albumId'];
            $controller = $_POST['controller'];
            $url = '/photo-album/' . $controller . '/photos/' . $albumId;

            if($commentText) {
                $photoId = $_POST['photoId'];

                if($this->db->comment($commentText, $photoId, $_SESSION['username'])) {
                    $this->addSuccessMessage('Comment added successfully');
                    $this->redirectToUrl($url);
                } else {
                    $this->addErrorMessage('Comment cannot be added. Please try again');
                    $this->redirectToUrl($url);
                }
            } else {
                $this->addErrorMessage('Please add non-empty comments');
                $this->redirectToUrl($url);
            }
        }
    }

    public function download() {
        $this->authorize();
        if($this->isPost) {
            if(isset($_POST['photoName'])) {
                $photoName = $_POST['photoName'];
                $userId = $_POST['userId'];
                $filePath = $_SERVER['HTTP_ORIGIN'] . '/photo-album/content/user-photos/user'.$userId . '/'. $photoName;
                header("Content-disposition: attachment; filename={$photoName}");
                header('Content-type: application/octet-stream');
                header("Content-Type: application/force-download");
                readfile($filePath);
            }
        }
    }
}