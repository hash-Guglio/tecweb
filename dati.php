<?php
    require_once('php/renderEngine.php');
    require_once('php/database.php');

    function getFieldUser($userData, $field) {
        if (is_array($userData)) {
            foreach ($userData as $user) {
                if (isset($user[$field])) {
                    return $user[$field];
                }
            }
            }
        return '';
    }

    function hasRestriction($userData, $restrictionId) {
        foreach ($userData as $user) {
            if (isset($user['rst_id']) && $user['rst_id'] == $restrictionId) {
                return true;
            }
        }
        return false;
    }

    function main() {
        RenderEngine::redirectBasedOnAuth('login', false);
        
        try {
            $connection = new Database();
            $userData = $connection->getUserDataByUserId($_SESSION['id']);
            $genders = $connection->getSchemaSelect("utente");
            $allgs = $connection->getSchemaSelect("ricette", "allgs");
        } catch(Exception $e) {
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
            RenderEngine::replaceAnchor($tmp, "gender", ucfirst($g));  
            RenderEngine::replaceAnchor($tmp, "gender_select", ($g == getFieldUser($userData, 'usr_gender') ? "selected" : ''));
	          $res .= $tmp;
        }
        RenderEngine::replaceSectionContent($page, "gender", $res);

        
        $allgsSection = RenderEngine::getSectionContent($page, 'allgs');
        $res = '';
        
        $allgsMap = [
            "Vegano" => "is_vegan",
            "Intollerante al lattosio" => "is_lactose_intolerant",
            "Celiaco" => "is_celiac"
        ];
        
        foreach ($allgs as $a) {
            $tmp = $allgsSection;
            RenderEngine::replaceAnchor($tmp, "allgs", ucfirst($a["rst_disorder_name"] ?? $a["rst_type"]));
            RenderEngine::replaceAnchor($tmp, "allgs_name", $allgsMap[$a["rst_disorder_name"] ??$a["rst_type"]]);
            RenderEngine::replaceAnchor($tmp, "allgs_value", $a["id"]);
            $isChecked = hasRestriction($userData, $a["id"]) ? "checked" : "";
            RenderEngine::replaceAnchor($tmp, "allgs_checked", $isChecked);
            $res .= $tmp;
        }

        RenderEngine::replaceSectionContent($page, "allgs", $res);

        RenderEngine::showPage($page);
    }

main();
?>
