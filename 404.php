<?php

    require_once("php/renderEngine.php");

    $page = RenderEngine::buildPage("404");
    RenderEngine::showPage($page);

?>
