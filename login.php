<?php

    require_once("php/renderEngine.php");
    require_once("php/database.php");
    
    RenderEngine::redirectBasedOnAuth('user', true);
    
    $page = RenderEngine::buildPage($_SERVER['SCRIPT_NAME']);
    RenderEngine::showPage($page);

?>

