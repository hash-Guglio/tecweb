<?php
    require_once('php/renderEngine.php');
    require_once('php/database.php');
    
    RenderEngine::redirectBasedOnAuth('utente', true);
    
    $page = RenderEngine::buildPage('login');
    RenderEngine::showPage($page);

?>

