<?php
define('EMAIL_NOREPLY', 'noreply@' . str_replace('www.','', $_SERVER['SERVER_NAME']));

define('PROTOCOL', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://" );
define('SUB_URL', '/');
define('BASE_URL', PROTOCOL . $_SERVER['SERVER_NAME'] . SUB_URL);