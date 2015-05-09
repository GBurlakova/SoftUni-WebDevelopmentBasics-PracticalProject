<?php

abstract class BaseController {
    protected $controllerName;
    protected $layoutName = DEFAULT_LAYOUT;
    protected $isPost = false;
    protected $isLoggedIn = false;
    protected $isAdmin = false;
    private $db;

    function __construct($controllerName) {
        session_start();
        $this->controllerName = $controllerName;
        $this->onInit();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->isPost = true;
        }

        if(isset($_SESSION['username'])) {
            $this->isLoggedIn = true;
        }

        if(isset($_SESSION['isAdmin'])) {
            if($_SESSION['isAdmin']) {
                $this->isAdmin = true;
            }
        }

        if($this->isLoggedIn){
            if($this->isAdmin) {
                $this->layoutName = 'admin';
            } else {
                $this->layoutName = 'user';
            }
        } else {
            $this->layoutName = DEFAULT_LAYOUT;
        }

        $this->db = new AccountModel();
    }

    protected function onInit() {
        // Functionality of the subclasses
    }

    public function index() {
        // Functionality of the subclasses
    }

    public function authorize($role = null) {
        if(!$this->isLoggedIn) {
            $this->addErrorMessage('Please login first.');
            $this->redirect('account', 'login');
        }

        if($role) {
            $username = $_SESSION['username'];
            $roleVerified = $this->db->verifyUserRole($username, $role);
            if(!$roleVerified) {
                $this->addErrorMessage('You do not have permissions needed.');
                $this->redirect('account', 'logout');
            }
        }
    }

    public function renderText($text) {
        echo htmlspecialchars($text);
    }

    public function renderView($viewName = "Index", $includeLayout = true) {
        // Include header
        if ($includeLayout) {
            $headerFile = 'views/layouts/' . $this->layoutName . '/header.php';
            include_once($headerFile);
        }

        // Include main page html
        $viewFileName = 'views/' . $this->controllerName . '/' . $viewName . '.php';
        include_once($viewFileName);
    }

    // redirect functions
    public function redirectToUrl($url) {
        header('Location: ' . $url);
        die;
    }

    public function redirect($controllerName, $action = null, $params = null) {
        $url = '/photo-album/' . $controllerName;
        if($action) {
           $url .= '/' . $action;
        }

        if($params) {
            $paramsEncoded = array_map($params, 'urlencode');
            $url .= implode('/', $paramsEncoded);
        }

        $this->redirectToUrl($url);
    }

    // notifications
    public function gerUserId() {
        $username = $_SESSION['username'];
        $userId = $this->db->getIserId($username);
        return $userId;
    }

    public function addInfoMessage($msg) {
        $this->addMessage($msg, 'info');
    }

    public function addSuccessMessage($msg) {
        $this->addMessage($msg, 'success');
    }

    public function addErrorMessage($msg) {
        $this->addMessage($msg, 'error');
    }

    private function addMessage($msg, $type) {
        session_start();
        if (!isset($_SESSION['messages'])) {
            $_SESSION['messages'] = array();
        };

        array_push($_SESSION['messages'],
            array('text' => $msg, 'type' => $type));
    }
}