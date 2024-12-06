<?php

    require_once('php/renderEngine.php');
    require_once('php/database.php');
    require_once('php/debug.php');
    
    function authenticateUser($username, $password) : array {
        try {
	          $connection = new Database();
            $loginData = $connection->authenticateUser($username, $password);
            unset($connection);
            return $loginData;
        } catch (Exception $e) {
            unset($connection);
            RenderEngine::errorCode(500);
	          exit();
        }
        
        return [];
    }   

    function setUserSession($loginData) {
        $_SESSION['id'] = $loginData['id'];
        $_SESSION['is_admin'] = $loginData['is_admin'];
    }

    function main() {
        RenderEngine::redirectBasedOnAuth('user', true);

        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $loginData = authenticateUser($username, $password);

        if (!empty($loginData)) {
            setUserSession($loginData);
            header('location: utente.php');
            exit();

        } else {
            $_SESSION['error'] = ['Credenziali errate. Riprova.'];
            header('location: login.php');
            exit();
        }
    }

main();

