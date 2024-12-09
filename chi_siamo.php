<?php
    require_once("php/renderEngine.php");

    $page = RenderEngine::buildPage("chi_siamo");
    RenderEngine::showPage($page);

?>
