<?php

    require_once("php/renderEngine.php");

    function redirectIfNotAuthenticated() {
        if (!isset($_SESSION["id"])) {
            header("location: login.php");
            exit();
        }
    }

    function configurePage($page, $username, $isAdmin) {
        RenderEngine::replaceAnchor($page, "username", RenderEngine::toHtml($username));
        if (!$isAdmin) {
            RenderEngine::replaceSection($page, "admin", "");
        }
        return $page;
    }

    function main() {
        redirectIfNotAuthenticated();

        $page = RenderEngine::buildPage($_SERVER["SCRIPT_NAME"]);
        $isAdmin = $_SESSION["is_admin"] == 1;
    
        $page = configurePage($page, $username, $isAdmin);
        RenderEngine::showPage($page);
    }

    main();
?>
