<?php

require_once("php/renderEngine.php");
require_once("php/database.php");

function main($searchType) {
    if (!isset($searchType)) {
        RenderEngine::errorCode(404);
        exit();
    }

    $question = $_GET["question"] ?? "";
    $filterName = $_GET["filter"] ?? "";
    $filterValue = $_GET[$filterName] ?? "";

    if (in_array($filterName, ["prt", "fat", "carbo", "cal"])) {
        $filterValue = $_GET["order"] ?? "";
    }

    $itemsPerPage = 8;
    $currentPage = intval($_GET["page"] ?? 0);
    $offset = $itemsPerPage * $currentPage;

    $filters = [];
    try {
        $connection = new Database();
        $filters = buildFilters($connection, $searchType);
        $results = handleSearch($connection, $searchType, $question, $filterName, $filterValue, $itemsPerPage, $offset);
        unset($connection);
    } catch (Exception $e) {
        renderEngine::errorCode(500);
        exit();
    }

    if (empty($results)) {
        RenderEngine::errorCode(404);
        exit();
    }

    renderSearchPage($searchType, $question, $filterName, $filterValue, $filters, $results, $itemsPerPage, $currentPage);
    }

    function buildFilters($connection, $searchType) {
        $filterConfig = [
            "ricette" => ["dish_type", "allgs"],
            "ingredienti" => ["order"],
        ];

        $filters = [];
        foreach ($filterConfig[$searchType] as $filterKey) {
            $filters[$filterKey] = $connection->getSchemaSelect($searchType, $filterKey);
        }

        $customFilters = [
            "ricette" => [
                "filter" => [
                    'dish_type' => 'Tipo piatto',
                    'allgs' => 'Allergeni'
                ]
            ],
            "ingredienti" => [
                "filter" => [
                    'cal' => 'Calorie',
                    'prt' => 'Proteine',
                    'fat' => 'Grassi',
                    'carbo' => 'Carboidrati'
                ]
            ]
        ];

        if (isset($customFilters[$searchType])) {
            $filters = array_merge($filters, $customFilters[$searchType]);
        }

        return $filters;
    }

    
    function handleSearch($connection, $searchType, $question, $filterName, $filterValue, $itemsPerPage, $offset) {
        switch ($searchType) {
            case "ricette":
                return searchRecipes($connection, $question, $filterName, $filterValue, $itemsPerPage, $offset);
            case "ingredienti":
                return searchIngredients($connection, $question, $filterName, $filterValue, $itemsPerPage, $offset);
            default:
                return [];
        }
    }


    function searchRecipes($connection, $question, $filterName, $filterValue, $itemsPerPage, $offset) {
        if ($filterName === "dish_type") { 
            return $connection->searchRecipeByType($question, $itemsPerPage, $offset, intval($filterValue) + 1);
        }
        if ($filterName === "allgs") {        
            return $connection->searchRecipeByAllgs($question, $itemsPerPage, $offset, intval($filterValue) + 1);
        }
        return $connection->searchRecipe($question, $itemsPerPage, $offset);
    }

    function searchIngredients($connection, $question, $filterName, $filterValue, $itemsPerPage, $offset) {
        if (!empty($filterName) && !empty($filterValue)) {
            return $connection->searchIngredientByNut($question, $itemsPerPage, $offset, $filterName, strtoupper($filterValue));
        }
        return $connection->searchIngredient($question, $itemsPerPage, $offset);
    }

    function getDescription($searchType): string {
        switch ($searchType) {
            case "ricette":
                return "Trova la ricetta perfetta per te, filtrando i risultati per tipo o allergene.";
            case "ingredienti":
                return "Scopri gli ingredienti, le loro proprietà alimentari e i benefici su fioridisapore.";
            default:
                return "Effettua una ricerca per ottenere i risultati desiderati.";
        }
    }

    function buildNavigationLink($searchType, $question, $filterName, $filterValue) {
        $navigationLink = "cerca_$searchType.php?question=$question";

        if ($filterName) {
            $navigationLink .= "&filter=" . $filterName;

            if ($filterValue) {
                $navigationLink .= "&" . $filterName . "=" . $filterValue;
            }
        }

        return $navigationLink;
    }

    function buildResultNavbar(&$page, $results, $currentPage, $itemsPerPage, $navigationLink) {
        $totalResults = $results["count"][0]["total"];
        $totalPages = ceil($totalResults / $itemsPerPage);

        $message = "Pagina " . ($currentPage + 1) . " su " . $totalPages . ". Risultati totali: " . $totalResults;
        
        RenderEngine::replaceAnchor($page, "message_result", $message);
        RenderEngine::replaceAnchor($page, "message_result_bottom", $message);

        if ($currentPage > 0) {
            RenderEngine::replaceAnchor($page, "prev_page", $navigationLink . "&page=" . ($currentPage - 1) . "#results");
        } else {
            RenderEngine::replaceSectionContent($page, "prev_page", "");
        }

        if (($currentPage + 1) < $totalPages) {
            RenderEngine::replaceAnchor($page, "next_page", $navigationLink . "&page=" . ($currentPage + 1) . "#results");
        } else {
            RenderEngine::replaceSectionContent($page, "next_page", "");
        }
    }

    function renderSearchPage($searchType, $question, $filterName, $filterValue, $filters, $results, $itemsPerPage, $currentPage) {
        $page = RenderEngine::buildPage("cerca", "cerca_$searchType");

        foreach (array_keys($filters) as $filterKey) {
            buildFilterSection($page, $filterKey, $filters, $filterName, $filterValue);
        }

        $breadcrumb = ucfirst($searchType);

        $header = buildHeader($page, $question, $searchType, $filters, $filterName, $filterValue);
        $title = (($question != "") ? '"' . $question . '" | ' : "") . "Cerca {$searchType} $header";

        RenderEngine::replaceAnchor($page, "searchtype_kw", $question ? ", $question," : ",");
        RenderEngine::replaceAnchor($page, "searchtype_desc", getDescription($searchType));
        RenderEngine::replaceAnchor($page, "breadcrumb", $breadcrumb . $header);
        RenderEngine::replaceAnchor($page, "search_type", $searchType);
        RenderEngine::replaceAnchor($page, "search_value", $question);
        RenderEngine::replaceAnchor($page, "title", $title);

        if (!empty($results["result"])) {
            RenderEngine::replaceSectionContent($page, "results", buildResultsSection($page, $results["result"], $searchType));
            $navigationLink = buildNavigationLink($searchType, $question, $filterName, $filterValue);
            buildResultNavbar($page, $results, $currentPage, $itemsPerPage, $navigationLink);
        } else {
            RenderEngine::replaceSectionContent($page, "results", "");
            RenderEngine::replaceAnchor($page, "message_result", "Questa ricerca non ha prodotto risultati");
            RenderEngine::replaceAnchor($page, "message_result_bottom", ""); 
            RenderEngine::replaceSectionContent($page, "navigation_bottom", "");
        }

        RenderEngine::showPage($page);
    }

    function buildFilterSection(string &$page, string $filterKey, array $filterOptions, string $selectedFilter, string $selectedValue): void {
        $originalKey = $filterKey;

        if (in_array($filterKey, ['asc', 'desc'])) {
            $filterKey = 'order';
        }

        $template = RenderEngine::getSectionContent($page, $filterKey);
        $filterHtml = '';

        foreach ($filterOptions[$originalKey] as $key => $option) {
            $filterItem = $template;
            RenderEngine::replaceAnchor($filterItem, 'value', $key);
            RenderEngine::replaceAnchor($filterItem, 'name', ucfirst( $option["dt_type"] ?? $option["rst_type"] ?? $option["it"] ?? $option));
            RenderEngine::replaceAnchor(
            $filterItem,
                'select',
                ($option['id'] ?? $option) === (($filterKey !== 'filter') ? $selectedValue : $selectedFilter) ? 'selected' : ''
            );
            $filterHtml .= $filterItem;
        }

        RenderEngine::replaceSectionContent($page, $filterKey, $filterHtml);
    }

    function buildHeader(string &$page, string $query, string $searchType, array $filters, string $filterName, string $filterValue): string {
        $headerText = '';

        if (empty($filterName) || empty($filterValue)) return '';
        
        switch ($searchType) {
            case 'ricette':
                $filterIndex = ($filterName === 'dish_type') ? 0 : 1;
                $filterField = ($filterName === 'dish_type') ? 'dt_type' : 'rst_type';
                $filterValueIndex = intval($filterValue);
                $headerText = ' filtrate per "' . ($filters[$filterName][$filterValueIndex][$filterField] ?? '') . '"';
                break;
        
            case 'ingredienti':
                $headerText = ' filtrati per "' . $filters["filter"][$filterName] . '" ordinati dal ' . $filters["order"][$filterValue];            
                break;
        }

        return $headerText;
    }

    function buildResultsSection(string $page, array $results, string $searchType): string {
        $cardsHtml = '';
        $resultsSection = RenderEngine::getSectionContent($page, 'results');
        $cardTemplate = RenderEngine::getSectionContent($page, 'card');

        foreach ($results as $result) {
            $cardHtml = $cardTemplate;
            RenderEngine::replaceAnchor($cardHtml, 'name', $result['name']);
            RenderEngine::replaceAnchor($cardHtml, 'link', "{$searchType}.php?id={$result['id']}");
            RenderEngine::replaceAnchor($cardHtml, 'webp', "pics/" . ($result['image'] ?? 'placeholder') . ".webp");
            RenderEngine::replaceAnchor($cardHtml, 'image', "pics/" . ($result['image'] ?? 'placeholder') . ".jpg");
            RenderEngine::replaceAnchor($cardHtml, 'time', $result['ready_in'] ?? '');
            RenderEngine::replaceAnchor($cardHtml, 'category', $result['category'] ?? '');
            RenderEngine::replaceAnchor($cardHtml, 'servings', $result['servings'] ?? '');

            if ($searchType === "ricette") {
                RenderEngine::replaceSectionContent($cardHtml, "ingredient_value", '');
            }
            if ($searchType === "ingredienti") {    
                RenderEngine::replaceSectionContent($cardHtml, "recipe_value", '');
                RenderEngine::replaceSectionContent($cardHtml, 'recipe_like', '');
            }
            $cardsHtml .= $cardHtml;
        }
        
        RenderEngine::replaceSectionContent($resultsSection, 'card', $cardsHtml);

        return $resultsSection;
    }
    
main($searchType);
?>

