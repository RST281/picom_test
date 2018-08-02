<?php
error_reporting (E_ALL);

class AutoLoad{

    public function __construct() {
        spl_autoload_register(array($this, 'loader'));
    }
    private function loader($className) {

        $filename = strtolower($className) . '.class.php';
        $file = CLASS_DIR . $filename;
        if (file_exists($file) == false) {
            return false;
        }
        if(DEBUG==1){
            echo 'Trying to load ', $className, ' via ', __METHOD__, "() from - $file<br/><br/>";
        }

        include_once($file);
        return true;
    }
}
