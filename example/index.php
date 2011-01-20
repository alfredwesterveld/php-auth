<?php
    session_start();

    /**
     * CSRF-protection. 
     * See http://shiflett.org/articles/cross-site-request-forgeries for more information!
     */

    $token_age = time() - $_SESSION['token_time'];

    /**
     * Valid CSRF-Token.
     * See http://shiflett.org/articles/cross-site-request-forgeries for more information!
     */
    if (!empty($_POST['token']) && $_POST['token'] == $_SESSION['token'] && $token_age <= 300) {
        /* Enforce string length on password as security measurement. */
        if (!empty($_POST['identifier']) && strlen($_POST['password']) >= 8) {
            require dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Authentication.php';
            $hash_cost_log2 = 8; // Base-2 logarithm of the iteration count used for password stretching
            $hash_portable = FALSE; // Do we require the hashes to be portable to older systems (less secure)?
            $hasher = new PasswordHash($hash_cost_log2, $hash_portable);
            $db = new PDO('sqlite:database/login.sqlite3');
            $authentication = new Authentication($db, $hasher);
            $authentication->createTables();

            if ($authentication->login($_POST['identifier'], $_POST['password'])) {
                echo 'succes';
            } else {
                echo 'failure';
            }
        }
        die(); /* should not continue execution! */
    }

    if (empty($_POST['token']) || $token_age > 300) {
        $token = md5(uniqid(rand(), TRUE));
        $_SESSION['token'] = $token;
        $_SESSION['token_time'] = time();
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
    </body>
</html>