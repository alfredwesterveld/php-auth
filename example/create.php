<?php
    if (!empty($_POST['username']) && !empty($_POST['email']) && strlen($_POST['password']) >= 8) {
        require dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Authentication.php';
        $db = new PDO('sqlite:database/login.sqlite3'); // Should update this if neccessary.
        $hash_cost_log2 = 8; // Base-2 logarithm of the iteration count used for password stretching
        $hash_portable = FALSE; // Do we require the hashes to be portable to older systems (less secure)?
        $hasher = new PasswordHash($hash_cost_log2, $hash_portable);
        $auth = new Authentication($db, $hasher);
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
