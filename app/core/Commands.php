<?php
class Commands
{
    private static $_instance;

    private $_os;
    private $commands = array();

    private function __construct()
    {
        $this->_os = getOS();
    }

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function run()
    {
        $command = $this->getCommand();
        if (isset($this->commands[$command])) {
            $cron = $this->commands[$command];
            $cron->run();
        } else {
            throw new Error("The command name `$command` doesn't exist.");
        }
    }

    public function addCommand($className)
    {
        $cron = new $className();
        $this->commands[$cron->command] = $cron;
    }

    private function getCommand()
    {
        global $argc, $argv;
        for ($i = 1; $i < $argc; $i++) {
            $arg = $argv[$i];
            $result = explode("=", $arg, 2);
            if (isset($result[0]) && $result[0] == "--name") {
                return $result[1];
            }
        }
        throw new Error("Command name is required. ex: --name=COMMNAD_NAME");
    }

    private function _runWindows()
    {
    }

    private function _runLinux()
    {
        // Define the command to run the script every day
        $commandToAdd = '* * * * * /path/to/php /path/to/your_script.php';

        // Use the `crontab` command to read the current crontab file
        exec('crontab -l', $output, $returnVar);

        // Check if the command already exists in the crontab file
        $commandExists = false;
        foreach ($output as $line) {
            if (trim($line) == $commandToAdd) {
                $commandExists = true;
                break;
            }
        }

        // If the command doesn't exist, add it to the crontab file
        if (!$commandExists) {
            $output[] = $commandToAdd;
            $newCrontab = implode(PHP_EOL, $output);
            file_put_contents('/tmp/crontab.txt', $newCrontab); // Save updated crontab to a temporary file

            // Use the `crontab` command to update the crontab file
            exec('crontab /tmp/crontab.txt', $output, $returnVar);

            if ($returnVar === 0) {
                echo "Command added to the crontab file.";
            } else {
                echo "Error adding command to the crontab file.";
            }
        } else {
            echo "The command already exists in the crontab file.";
        }
    }

    private function _runMac()
    {
    }
}
