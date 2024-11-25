<?php
    require_once("php/renderEngine.php");
    require_once("php/database.php");
    
    if (!isset($search_type)) {
        RenderEngine::errorCode(404);
        exit();
    } 

    $question = "ricetta1";/*$_GET["question"] ?? "";*/ 
    $filter_name = "rdish_type";/*$_GET["filter_name"] ?? "";*/  
    $rdish_type = "primo";/*$_GET["rdish_type"] ?? "";*/
    $rallgs = "";/*$_GET["rallgs"] ?? "";*/
    $itemsPerPage = 16;
    
    $currentPage = intval($_GET["page"] ?? 0);
    $offset = $itemsPerPage * $currentPage;
    
    try {
        $connection = new Database();
        $result = handleSearch($connection, $search_type, $question, $filter_name, $rdish_type, $rallgs, $itemsPerPage, $offset);    
        unset($connection);
    } catch (Exception $e) {
        RenderEngine::errorCode(500);
        exit();
    }

    /*if (empty($result)) {
        RenderEngine::errorCode(404);
        exit();
    }*/

    $page = RenderEngine::buildPage("cerca", "cerca_$search_type");
    RenderEngine::showPage($page);



    function handleSearch($connection, $search_type, $question, $filter_name, $rdish_type, $rallgs, $liimit_res, $offset) {
        switch ($search_type) {
            case "ricetta":
                return searchRecipe($connection, $question, $filter_name, $rdish_type, $rallgs, $liimit_res, $offset);
        
        default:
            return [];    
        }

    }

    function searchRecipe($connection, $question, $filter_name, $rdish_type, $rallgs, $itemsPerPage, $offset) {
        if ($filter_name === "rdish_type" && $rdish_type) {
            //return $connection->searchRecipeByType($question, $itemsPerPage, $offset, $rdish_type);
        } else if ($filter_name === "allgs" && $rallgs) {
            //return $connection->searchRecipeByAllgs($question, $itemsPerPage, $offset, $rallgs);
        }

        //return $connection->searchRecipe($question, $itemsPerPage, $offset);
    }
?>
