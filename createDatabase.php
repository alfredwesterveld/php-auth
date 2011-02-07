<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$db = new PDO('sqlite:database/login.sqlite3');

function createTables($db) {
    $db->exec("CREATE TABLE IF NOT EXISTS users (Id INTEGER PRIMARY KEY
    , username TEXT NOT NULL UNIQUE, email TEXT NOT NULL UNIQUE, hash TEXT NOT NULL, active BOOLEAN)");
}

createTables($db);
print 'created database';
