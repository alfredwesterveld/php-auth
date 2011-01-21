<?php

session_start();

$token_age = time() - $_SESSION['token_time'];

if (empty($_POST['token']) || $token_age > 300) {
    $token = md5(uniqid(rand(), TRUE));
    $_SESSION['token'] = $token;
    $_SESSION['token_time'] = time();
}

/**
 * Has protection against CSRF.
 * See http://shiflett.org/articles/cross-site-request-forgeries for more information!
 */
if (!empty($_POST['username']) && !empty($_POST['email']) && strlen($_POST['password']) >= 8) {
    require dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Authentication.php';
    require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'database.php';
    $hash_cost_log2 = 8; // Base-2 logarithm of the iteration count used for password stretching
    $hash_portable = FALSE; // Do we require the hashes to be portable to older systems (less secure)?
    $hasher = new PasswordHash($hash_cost_log2, $hash_portable);
    $auth = new Authentication($db /* GLOBAL from database.php */, $hasher);
    if ($auth->create($_POST['username'], $_POST['email'], $_POST['password'])) {
        print 'account created';
    } else {
        print 'could not creat account';
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
