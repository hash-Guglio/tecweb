<?php
    require_once('php/renderEngine.php');
    $page = RenderEngine::buildPage($_SERVER["SCRIPT_NAME"]);
    RenderEngine::showPage($page);
?>





