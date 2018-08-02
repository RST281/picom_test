<?php
//DEBUG MODE [ 1 - on | 0 - off ]
define('DEBUG', 0);
//DIRECTORIES
define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'].'/');
define('CLASS_DIR', $_SERVER['DOCUMENT_ROOT'].'/classes/');
define('TPL_DIR', $_SERVER['DOCUMENT_ROOT'].'/tpl/');
define('CACHE_DIR', $_SERVER['DOCUMENT_ROOT'].'/cache/');
//DATA BASE SETTINGS
//@const DB_TYPE [ sqlite | mysqli ]
define('DB_TYPE', 'sqlite');
define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_BASE', 'db.sqlite3');