<?php
    require_once('php/renderEngine.php');
    require_once('php/database.php');

    function getFieldUser($user, $field) {
        return (isset($user[0][$field]) ? $user[0][$field] : '');
    }
    
    function main() {
        RenderEngine::redirectBasedOnAuth('login', false);
        
        try {
            $connection = new Database();
            $userData = $connection->getUserDataByUserId($_SESSION['id']);
        } catch(Exception) {
            unset($connection);
            RenderEngine::errorCode(500);
            exit();
        }

        $page = RenderEngine::buildPage('dati');
        RenderEngine::replaceAnchor($page, 'username', $userData[0]["usr_name"]);
        RenderEngine::replaceAnchor($page, 'mail', getFieldUser($userData, 'usr_mail')); 
        RenderEngine::showPage($page);
    }

main();
?>
