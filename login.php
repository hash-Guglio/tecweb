<?php
    require_once('php/renderEngine.php');
    require_once('php/database.php');
    
    RenderEngine::redirectBasedOnAuth('user', true);
    
    $page = RenderEngine::buildPage('login');
    RenderEngine::showPage($page);

?>

