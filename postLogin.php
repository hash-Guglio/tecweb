<?php

    require_once("php/renderEngine.php");
    
    function getSanitizedLoginData() {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
        return [$username, $password];
    }


    function authenticateUser($username, $password) : array {
        try {
	          $connection = new Database();
	          $loginData = $connection->authenticateUser($username, $password);
	          unset($connection);
            return $loginData;
        } catch (Exception) {
	          unset($connection);
	          RenderEngine::errCode(500);
	          exit();
        }
        
        return [];
    }   

    function setUserSession($loginData) {
        $_SESSION['id'] = $loginData['id'];
        $_SESSION['is_admin'] = $loginData['is_admin'];
    }

    function main() {
        [$username, $password] = getSanitizedLoginData();
        $loginData = authenticateUser($username, $password);

        if (!empty($loginData)) {
            setUserSession($loginData);
            header('location: user.php');
            exit();

        } else {
            $_SESSION['error'] = ['Credenziali errate. Riprova.'];
            header('location: login.php');
            exit();
        }
    }

main();

