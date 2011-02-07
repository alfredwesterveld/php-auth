<?php

session_start();
session_destroy();

echo 'Logged out <br />';
echo '<a href="index.php">Go back</a>';
