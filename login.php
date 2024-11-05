<?php

    require_once("php/renderEngine.php");

    function checkAuthentication() {
        if (isset($_SESSION["id"])) {
            header("location: user.php");
            exit();
        }
    }

    function main() {
        checkAuthentication(); 

        $page = RenderEngine::buildPage($_SERVER["SCRIPT_NAME"]);
        RenderEngine::showPage($page);
    }

    main();
?>

