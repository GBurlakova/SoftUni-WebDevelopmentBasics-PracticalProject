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

            $respond = $this->db->register($firstName, $lastName, $username, $password);
            $statusCode = $respond['statusCode'];
            if ($statusCode == 201) {
                $_SESSION['username'] = $username;
                $this->addSuccessMessage("Successful registration!");
                $this->redirect("userAlbums", "index");
            } else {
                if(isset($respond['message'])) {
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

            $isLoggedIn = $this->db->login($username, $password);
            if ($isLoggedIn) {
                $_SESSION['username'] = $username;
                $this->redirect("userAlbums", "index");
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
        $this->redirectToUrl("/photo-album");
    }

    public function profile(){
        $this->authorize();
        $profile = $this->db->profile($_SESSION['username']);
        $this->profile = $profile;
        $this->renderView(__FUNCTION__);
    }
}