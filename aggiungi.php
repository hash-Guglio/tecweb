<?php
    require_once('php/renderEngine.php');

    RenderEngine::redirectBasedOnAuth('login', false);
    RenderEngine::redirectBasedOnAuth('login', true, 'admin');

    $page = RenderEngine::buildPage('aggiungi');

    RenderEngine::showPage($page);
?>
