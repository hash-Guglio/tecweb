<?php

    require_once("php/renderEngine.php");
    require_once("php/database.php");

    function getUsername($db, $userId) {
        try {
	          $username = $db->getUsernameByUserId($userId);
            unset($db);
            return $username['username'];
        } catch (Exception) {
	          unset($db);
	          RenderEngine::errCode(500);
	          exit();
        }
    }
    
    function main() {
        RenderEngine::redirectBasedOnAuth('login', false);

        $connection = new Database();
        $username = getUsername($_SESSION["id"]); 

        $page = RenderEngine::buildPage($_SERVER['SCRIPT_NAME'], [
            [
                'callback' => 'replaceAnchor',
                'params' => ['username', $username, false]
            ],
            $_SESSION["is_admin"] == 0 ? 
                [
                    'callback' => 'replaceSectionContent',
                    'params' => ['admin', '']
                ]
                : null
        ]);
        
        RenderEngine::showPage($page);
    }

    main();
?>
