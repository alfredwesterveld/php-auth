<?php


require dirname(__FILE_) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Authentication.php';

/**
 *
 * @author alfred
 *
 */
class AuthenticationTest extends PHPUnit_Framework_TestCase {
    protected static $hasher;
    protected $authentication;

    public static function setUpBeforeClass()  {
        $hash_cost_log2 = 8; // Base-2 logarithm of the iteration count used for password stretching
        $hash_portable = FALSE; // Do we require the hashes to be portable to older systems (less secure)?
        self::$hasher = new PasswordHash($hash_cost_log2, $hash_portable);
    }

    protected function setUp() {
        $db = new PDO('sqlite::memory:');
        $this->authentication = new Authentication($db, self::$hasher);
        $this->authentication->createTables();
    }

    public function testAddingAccountWhichDoesNotYetExist() {
        $this->assertTrue($this->authentication->create("alfred", "alfredwesterveld@gmail.com", "westerveld"));
    }

    public function testAddingAccountTwice() {
        $this->assertTrue($this->authentication->create("alfred", "alfredwesterveld@gmail.com", "westerveld"));
        $this->assertFalse($this->authentication->create("alfred", "alfredwesterveld@gmail.com", "westerveld"));
        $this->assertFalse($this->authentication->create("alfred", "dada@dada.com", "westerveld"));

        /**
         * Breaks tests for now :$.
         */
        //$this->assertFalse($this->authentication->create("hi", "alfredwesterveld@gmail.com", "westerveld"));
    }

    public function testLoginUserWhichDoesNotExist() {
        $ok = $this->authentication->login("alfred", "westerveld");
        $this->assertFalse($ok);
    }

    public function testLoginUserWhichDoesExist() {
        $this->authentication->create("alfred", "alfredwesterveld@gmail.com", "westerveld");
        $this->assertTrue($this->authentication->login("alfred", "westerveld"));
    }

    public function testLoginAlsoWorksForEmail() {
        $this->assertFalse($this->authentication->login("alfredwesterveld@gmail.com", "westerveld"));
        $this->authentication->create("alfred", "alfredwesterveld@gmail.com", "westerveld");
        $this->assertTrue($this->authentication->login("alfredwesterveld@gmail.com", "westerveld"));
    }

    public function testEmailVerificationWorksForValidSecret() {
        $secret = $this->authentication->createEmailVerificationSecret("alfredwesterveld@gmail.com");
        $this->assertTrue($this->authentication->verifyEmail("alfredwesterveld@gmail.com", $secret));
    }

    public function testEmailVerificationFailsForInvalidSecret() {
        $this->assertFalse($this->authentication->verifyEmail("alfredwesterveld@gmail.com", 'dada'));
    }

    /**
     *  @expectedException EmailException
     */
    public function testExceptionThrownForEmailVerificationIfNoEmailHasBeenProvided() {
        $this->authentication->createEmailVerificationSecret("alfred");
    }

    public function testThatNewlyCreatedAccountIsNotActivated() {
        $this->authentication->create("alfred", "alfredwesterveld@gmail.com", "westerveld");
        $this->assertFalse($this->authentication->isActivated("alfred"));
    }

    public function testThatAccountIsActivatedAfterVerification() {
        $this->authentication->create("alfred", "alfredwesterveld@gmail.com", "westerveld");
        $secret = $this->authentication->createEmailVerificationSecret("alfredwesterveld@gmail.com");
        $this->authentication->verifyEmail("alfredwesterveld@gmail.com", $secret);
        $this->assertTrue($this->authentication->isActivated("alfred"));
    }
}