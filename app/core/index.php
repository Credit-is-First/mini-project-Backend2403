<?php
session_start();

require_once('app/core/helpers.php');

spl_autoload_register("autoload");
set_error_handler("customErrorHandler");

Router::process();
