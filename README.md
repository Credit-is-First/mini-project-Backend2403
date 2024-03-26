# Test for mini-project-Backend2403

## 1. Code structure
I have meticulously crafted the code structure from its inception, and I am confident that it will be impeccably completed in the future.

Only I implemented some functions that need in current test.

### Controllers (app/controllers)
```
class TestController extends Controller {
    public function func1() {
        // you can call this function http://localhost/test/func1
        $test = TestModel::leftJoin("test2", "test2.id", "=", "tests.id")
            ->where(["name" => "hello"])
            ->select("tests.*")
            ->first();
        echo json_encode($test);
    }

    // Start of resource functions
    /**
     * Method: GET
     * url: http://localhost/test
     */
    public function find()
    {
        $tests = TestModel::all();
        echo json_encode($tests);
    }

    /**
     * Method: GET
     * url: http://localhost/test/:id
     */
    public function get($id)
    {
        $test = TestModel::find($id);
        echo json_encode($test);
    }
    
    /**
     * Method: POST
     * url: http://localhost/test
     */
    public function create()
    {
    }

    /**
     * Method: DELETE
     * url: http://localhost/test
     */
    public function delete()
    {
    }

    /**
     * Method: PUT
     * url: http://localhost/test/:id
     */
    public function update($id)
    {
    }
    // End of resource functions
}
```

### Models (app/models)
```
class TestModel {
    protected $_table = "tests";      // required
    protected $_pk_id = "test_id";    // default: 'id'
}
```

### Commands (app/commands)
```
<?php
class TestCommand implements Command
{
    public $command = "test-command";
    public function run()
    {
        $test = TestModel::where("name", "hello")->first();
        $test->check_date = date("Y-m-d");
        $test->save();
    }
}
```

- You should add this command in command.php
```
Commands::getInstance()->addCommand(TestCommand::class);
```

- You can run this command in command line
```
php.exe command.php --name=test-command
```

- So we can run this command every day by add this command in crontab
```
* * * * * /path/to/php /path/to/this_project/command.php --name=test-command
```

### Config (configs.php)
```
$config = array(
    "database" => array(
        "default" => "mysql",

        "mysql" => array(
            "host"      => "127.0.0.1",
            "username"  => "root",
            "password"  => "",
            "db_name"   => "mini_backend"
        )
    ),
);
```
> I will integrate config with .env file

## Database Design
<p>
  <a href="./DB diagram.jpg" target="_blank">
    <img alt="DB Diagram" src="./DB diagram.jpg">
  </a>
</p>

## How to run

### Migrate database
- Excute `app/databases/migrations/migration.sql` file

### Make fake seed data
- Excute `app/databases/seeds/seed.sql` file

### Update 7 days of voiceovers in advance
> We will execute this command only once, as the subsequent updates will be handled by the cron job.

in your project command line
```
php command.php --name=update.schedule
```
### Run cron job to update 7 days of voiceovers in advance
```
* * * * * /path/to/php /path/to/this_project/command.php --name=update.schedule
```

This cron job will run every day

>In the future, I intend to implement a function within the code that will facilitate the execution of cron jobs.

### Today's schedule

- Endpoint
```
http://localhost/prayer/today/PRAYER_NAME
```
> For example: http://localhost/prayer/today/JHR01

- Response
```
{
    "prayer_name": "JHR01",
    "song_name": "song1",
    "date": "2024-03-26",
    "imsak": "05:46:00",
    "fajr": "05:56:00",
    "syuruk": "07:04:00",
    "dhuhr": "13:10:00",
    "asr": "16:09:00",
    "maghrib": "19:13:00",
    "isha": "20:21:00"
}
```

### Schedules for a week

- Endpoint
```
http://localhost/prayer/week/PRAYER_NAME
```
> For example: http://localhost/prayer/week/JHR01

- Response
```
[
    {
        "prayer_name": "JHR01",
        "date": "2024-03-26",
        "song_name": "song1",
        "imsak": "05:46:00",
        "fajr": "05:56:00",
        "syuruk": "07:04:00",
        "dhuhr": "13:10:00",
        "asr": "16:09:00",
        "maghrib": "19:13:00",
        "isha": "20:21:00"
    },
    {
        "prayer_name": "JHR01",
        "date": "2024-03-27",
        "song_name": "song1",
        "imsak": "05:46:00",
        "fajr": "05:56:00",
        "syuruk": "07:04:00",
        "dhuhr": "13:10:00",
        "asr": "16:09:00",
        "maghrib": "19:12:00",
        "isha": "20:21:00"
    },
    {
        "prayer_name": "JHR01",
        "date": "2024-03-28",
        "song_name": "song1",
        "imsak": "05:46:00",
        "fajr": "05:56:00",
        "syuruk": "07:03:00",
        "dhuhr": "13:10:00",
        "asr": "16:09:00",
        "maghrib": "19:12:00",
        "isha": "20:21:00"
    },
    {
        "prayer_name": "JHR01",
        "date": "2024-03-29",
        "song_name": "song1",
        "imsak": "05:45:00",
        "fajr": "05:55:00",
        "syuruk": "07:03:00",
        "dhuhr": "13:09:00",
        "asr": "16:10:00",
        "maghrib": "19:12:00",
        "isha": "20:21:00"
    },
    {
        "prayer_name": "JHR01",
        "date": "2024-03-30",
        "song_name": "song1",
        "imsak": "05:45:00",
        "fajr": "05:55:00",
        "syuruk": "07:03:00",
        "dhuhr": "13:09:00",
        "asr": "16:10:00",
        "maghrib": "19:12:00",
        "isha": "20:21:00"
    },
    {
        "prayer_name": "JHR01",
        "date": "2024-03-31",
        "song_name": "song1",
        "imsak": "05:44:00",
        "fajr": "05:54:00",
        "syuruk": "07:02:00",
        "dhuhr": "13:09:00",
        "asr": "16:11:00",
        "maghrib": "19:11:00",
        "isha": "20:20:00"
    },
    {
        "prayer_name": "JHR01",
        "date": "2024-04-01",
        "song_name": "song1",
        "imsak": "05:44:00",
        "fajr": "05:54:00",
        "syuruk": "07:02:00",
        "dhuhr": "13:08:00",
        "asr": "16:11:00",
        "maghrib": "19:11:00",
        "isha": "20:20:00"
    }
]
```
