<?php
class AccountController extends BaseController {
    private $db;

    public function onInit() {
        $this->title = 'Account';
        $this->db = new AccountModel();
    }

    public function register() {
        if ($this->isPost) {
            $username = $_POST['username'];
            if($username == null || strlen($username) < 3) {
                $this->addErrorMessage("Username is invalid!");
                $this->redirect("account", "register");
            }

            $password = $_POST['password'];
            $isRegistered = $this->db->register($username, $password);
            if ($isRegistered) {
                $_SESSION['username'] = $username;
                $this->addSuccessMessage("Successful registration!");
                $this->redirect("albums", "index");
            } else {
                $this->addErrorMessage("Register failed!");
                $this->redirect("account", "register");
            }
        }

        $this->renderView(__FUNCTION__);
    }

    public function login() {
        if ($this->isPost) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $isLoggedIn = $this->db->login($username, $password);
            if ($isLoggedIn) {
                $_SESSION['username'] = $username;
                $this->redirect("albums", "index");
            } else {
                $this->addErrorMessage("Login error!");
                $this->redirect("account", "login");
            }
        }

        $this->renderView(__FUNCTION__);
    }

    public function logout() {
        $this->authorize();
        $this->addInfoMessage("Bye, bye, " . $_SESSION['username']);
        unset($_SESSION['username']);
        $this->redirectToUrl("/");
    }
}