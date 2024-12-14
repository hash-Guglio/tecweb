<?php

    require_once("php/renderEngine.php");
    require_once("php/database.php");

    $id = (isset($_GET["id"]) ? ($_GET["id"]) : "");

    if (empty($id)) {
	      RenderEngine::errCode(404);
	      exit();
    };

    try {
        $connection = new Database();
        $recipe = $connection->getRecipeById($id);
        $dish_type = $connection->getDtByRecipeId($id);
        $restriction = $connection->getRstByRecipeId($id);   
        $ingredients = $connection->getIngredientsByRecipeId($id);
        
        $totalNutrients = [
            'cal' => [0, ""],
            'prt' => [0, ""],
            'fat' => [0, ""],
            'carbo' => [0, ""]
        ];

        foreach ($ingredients as $ingredient) {
            $nutrients = $connection->getNutByIngredientId($ingredient['id']);

            foreach ($nutrients as $nutrient) {
                $totalNutrients[$nutrient['id']][0] += $nutrient['amount'];
                $totalNutrients[$nutrient['id']][1] =  $nutrient['unit'];
            }
        }

        unset($connection);
    } catch(Exception $e) {
        unset($connection);
	      RenderEngine::errorCode(500);
	      exit();
    };
    
    $page = RenderEngine::buildPage("ricette");

    RenderEngine::replaceAnchor($page, "recipe_title", $recipe[0]["rcp_title"] . " | Ricette");
    RenderEngine::replaceAnchor($page, "recipe", $recipe[0]["rcp_title"]);
    
    RenderEngine::replaceAnchor($page, 'webp', "pics/" . ($result['rcp_image'] ?? 'placeholder') . ".webp");
    RenderEngine::replaceAnchor($page, 'image', "pics/" . ($result['rcp_image'] ?? 'placeholder') . ".jpg");

    RenderEngine::replaceAnchor($page, "time", $recipe[0]["rcp_ready_minutes"]);
    RenderEngine::replaceAnchor($page, "servings", $recipe[0]["rcp_servings"]);
    RenderEngine::replaceAnchor($page, "price_per_serving", $recipe[0]["rcp_price_servings"]); 
    
    $restrictionMap = [
        "Vegano" => "vegan",
        "Senza lattosio" => "dairy_free",
        "Senza glutine"  => "gluten_free"
    ];

    $rstSection = RenderEngine::getSectionContent($page, "restrictions"); 
    $rstSectionData = RenderEngine::getSectionContent($page, "restrictions_data");
    $rsts = "";
    foreach ($restriction as $r) {
        $tmp = RenderEngine::getSectionContent($rstSection, $restrictionMap[$r["rst_type"]]);  
        $rsts .= $tmp;
    }
    RenderEngine::replaceSectionContent($rstSection, "restrictions_data", $rsts);
    RenderEngine::replaceSectionContent($page, "restrictions", $rstSection);

    foreach ($totalNutrients as $nutrientName => $nutrientAmount) {
        RenderEngine::replaceAnchor($page, $nutrientName, $nutrientAmount[0] . " " . $nutrientAmount[1]);
    }

    $res = "";
    foreach ($ingredients as $ingredient) {
        $ingredientSection = RenderEngine::getSectionContent($page, "ingredient");
        RenderEngine::replaceAnchor($ingredientSection, "igr_name", $ingredient["name"]); 
        RenderEngine::replaceAnchor($ingredientSection, "igr_amount", $ingredient["amount"]);
        RenderEngine::replaceAnchor($ingredientSection, "igr_unit", $ingredient["unit"]);
        RenderEngine::replaceAnchor($ingredientSection, "igr_link", "ingredienti.php?id={$ingredient["id"]}");
        $res .= $ingredientSection;
    }

    RenderEngine::replaceSectionContent($page, "ingredient", $res);
        
    $res = "";
    foreach ($dish_type as $dt) {
        $dtSection = RenderEngine::getSectionContent($page, "dt");
        RenderEngine::replaceAnchor($dtSection, "dt_name", $dt["name"]); 
        RenderEngine::replaceAnchor($dtSection, "dt_link", "cerca_ricette.php?filter=dish_type&dish_type={$dt["id"]}");
        $res .= $dtSection;
    }

    RenderEngine::replaceSectionContent($page, "dt", $res);

    RenderEngine::showPage($page);
?>
