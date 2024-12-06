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

        if (in_array($filterName, ["pro", "fat", "carbo", "cal"])) {
            $filterValue = $_GET["order"];
        }
        
        $itemsPerPage = 4;
        
        $currentPage = intval($_GET["page"] ?? 0);
        $offset = $itemsPerPage * $currentPage;

        $filters = [];

        try {
            $connection = new Database();

            $filterNames = [
                "ricette" => ["dish_type", "allgs"],
                "ingredienti" => ["order"],
                //da nutriente (asc/disc)
                //
            ];

            foreach ($filterNames[$searchType] as $fName) {
                $filterLabel = $connection->getSchemaSelect($searchType, $fName);
                $filters[$fName] = $filterLabel;
            }

            switch ($searchType) {
                case "ricette":
                    $filters['filter'] = [
                        ['id' => 'dish_type', 'it' => 'Tipo piatto'],
                        ['id' => 'allgs', 'it' => 'Allergeni']
                    ];
                    break;
                case "ingredienti":
                    $filters['filter'] = [
                        ['id' => 'cal', 'it' => 'Calorie'],
                        ['id' => 'prt', 'it' => 'Proteine'], 
                        ['id' => 'fat', 'it' => 'Grassi'],
                        ['id' => 'carbo', 'it' => 'Carboidrati'],
                    ];
                    foreach ($filters['filter'] as $filter) {
                        if ($filter['id'] === $filterName) {
                            $filterName = $filter['it'];
                        }
                     }
                    break;
            }

            $results = handleSearch($connection, $searchType, $question, $filterName, $filterValue, $itemsPerPage, $offset);    
            unset($connection);
        } catch (Exception $e) {
            echo $e;
            //RenderEngine::errorCode(500);
            exit();
        }

        if (!isset($results)) {
            RenderEngine::errorCode(404);
            exit();
        }
        
        $page = RenderEngine::buildPage("cerca", "cerca_$searchType");

        foreach (array_keys($filters) as $fName) {
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
            case "ingredienti":
                $mdesc = "Scopri gli ingredienti, le loro proprietà alimentari e i benefici su fioridisapore";
                break;
        }

        RenderEngine::replaceAnchor($page, "searchtype_kw", ($question != "")?(", ". $question . ","):","); 
        RenderEngine::replaceAnchor($page, "searchtype_desc", $mdesc);
        RenderEngine::replaceAnchor($page, "breadcrumb", $bcrb);
        RenderEngine::replaceAnchor($page, "search_type", $searchType);
        RenderEngine::replaceAnchor($page, "search_value", $question);
        RenderEngine::replaceAnchor($page, "title", $title);


        if (!empty($results["result"])) {
            RenderEngine::replaceSectionContent($page, "results", buildResultsSection($page, $results["result"], $searchType));
	          $navigationLink = "cerca_$searchType.php?question=$question" . (($searchType == "ricette" && $filterName) ? ("&filter=" . $filterName. "&dish_type=" . $filterValue . "&allgs=" . $filterValue) : "");
            buildResultNavbar($page, $results, $currentPage, $itemsPerPage, $navigationLink);
        } else {
            RenderEngine::replaceSectionContent($page, "results", "");
            RenderEngine::replaceSectionContent($page, "navigation_bottom", "");
            RenderEngine::replaceAnchor($page, "message_result", "Questa ricerca non ha prodotto risultati");
        }
        
        RenderEngine::showPage($page);

    }

    function handleSearch($connection, $searchType, $question, $filterName, $filterValue, $liimit_res, $offset) {
        switch ($searchType) {
            case "ricette":
                return searchRecipe($connection, $question, $filterName, $filterValue, $liimit_res, $offset);
            case "ingredienti":
                return searchIngredient($connection, $question, $filterName, $filterValue, $liimit_res, $offset);
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

    function searchIngredient($connection, $question, $filterName, $filterValue, $itemsPerPage, $offset) {
    
        if ($filterName !== '' && $filterValue !== '') {
            return $connection->searchIngredientByNut($question, $itemsPerPage, $offset, $filterName, strtoupper($filterValue));
        }
        return $connection->searchIngredient($question, $itemsPerPage, $offset);    
    }
    
    function buildFilterSection(&$page, $filterName, $filterItems, $filterNameSelected, $selectedValue) {
        $prev = $filterName;

        if ($filterName === "asc" || $filterName === "desc") {
            $filterName = "order";
        };

        $template = RenderEngine::getSectionContent($page, $filterName);
        $result = "";

        foreach ($filterItems[$prev] as $item) {
                $filter = $template;
                RenderEngine::replaceAnchor($filter, "value", $item["id"] ?? $item);
                RenderEngine::replaceAnchor($filter, "name", $item["dt_type"] ?? $item["rst_type"] ?? $item["it"] ?? $item["ntr_name"]);
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
                $optionFilterField = ($filterName == "dish_type")?"dt_type":"rst_type";
                $intFilterValue = intval($filterValue) - 1;
                $res = " filtrate per {$filters[$filterName][$intFilterValue][$optionFilterField]}";     
                break;
            case "ingredienti": 
                break;
            default:
                break;
        }
        return $res;
    }
    
    function buildResultsSection($page, $results, $searchType) {
        $cards = "";
        $resSection = RenderEngine::getSectionContent($page, "results");
        $template = RenderEngine::getSectionContent($page, "card");
                
        foreach ($results as $result) {
            $card = $template;
            RenderEngine::replaceAnchor($card, "name", $result["name"]);
            RenderEngine::replaceAnchor($card, "link", "{$searchType}.php?id={$result['id']}");
            RenderEngine::replaceAnchor($card, "webp", "pics/" . $result["image"] . ".webp");
            RenderEngine::replaceAnchor($card, "image", "pics/" . $result["image"] . ".jpg");
            RenderEngine::replaceAnchor($card, "time", $result["ready_in"] ?? "");
            RenderEngine::replaceAnchor($card, "servings", $result["servings"] ?? "");
            $cards .= $card;
        }
        
        RenderEngine::replaceSectionContent($resSection, "card", $cards);

        return $resSection;
    }
    
    function buildResultNavbar(&$page, $results, $currentPage, $itemsPerPage, $navigationLink) {
        $message = ("Pagina ". ($currentPage + 1)  . " su ". ceil($results["count"][0]["total"]/ $itemsPerPage) . ". Risultati totali: " . $results["count"][0]["total"]);
        RenderEngine::replaceAnchor($page, "message_result", $message);
        RenderEngine::replaceAnchor($page, "message_result_bottom", $message); 

        if ($currentPage > 0) { 
            RenderEngine::replaceAnchor($page, "prev_page", ($navigationLink. "&page=" . ($currentPage - 1) . "#results"));     
        } 
        else { 
            RenderEngine::replaceSectionContent($page, "prev_page", ""); 
        }
        if (($currentPage + 1) < ceil($results["count"][0]["total"] / $itemsPerPage)) {
            RenderEngine::replaceAnchor($page, "next_page", ($navigationLink . "&page=" . ($currentPage + 1) . "#results"));
        }
        else {
            RenderEngine::replaceSectionContent($page, "next_page", "");
        }
    }
    
    main($searchType);
?>
