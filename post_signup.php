<?php
    require_once("php/renderEngine.php");
    require_once("php/database.php");
    
    RenderEngine::redirectBasedOnAuth("utente");

    function getPost($field) {
        return isset($_POST[$field]) ? $_POST[$field] : "";
    }

    function addError(&$err, $message) {
        $errors[] = $message;
    }

    $username = getPost("username");
    $password  = getPost("password");
    $password_confirm = getPost("password_confirm");

    try {
        $connection = new Database();
        $signup = $connection->signupUser($username, $password);
        
        if (!empty($signup)) {
            $userId = $signup[1];
            $login = $connection->authenticateUser($username, $password);
        }

        unset($connection);
    } catch (Exception $e) {
        echo $e;
        //unset($connection);
        RenderEngine::errorCode(500);
        exit();
    }

    $errors = [];
    
    if (!empty($signup) && !empty($login)) {
        $_SESSION["id"] = $login["id"];
        $_SESSION["is_admin"] = $login["is_admin"];
        RenderEngine::redirectBasedOnAuth("utente");
    } else {
        $_SESSION["error"] = ["Questo [en]username[/en] è già stato scelto da un altro utente. Per favore, prova con uno diverso."];
        header("location: signup.php");
        exit();
    }
?>
