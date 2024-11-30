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
            $genders = $connection->getSchemaSelect("utente", "gender");
        } catch(Exception) {
            unset($connection);
            RenderEngine::errorCode(500);
            exit();
        }

        $page = RenderEngine::buildPage('dati');
        RenderEngine::replaceAnchor($page, 'username', $userData[0]["usr_name"]);
        RenderEngine::replaceAnchor($page, 'mail', getFieldUser($userData, 'usr_mail'));  
        RenderEngine::replaceAnchor($page, 'first_name', getFieldUser($userData, 'usr_first_name'));
        RenderEngine::replaceAnchor($page, 'birth_date', getFieldUser($userData, 'usr_birth_date'));

        $genderSection = RenderEngine::getSectionContent($page, 'gender'); 
        $res = '';
        foreach ($genders as $g) {
            $tmp = $genderSection;
            RenderEngine::replaceAnchor($tmp, 'gender', ucfirst($g));  
            RenderEngine::replaceAnchor($tmp, "_select", ($g == getFieldUser($userData, 'usr_gender') ? "selected" : ''));
	          $res .= $tmp;
        }
        RenderEngine::replaceSectionContent($page, "gender", $res);

        RenderEngine::showPage($page);
    }

main();
?>
