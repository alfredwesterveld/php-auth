<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 */

define('PATH_PROJECT', realpath(dirname(__FILE__)));
define('DATABASE_FOLDER', PATH_PROJECT . DIRECTORY_SEPARATOR . "database");

//die(DATABASE_FOLDER);
if (!is_dir(DATABASE_FOLDER)) {
    if (!mkdir(DATABASE_FOLDER, 700)) {
        die('Failed to create folders...');
    }
}

$db = new PDO('sqlite:database/login.sqlite3');

function createTables($db) {
    $db->exec("CREATE TABLE IF NOT EXISTS users (Id INTEGER PRIMARY KEY
    , username TEXT NOT NULL UNIQUE, email TEXT NOT NULL UNIQUE, hash TEXT NOT NULL, active BOOLEAN)");
}

createTables($db);
print 'created database';