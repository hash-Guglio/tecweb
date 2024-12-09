<?php

    require_once("php/renderEngine.php");
    require_once("php/database.php");

    RenderEngine::redirectBasedOnAuth("utente");
    $page = RenderEngine::buildPage("signup");

    RenderEngine::showPage($page);

?>
