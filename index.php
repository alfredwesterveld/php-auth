<?php

/**
 * Has protection against CSRF.
 * See http://shiflett.org/articles/cross-site-request-forgeries for more information!
 */
session_start();

define('DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR);

/* prevent XSS. */
$_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
 
if (isset($_SESSION['identifier'])) {
    echo $_SESSION['identifier'] . '<br />';
    echo '<a href="logout.php">logout</a>';
    exit(0);
}
            
if (empty($_POST['token']) ||  (isset($token_age) && $token_age > 300)) {
    $token = md5(uniqid(rand(), TRUE));
    $_SESSION['token'] = $token;
    $_SESSION['token_time'] = time();
}

$token_age = time() - $_SESSION['token_time'];

/* Valid CSRF-Token. */
if (!empty($_POST['token']) && $_POST['token'] == $_SESSION['token'] && $token_age <= 300) {
    /* Enforce string length on password as security measurement. */
    if (!empty($_POST['identifier']) && strlen($_POST['password']) >= 8) {
        include DIR . "Autoloader.php";

        $hash_cost_log2 = 8; // Base-2 logarithm of the iteration count used for password stretching
        $hash_portable = FALSE; // Do we require the hashes to be portable to older systems (less secure)?
        $hasher = new PasswordHash($hash_cost_log2, $hash_portable);
        $settings = new Settings();
        $authentication = new Authentication($settings->get('db'), $hasher);
        if ($authentication->login($_POST['identifier'], $_POST['password'])) {
            session_regenerate_id(true);
            $_SESSION['identifier'] = $_POST['identifier'];
            echo '<p>Welcome' . $_POST['identifier'];
        } else {
            echo 'failure';
        }
        die(); /* should not continue execution after this point! */
    }
}

?>
<html>
    <head>
        <title>Login</title>
    </head>
    <body>
        <form action=""  method="POST">
            <input type="hidden" name="token" value="<?php echo $token; ?>" />
            
            <label for="identifier">Username/Email:</label><br />
            <input type="text" name="identifier" id="idenitier"/><br />

            <label for="password">Password:</label><br />
            <input type="password" name="password" id="password"/><br />

            <input type="submit" />
        </form>

        <p>
            <a href="create.php">Have no account? Click here to create one!</a>
        </p>
    </body>
</html>
