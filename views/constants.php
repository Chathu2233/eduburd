<?php

if ($_SERVER['SERVER_NAME'] == 'localhost') {
    define('ROOT', 'http://localhost/eduburd');
} else {
    define('ROOT', 'https://www.yourwebsite.com');
}

// App Name
define('APP_NAME', 'EDUburd');

// Show errors in dev mode
define('DEBUG', true);
