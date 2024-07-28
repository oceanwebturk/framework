<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '');
if($uri !== '/' && file_exists(GET_DIRS['DIRECTORY_ROOT'].$uri)) {
    return false;
}
