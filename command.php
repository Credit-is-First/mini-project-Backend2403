<?php
session_start();
require_once('config.php');
require_once('app/core/helpers.php');

spl_autoload_register("autoload");

Commands::getInstance()->addCommand(UpdateScheduleCommand::class);
// Add new commands here

Commands::getInstance()->run();
