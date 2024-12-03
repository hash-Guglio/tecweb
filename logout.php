<?php
    require_once('php/renderEngine.php');

    session_unset();
    session_destroy();

    header('location: index.php');
?>
