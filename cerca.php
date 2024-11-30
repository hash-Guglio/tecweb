<?php

    require_once("php/renderEngine.php");
    require_once("php/database.php");

    
    if (!isset($searchType)) {
        RenderEngine::errorCode(404);
        exit();
    }

    function main($searchType) {

        $question = $_GET["question"] ?? ""; 
        $filterName = $_GET["filter"] ?? "";  
        
        $filterValue = $_GET[$filterName] ?? "";

        $itemsPerPage = 16;
        
        $currentPage = intval($_GET["page"] ?? 0);
        $offset = $itemsPerPage * $currentPage;
        
        $filters = [];

        try {
            $connection = new Database();

            $filterNames = [
                "ricette" => ["dish_type", "allgs"],
                "ingrediente" => ["allgs"]
            ];

            foreach ($filterNames[$searchType] as $fName) {
                $filterLabel = $connection->getSchemaSelect($searchType, $fName);
                $filters[$fName] = $filterLabel;
            }

            $filters['filter'] = [
                ['id' => 'dish_type', 'it' => 'Tipo piatto'],
                ['id' => 'allgs', 'it' => 'Allergeni']
            ];

            $results = handleSearch($connection, $searchType, $question, $filterName, $filterValue, $itemsPerPage, $offset);    
            unset($connection);
        } catch (Exception $e) {
            RenderEngine::errorCode(500);
            exit();
        }

        if (!isset($results)) {
            RenderEngine::errorCode(404);
            exit();
        }
        
        $page = RenderEngine::buildPage("cerca", "cerca_$searchType");

        foreach (["filter", "dish_type", "allgs"] as $fName) {
            buildFilterSection($page, $fName, $filters, $filterName, $filterValue);
        }    

        $bcrb = ucfirst($searchType);
        $bheader = ""; 
        
        if ($filterName != "" && $filterValue != "") {
            $bheader = buildHeaderFilter($page, $question, $searchType, $filters, $filterName, $filterValue);
            $bcrb .= $bheader;
        }
        
        $title = (($question != "") ? ('"' . $question . '" | ') : "") . "Cerca {$searchType}" . $bheader;
        $mdesc = "";

        switch ($searchType) {
            case "ricette":
                $mdesc = "Trova la ricetta perfetta per te, filtrando i risultati per tipo o allergene.";
                break;
        }

        RenderEngine::replaceAnchor($page, "searchtype_kw", $question); 
        RenderEngine::replaceAnchor($page, "searchtype_desc", $mdesc);
        RenderEngine::replaceAnchor($page, "breadcrumb", $bcrb);
        RenderEngine::replaceAnchor($page, "search_type", $searchType);
        RenderEngine::replaceAnchor($page, "search_value", $question);
        RenderEngine::replaceAnchor($page, "title", $title);


        if (!empty($results["recipe"])) {
            RenderEngine::replaceSectionContent($page, "results", buildResultsSection($page, $results["recipe"], $searchType));
            buildResultNavbar($page, $results, $currentPage, $itemsPerPage);
        } else {
            RenderEngine::replaceSectionContent($page, "results", "");
            RenderEngine::replaceAnchor($page, "message_result", "Questa ricerca non ha prodotto risultati");
        }


        //if (!empty($result["recipe"])) RenderEngine::replaceAnchor($page, "name", $result["recipe"][0]["name"]);     
        
        RenderEngine::showPage($page);

    }

    function handleSearch($connection, $searchType, $question, $filterName, $filterValue, $liimit_res, $offset) {
        switch ($searchType) {
            case "ricette":
                return searchRecipe($connection, $question, $filterName, $filterValue, $liimit_res, $offset);
        
        default:
            return [];    
        }

    }

    function searchRecipe($connection, $question, $filterName, $filterValue, $itemsPerPage, $offset) {
        
        if ($filterName === "dish_type" && $filterValue) { 
            return $connection->searchRecipeByType($question, $itemsPerPage, $offset, $filterValue);
        } else if ($filterName === "allgs" && $filterValue) {
            return $connection->searchRecipeByAllgs($question, $itemsPerPage, $offset, $filterValue);
        }
        return $connection->searchRecipe($question, $itemsPerPage, $offset);
    }
    
    function buildFilterSection(&$page, $filterName, $filterItems, $filterNameSelected, $selectedValue) {

        $template = RenderEngine::getSectionContent($page, $filterName);
        $result = "";
        foreach ($filterItems[$filterName] as $item) {
                $filter = $template;
                RenderEngine::replaceAnchor($filter, "value", $item["id"]);
                RenderEngine::replaceAnchor($filter, "name", $item["dt_type"] ?? $item["restriction_type"] ?? $item["it"]);
                RenderEngine::replaceAnchor($filter, "select", ($item["id"] == (($filterName != "filter")?$selectedValue:$filterNameSelected)) ? "selected" : "");
                $result .= $filter;
        }

        RenderEngine::replaceSectionContent($page, $filterName, $result);
    }

    function buildHeaderFilter(&$page, $question, $searchType, $filters, $filterName, $filterValue) {
        $res = "";
        switch ($searchType) {
            case "ricette":
                $optionFilterType = ($filterName == "dish_type")?0:1;
                $optionFilterField = ($filterName == "dish_type")?"dt_type":"restriction_type";
                $intFilterValue = intval($filterValue) - 1;
                $res = " filtrate per {$filters[$filterName][$intFilterValue][$optionFilterField]}";     
               //"({$filters["filter"][$optionFilterType]["it"]})";
                break;
            default:
                break;
        }
        return $res;
    }
    
    function buildResultsSection($page, $results, $searchType) {
        $cards = "";
        $template = RenderEngine::getSectionContent($page, "card");
        
        foreach ($results as $result) {
            $card = $template;
            RenderEngine::replaceAnchor($card, "name", $result["name"]);
            RenderEngine::replaceAnchor($card, "link", "{$searchType}.php?id={$result['id']}");
            $cards .= $card;
        }
        
        return $cards;
    }
    
    function buildResultNavbar(&$page, $results, $currentPage, $itemsPerPage) {
        $message = ("Pagina ". ($currentPage + 1)  . " su ". ceil($results["count"][0]["total"]/ $itemsPerPage) . ". Risultati totali: " . $results["count"][0]["total"]);
        RenderEngine::replaceAnchor($page, "message_result", $message);
    }
    
    main($searchType);
?>
