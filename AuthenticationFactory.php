<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PasswordHash.php';
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Authentication.php';

class AuthenticationFactory {
    public static function create($db, $hasher) {
        return new Authentication($db, $hasher);
    }
}
?>
