<?php

require_once("ini.php");
session_start();

    class RenderEngine {

        private static function loadHtmlFile(string $filePath, string $errorMessage): string {
            if (!file_exists($filePath)) {
                throw new Exception("File not found: " . $filePath);
            }
            
            $content = @file_get_contents($filePath);
            if ($content === false) {
                throw new Exception($errorMessage);
            }
            
            return $content;
        }

        private static function getSectionContent(&$in, $name) : string {
            $startDelimiter = "<!-- {$name}_start -->";
            $endDelimiter = "<!-- {$name}_end -->";
            
            $startPos = strpos($in, $startDelimiter);
            $endPos = strpos($in, $endDelimiter);

    
            if ($startPos === false || $endPos === false) {
                throw new Exception("Delimiters not found: {$name}");
            }

            $startPos += strlen($startDelimiter);
            $contentLength = $endPos - $startPos;

            return substr($in, $startPos, $contentLength);
        }

        private static function replaceSectionContent(&$in, $sectionName) : void {
            $sectionStartTag = "<!-- shared_{$sectionName} -->";
            $startIndex = strpos($in, $sectionStartTag);

            if ($startIndex === false) return;

            $length = strlen($sectionStartTag);

            $filePath = __DIR__ . "/../html/component.{$sectionName}.html";
            $fileStr = self::loadHtmlFile($filePath, "Could not load content for section '{$sectionName}'");
            $newContent = self::getSectionContent($fileStr, $sectionName);

            $in = substr_replace($in, $newContent, $startIndex, $length);
        }

        public static function errorCode($number) : void {
		        http_response_code($number);
		        require ("{$number}.php");
        }

        public static function redirectIfNotAuthenticated($page) {
            if (!isset($_SESSION["id"])) {
                header("location: {$page}.php");
                exit();
            }
        }

        private static function deleteCircularLinks(&$page, $name, $activeClass = 'active') : void {
		        $from = '/<a href="' . $name . '\.php.*?"([^>]*?)>(.*?)<\/a>/s';
		        $to = '<span class="'. $activeClass .'"${1}>${2}</span>';
		        $page = preg_replace($from, $to, $page);
	      }

        public static function buildPage($name) : string {        
            $name = basename($name, ".php");

            try {
                $page = self::loadHtmlFile(__DIR__ . "/../html/{$name}.html", "Could not load page content: {$name}"); 
                self::replaceSectionContent($page, 'head');
                self::replaceSectionContent($page, 'header');
                self::replaceSectionContent($page, 'footer');
            } catch (Exception $e) {
                echo "<p lang='en'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p lang='en'>File: " . htmlspecialchars($e->getFile()) . " | Line: " . $e->getLine() . "</p>";
                return "";
            }

            return $page;
        }

        public static function showPage(&$page) : void {
		        $page = preg_replace('/^\h*\v+/m', '', $page);
		        echo($page);
	      }
    }

?>
