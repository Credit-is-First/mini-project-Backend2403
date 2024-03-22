<?php
session_start();

require_once('app/core/helpers.php');

spl_autoload_register("autoload");

Router::process();
