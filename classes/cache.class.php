<?php
class Cache {
    private $registry;
    function __construct($registry){
        $this->registry = $registry;
    }

    public static function set($key, $value) {
        $path = CACHE_DIR . $key;
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        $file = fopen($path, 'a+');
        flock($file, LOCK_EX);
        ftruncate($file, 0);
        fwrite($file, sprintf("<?php\nreturn %s;", var_export($value, true)));
        fflush($file);
        flock($file, LOCK_UN);
        fclose($file);
    }

    public static function get($key)
    {
        $path = CACHE_DIR . $key;

        if (is_file($path)) {
            return include $path;
        }

        return null;
    }

    public static function has($key)
    {
        $path = CACHE_DIR . $key;

        return is_file($path);
    }

    public static function reset($key)
    {
        $path = CACHE_DIR . $key;

        if (is_file($path)) {
            unlink($path);
        }
    }
}