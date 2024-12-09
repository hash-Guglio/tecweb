<?php
    require_once("php/renderEngine.php");
    require_once("php/database.php");
    
    RenderEngine::redirectBasedOnAuth("login", false);

    $page = RenderEngine::buildPage("statistiche");
    RenderEngine::showPage($page);
?>
