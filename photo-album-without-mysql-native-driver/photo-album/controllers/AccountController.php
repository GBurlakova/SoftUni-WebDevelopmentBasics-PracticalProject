<?php
class AccountController extends BaseController {
    private $db;

    public function onInit() {
        $this->title = 'Account';
        $this->db = new AccountModel();
    }

    public function register() {
        $this->title = 'Register';
        if(isset($_SESSION['emptyFields'])) {
            $this->emptyFields = $_SESSION['emptyFields'];
            $this->filledFields = $_SESSION['filledFields'];
            unset($_SESSION['emptyFields']);
            unset($_SESSION['filledFields']);
        }

        if(isset($_SESSION['registerErrors'])) {
            $this->registerErrors = $_SESSION['registerErrors'];
            unset($_SESSION['registerErrors']);
        }

        if ($this->isPost) {
            $hasEmptyFields = false;
            $filledFields = array();
            if($_POST['username']) {
                $username = $_POST['username'];
                $filledFields['username'] = $username;
            } else {
                $_SESSION['emptyFields']['username'] = true;
                $hasEmptyFields = true;
            }

            if($_POST['firstName']) {
                $firstName= $_POST['firstName'];
                $filledFields['firstName'] = $firstName;
            } else {
                $_SESSION['emptyFields']['firstName'] = true;
                $hasEmptyFields = true;
            }

            if($_POST['lastName']) {
                $lastName = $_POST['lastName'];
                $filledFields['lastName'] = $lastName;
            } else {
                $_SESSION['emptyFields']['lastName'] = true;
                $hasEmptyFields = true;
            }

            if($_POST['password']) {
                $password = $_POST['password'];
            } else {
                $_SESSION['emptyFields']['password'] = true;
                $hasEmptyFields = true;
            }

            if($hasEmptyFields) {
                $_SESSION['filledFields'] = $filledFields;
                $this->redirect("account", "register");
            }

            $response = $this->db->register($firstName, $lastName, $username, $password);
            $statusCode = $response['statusCode'];
            if ($statusCode == 201) {
                $_SESSION['username'] = $username;
                $this->addSuccessMessage("Successful registration!");
                $this->redirect("home");
            } else {
                if(isset($response['message'])) {
                    $_SESSION['registerErrors']['usernameTaken'] = true;
                } else {
                    $this->addErrorMessage("Register failed!");
                }

                $this->redirect("account", "register");
            }
        }

        $this->renderView(__FUNCTION__);
        unset($this->emptyFields);
        unset($this->filledFields);
        unset($this->registerErrors);
    }

    public function login() {
        $this->title = 'Login';
        if(isset($_SESSION['emptyFields'])) {
            $this->emptyFields = $_SESSION['emptyFields'];
            unset($_SESSION['emptyFields']);
        }

        if ($this->isPost) {
            $hasEmptyFields = false;
            if($_POST['username']) {
                $username = $_POST['username'];
                $filledFields['username'] = $username;
            } else {
                $_SESSION['emptyFields']['username'] = true;
                $hasEmptyFields = true;
            }

            if($_POST['password']) {
                $password = $_POST['password'];
            } else {
                $_SESSION['emptyFields']['password'] = true;
                $hasEmptyFields = true;
            }

            if($hasEmptyFields) {
                $this->redirect("account", "login");
            }

            $response = $this->db->login($username, $password);
            $statusCode = $response['statusCode'];
            if ($statusCode == 200) {
                $_SESSION['username'] = $username;
                $_SESSION['isAdmin'] = $response['isAdmin'];
                $this->redirect("home");
            } else {
                $this->addErrorMessage("Login error!");
                $this->redirect("account", "login");
            }
        }

        $this->renderView(__FUNCTION__);
        unset($this->emptyFields);
    }

    public function logout() {
        $this->authorize();
        $this->addInfoMessage("Bye, bye, " . $_SESSION['username']);
        unset($_SESSION['username']);
        unset($_SESSION['isAdmin']);
        $this->redirectToUrl("/photo-album");
    }

    public function profile(){
        $this->authorize();
        $this->title = 'Profile';
        $profile = $this->db->profile($_SESSION['username']);
        $this->profile = $profile;
        $this->renderView(__FUNCTION__);
    }

    public function editProfile() {
        $this->authorize();
        $this->title = 'Edit profile';
        if(isset($_SESSION['emptyFields'])) {
            $this->emptyFields = $_SESSION['emptyFields'];
            $this->filledFields = $_SESSION['filledFields'];
            unset($_SESSION['emptyFields']);
            unset($_SESSION['filledFields']);
        }

        if(isset($_SESSION['editProfileErrors'])) {
            $this->editProfileErrors = $_SESSION['editProfileErrors'];
            unset($_SESSION['editProfileErrors']);
        }

        if($this->isPost) {
            $hasEmptyFields = false;
            $filledFields = array();
            if($_POST['username']) {
                $username = $_POST['username'];
                $filledFields['username'] = $username;
            } else {
                $_SESSION['emptyFields']['username'] = true;
                $hasEmptyFields = true;
            }

            if($_POST['firstName']) {
                $firstName= $_POST['firstName'];
                $filledFields['firstName'] = $firstName;
            } else {
                $_SESSION['emptyFields']['firstName'] = true;
                $hasEmptyFields = true;
            }

            if($_POST['lastName']) {
                $lastName = $_POST['lastName'];
                $filledFields['lastName'] = $lastName;
            } else {
                $_SESSION['emptyFields']['lastName'] = true;
                $hasEmptyFields = true;
            }

            if($_POST['password']) {
                $password = $_POST['password'];
            } else {
                $_SESSION['emptyFields']['password'] = true;
                $hasEmptyFields = true;
            }

            if($hasEmptyFields) {
                $_SESSION['filledFields'] = $filledFields;
                $this->redirect("account", "editProfile");
            }

            $response = $this->db->editProfile(
                $_SESSION['username'], $firstName, $lastName, $username, $password);
            $statusCode = $response['statusCode'];
            if($statusCode == 200) {
                $this->addSuccessMessage("Profile edited successfully");
                $_SESSION['username'] = $username;
                $this->redirect("account", "profile");
            } else {
                if(isset($response['message'])) {
                    $_SESSION['editProfileErrors']['usernameTaken'] = true;
                } else {
                    $this->addErrorMessage("Edit profile failed");
                }

                $this->redirect("account", "editProfile");
            }

        }

        $profile = $this->db->profile($_SESSION['username']);
        $this->profile = $profile;
        $this->renderView(__FUNCTION__);
    }
}