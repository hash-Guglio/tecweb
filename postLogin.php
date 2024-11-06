<?php

    require_once("php/renderEngine.php");
    
    function getSanitizedLoginData() {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
        return [$username, $password];
    }


    function authenticateUser($username, $password) {
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

