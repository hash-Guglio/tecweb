<?php
    class Debug {

        public static function write($data) {
            $output = $data;
            if (is_array($output))
                $output = implode(',', $output);
            echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
        }

    }
?>
