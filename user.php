<?php

    require_once("php/renderEngine.php");
    require_once("php/database.php");

    function getUsername($db, $userId) {
        try {
	          $res = $db->getUsernameByUserId($userId);
            unset($db);
            return $res[0]['usr_name'];
        } catch (Exception) {
	          unset($db);
	          RenderEngine::errorCode(500);
	          exit();
        }
    }
    
    function main() {
        RenderEngine::redirectBasedOnAuth('login', false);

        $connection = new Database();
        $username = getUsername($connection, $_SESSION["id"]); 

        $page = RenderEngine::buildPage('user');

        RenderEngine::replaceAnchor($page, 'username', $username);
        
        if ($_SESSION['is_admin'] == 0) RenderEngine::replaceSectionContent($page, 'admin_board', '');
        RenderEngine::showPage($page);
    }

    main();
?>
