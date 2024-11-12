<?php

require_once("ini.php");
session_start();

    class RenderEngine {
        private static $authPages = ['login', 'user'];

        private static function loadHtmlFile(string $filePath, string $errorMessage): string {
            if (!file_exists($filePath)) {
                throw new Exception("File not found: {$filePath}");
            }
            
            $content = @file_get_contents($filePath);
            if ($content === false) {
                throw new Exception($errorMessage);
            }
            
            return $content;
        }

        private static function isAuthPage($name): bool {
            return in_array($name, self::$authPages);
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

        public static function replaceAnchor(&$in, $anchor, $content, $comment = false) : void {
            $from = $comment ? "<!-- $anchor -->" : "@@{$anchor}@@";
            $pos = strpos($in, $from);


            if ($pos !== false) {
                $in = substr_replace($in, $content, $pos, strlen($from));
            }
        }
       
        public static function replaceSectionContent(&$in, $sectionName, $content = '@@') : void {
            
            $sharedTag = "<!-- shared_{$sectionName} -->";
            $startTag = "<!-- {$sectionName}_start -->";
            $endTag = "<!-- {$sectionName}_end -->";

            $startIndex = strpos($in, $sharedTag);

            if ($startIndex !== false && $content == '@@') {
                $filePath = __DIR__ . "/../html/component.{$sectionName}.html";
                $fileStr = self::loadHtmlFile($filePath, "Could not load content for section '{$sectionName}'");
                $content = self::getSectionContent($fileStr, $sectionName);
                $length = strlen($sharedTag);
                $in = substr_replace($in, $content, $startIndex, $length);
                return;
            }

            $startIndex = strpos($in, $startTag);
            $endIndex = strpos($in, $endTag);

            if ($startIndex === false && $endIndex === false) 
                throw new Exception("Section delimiters not found for '{$sectionName}'");
            
            $startIndex += strlen($startTag);
            $length = $endIndex - $startIndex;
        
            $in = substr_replace($in, $content, $startIndex, $length);
        }

        public static function errorCode($number) : void {
		        http_response_code($number);
		        require ("{$number}.php");
        }

        public static function redirectBasedOnAuth($page, $authenticated = true) {
            if (($authenticated == isset($_SESSION["id"])) ) {
                header("Location: {$page}.php");
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
            
                if (self::isAuthPage($name)) {
                    self::replaceSectionContent($page, 'header_user', '');
                    self::replaceSectionContent($page, 'account', '');
                } 
                else {
                    if (isset($_SESSION["id"])) {
				                    self::replaceAnchor($page, "account_button", "Utente");
                            
                            if ($_SESSION["is_admin"] == 0)
					                      self::replaceSectionContent($page, "header_admin", "");
			              } else {
				                self::replaceSectionContent($page, "header_user", "");
				                self::replaceAnchor($page, "account_button", "Accedi");
			              }                
                }

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
