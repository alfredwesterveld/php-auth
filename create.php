<?php

session_start();

define('DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR);

/* prevent XSS. */
$_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

if (empty($_POST['token']) ||  (isset($token_age) && $token_age > 300)) {
    $token = md5(uniqid(rand(), TRUE));
    $_SESSION['token'] = $token;
    $_SESSION['token_time'] = time();
}

$token_age = time() - $_SESSION['token_time'];

/**
 * Has protection against CSRF.
 * See http://shiflett.org/articles/cross-site-request-forgeries for more information!
 */
if (!empty($_POST['username']) && !empty($_POST['email']) && strlen($_POST['password']) >= 8) {
    include DIR . "Autoloader.php";
    $hash_cost_log2 = 8; // Base-2 logarithm of the iteration count used for password stretching
    $hash_portable = FALSE; // Do we require the hashes to be portable to older systems (less secure)?
    $hasher = new PasswordHash($hash_cost_log2, $hash_portable);
    $settings = new Settings();

    $auth = new Authentication($settings->get('db'), $hasher);
    if ($auth->create($_POST['username'], $_POST['email'], $_POST['password'])) {
        print 'account created<br />';
        print '<a href="index.php">Go back!</a>';
    } else {
        print 'could not creat account<br />';
        print '<a href="index.php">Go back!</a>';
    }
    die();
}
?>
<html>
    <head>
        <title>Create Account</title>
    </head>
    <body>
        <form action=""  method="POST">
            <input type="hidden" name="token" value="<?php echo $token; ?>" />
            
            <label for="identifier">Username:</label><br />
            <input type="text" name="username" id="username"/><br />

            <label for="identifier">Email:</label><br />
            <input type="text" name="email" id="email"/><br />

            <label for="password">Password:</label><br />
            <input type="password" name="password" id="password"/><br />

            <input type="submit" />
        </form>
    </body>
</html>
