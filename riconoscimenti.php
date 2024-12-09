<?php

    require_once("php/renderEngine.php");

    $page = RenderEngine::buildPage("riconoscimenti");
    RenderEngine::showPage($page);

?>
