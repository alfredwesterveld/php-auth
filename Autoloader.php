<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define('PATH_PROJECT', realpath(dirname(__FILE__)));

function __autoload($class_name) {
    include PATH_PROJECT . DIRECTORY_SEPARATOR . $class_name . ".php";
}