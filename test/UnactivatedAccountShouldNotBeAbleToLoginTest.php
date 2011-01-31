<?php

define('PATH_PROJECT', realpath(dirname(__FILE__)));
define('PREVIOUS_FOLDER', PATH_PROJECT . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
require_once PREVIOUS_FOLDER . 'AuthenticationFactory.php';

/**
 *
 * @author alfred
 *
 */
class UnactivatedAccountShouldNotBeAbleToLoginTest extends PHPUnit_Framework_TestCase {
    // @codeCoverageIgnoreStart
    protected static $hasher;
    protected $authentication;

    public static function setUpBeforeClass()  {
        $hash_cost_log2 = 8; // Base-2 logarithm of the iteration count used for password stretching
        $hash_portable = FALSE; // Do we require the hashes to be portable to older systems (less secure)?
        self::$hasher = new PasswordHash($hash_cost_log2, $hash_portable);
    }

    protected function setUp() {
        $db = new PDO('sqlite::memory:');
        $this->authentication = AuthenticationFactory::create($db, self::$hasher);
        $this->authentication->createTables();
    }

    public function testNothing() {
        $this->assertTrue(true);
    }
}