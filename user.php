<?php

    require_once("php/renderEngine.php");


    function getUsername($db, $userId) {
    }
    
    function main() {
        RenderEngine::redirectIfNotAuthenticated('login');

        $page = RenderEngine::buildPage($_SERVER['SCRIPT_NAME']);
        $isAdmin = $_SESSION['is_admin'] == 1;

        RenderEngine::showPage($page);
    }

    main();
?>
