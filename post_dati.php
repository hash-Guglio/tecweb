<?php
    require_once('php/renderEngine.php');
    require_once('php/database.php');
    
    function getInput() : array {
        $fields = [ "username", "mail", "first_name", "gender", "birth_date", "is_vegan",
            "usr_is_celiac", "is_lactose_intolerant","old_password", "new_password" "new_password_confirm"];

        $result = [];

        foreach ($fields as $field) {
            $result[$field] = isset($postData[$field]) ? $postData[$field] : "";
        }

        return $result;
    }

    function updateUserData($connessione, $user_id, $postData) {
        return $connessione->updateUtente(
            $user_id,
            $postData["username"],
            $postData["mail"],
            $postData["first_name"],
            $postData["gender"],
            $postData["data_nascita"],
            isset($postData["new_password"]) ? $postData["new_password"] : null
    )[0];
}

    function main() {
        RenderEngine::redirectBasedOnAuth('login', false);
        $res = getPostData();        
    }

main();
?>
