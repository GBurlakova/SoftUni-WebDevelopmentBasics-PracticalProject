<?php
// Settings
require_once('includes/config.php');

// Process current url - get controller name, action name and params
$requestParts = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$controllerName = DEFAULT_CONTROLLER;
$action = DEFAULT_ACTION;

if(count($requestParts) >= URL_WITH_CONTROLLER_MIN_LENGTH && $requestParts[CONTROLLER_NAME_URL_POSITION] != ''){
    $controllerName = $requestParts[CONTROLLER_NAME_URL_POSITION];
}

if(count($requestParts) >= URL_WITH_ACTION_MIN_LENGTH && $requestParts[ACTION_NAME_URL_POSITION] != ''){
    $action = $requestParts[ACTION_NAME_URL_POSITION];
}

$params = array_splice($requestParts, URL_PARAMS_POSITION);
$controllerClassName = ucfirst(strtolower($controllerName)) . 'Controller';
$controllerFileName = "controllers/" . $controllerClassName . '.php';

if (class_exists($controllerClassName)) {
    $controller = new $controllerClassName($controllerName, $action);
} else {
    die("Cannot find controller '$controllerName' in class '$controllerFileName'");
}

if (method_exists($controller, $action)) {
    call_user_func_array(array($controller, $action), $params);
} else {
    die("Cannot find action '$action' in controller '$controllerClassName'");
}

// Auto load controllers and models
function __autoload($class_name) {
    if (file_exists("controllers/$class_name.php")) {
        include "controllers/$class_name.php";
    }

    if (file_exists("models/$class_name.php")) {
        include "models/$class_name.php";
    }
}