<?php
    require_once('php/renderEngine.php');
    require_once('php/database.php');
    
    function getPostData() : array {
        $fields = [ "username", "mail", "first_name", "gender", "birth_date", "is_vegan",
            "is_celiac", "is_lactose_intolerant","old_password", "new_password", "new_password_confirm"];

        $result = [];

        foreach ($fields as $field) {
            $result[$field] = isset($_POST[$field]) ? $_POST[$field] : "";

        }
        return $result;
    }

    function updateUserData($connessione, $user_id, $postData) {
        return $connessione->persistUser(
            id: $user_id,
            usr_name: $postData["username"],
            usr_mail: $postData["mail"],
            usr_first_name: $postData["first_name"],
            usr_gender: $postData["gender"],
            usr_birth_date: $postData["birth_date"],
            usr_new_password: isset($postData["new_password"]) ? $postData["new_password"]: null,
            usr_restrictions: ["is_vegan" => $postData["is_vegan"], "is_celiac" => $postData["is_celiac"], "is_lactose_intolerant" => $postData["is_lactose_intolerant"]]
        )[0];
    }

    function main() {
        RenderEngine::redirectBasedOnAuth('login', false);
        $data = getPostData();
        
        try {
            $connection = new Database();
            updateUserData($connection, $_SESSION['id'], $data);

        } catch (Exception $e) {
            unset($connection);
            RenderEngine::errorCode(500);
            exit();
        }

        // $_SESSION["error"] = ["La password corrente è errata. Nessuna modifica è stata effettuata."];
        // $_SESSION["success"] = ["Nessuna modifica è stata effettuata."];
        $_SESSION["success"] = ["I dati sono stati aggiornati correttamente."];

        header("Location: dati.php");

    }

main();
?>
