<?php 
    require_once('ini.php');

    class RenderEngine {
        private static function getFile(string $filePath) : string {
            if(!file_exists($filePath)) {
                throw new Exception("[ERROR] {$filePath}  not found in /html directory.");
            }

            $content = @file_get_contents($filePath);

            if ($content == false) {
                throw new Exception("[ERROR] {$filePath} is empty.");
            }
            return $content; 
        }

        public static function replaceSectionContent(&$in, $sectionName) {
            $sectionTag = "<!-- shared_{$sectionName} -->";
            $startPos = strpos($in, $sectionTag);
            
            if ($startPos == false) {
                return new Exception("[ERROR] Start delimeter (section: {$sectionName}) not found.");
            } 

            $length = strlen($sectionTag);

            $sectionFile = self::getFile(__DIR__ . "/../html/component.{$sectionName}" . '.html');

            $contentSection = self::getSectionContent($sectionFile, $sectionName);

            $in = substr_replace($in, $contentSection, $startPos, $length); 
        }

        public static function getSectionContent(&$in, $sectionName) : string {
            $sectionStartTag = "<!-- {$sectionName}_start -->"; 
            $sectionEndTag = "<!-- {$sectionName}_end -->";

            $startPos = strpos($in, $sectionStartTag); 
            $endPos = strpos($in, $sectionEndTag);


            if ($startPos === false || $endPos === false) {
                return new Exception("[ERROR] Delimeters (section: {$sectionName}) not found in component file.");
            }

            $startPos += strlen($sectionStartTag);
            $contentLength = $endPos - $startPos;

            return substr($in, $startPos, $contentLength);
        }

        public static function buildPage(string $name) : string {            
            try {
                $page = self::getFile(__DIR__ . "/../html/{$name}.html");
                self::replaceSectionContent($page, 'head');
                self::replaceSectionContent($page, 'header');
                self::replaceSectionContent($page, 'footer');
                return $page;
            }
            catch(Exception $ex) {
                return $ex;
            }
        }    
        
        public static function showPage(string &$page) : void {
            $page = preg_replace('/^\h*\v+/m', '', $page);
            echo($page);
        } 

    }
?>
