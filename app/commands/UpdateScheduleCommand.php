<?php
class UpdateScheduleCommand implements Command
{
    public $command = "update.schedule";
    public function run()
    {
        $today = date("Y-m-d");
        $prayers = Prayer::all();
        $data = [];
        foreach ($prayers as $prayer) {
            $weekSchedule = $this->generateFakeData($prayer["prayer_name"], $today);
            foreach ($weekSchedule as $schedule) {
                $timestamp = strtotime($schedule["date"]);
                $date = date("Y-m-d", $timestamp);
                foreach (["imsak", "fajr", "syuruk", "dhuhr", "asr", "maghrib", "isha"] as $name) {
                    $data[] = [
                        "prayer_id" => $prayer["prayer_id"],
                        "date" => $date,
                        "schedule_name" => $name,
                        "time" => $schedule[$name],
                    ];
                }
            }
        }
        PraySchedule::transaction(function () use (&$data) {
            // I thought we don't need old data.
            PraySchedule::empty();
            PraySchedule::insertBatch($data);
        });
    }

    private function generateFakeData($prayer_name, $date)
    {
        return [
            [
                "hijri" => "1445-09-15",
                "date" => $date,
                "imsak" => "05:46:00",
                "fajr" => "05:56:00",
                "syuruk" => "07:04:00",
                "dhuhr" => "13:10:00",
                "asr" => "16:09:00",
                "maghrib" => "19:13:00",
                "isha" => "20:21:00"
            ],
            [
                "hijri" => "1445-09-16",
                "date" => "27-Mar-2024",
                "imsak" => "05:46:00",
                "fajr" => "05:56:00",
                "syuruk" => "07:04:00",
                "dhuhr" => "13:10:00",
                "asr" => "16:09:00",
                "maghrib" => "19:12:00",
                "isha" => "20:21:00"
            ],
            [
                "hijri" => "1445-09-17",
                "date" => "28-Mar-2024",
                "day" => "Thursday",
                "imsak" => "05:46:00",
                "fajr" => "05:56:00",
                "syuruk" => "07:03:00",
                "dhuhr" => "13:10:00",
                "asr" => "16:09:00",
                "maghrib" => "19:12:00",
                "isha" => "20:21:00"
            ],
            [
                "hijri" => "1445-09-18",
                "date" => "29-Mar-2024",
                "day" => "Friday",
                "imsak" => "05:45:00",
                "fajr" => "05:55:00",
                "syuruk" => "07:03:00",
                "dhuhr" => "13:09:00",
                "asr" => "16:10:00",
                "maghrib" => "19:12:00",
                "isha" => "20:21:00"
            ],
            [
                "hijri" => "1445-09-19",
                "date" => "30-Mar-2024",
                "imsak" => "05:45:00",
                "fajr" => "05:55:00",
                "syuruk" => "07:03:00",
                "dhuhr" => "13:09:00",
                "asr" => "16:10:00",
                "maghrib" => "19:12:00",
                "isha" => "20:21:00"
            ],
            [
                "hijri" => "1445-09-20",
                "date" => "31-Mar-2024",
                "imsak" => "05:44:00",
                "fajr" => "05:54:00",
                "syuruk" => "07:02:00",
                "dhuhr" => "13:09:00",
                "asr" => "16:11:00",
                "maghrib" => "19:11:00",
                "isha" => "20:20:00"
            ],
            [
                "hijri" => "1445-09-21",
                "date" => "01-Apr-2024",
                "imsak" => "05:44:00",
                "fajr" => "05:54:00",
                "syuruk" => "07:02:00",
                "dhuhr" => "13:08:00",
                "asr" => "16:11:00",
                "maghrib" => "19:11:00",
                "isha" => "20:20:00"
            ],
            [
                "hijri" => "1445-09-22",
                "date" => "02-Apr-2024",
                "imsak" => "05:43:00",
                "fajr" => "05:53:00",
                "syuruk" => "07:02:00",
                "dhuhr" => "13:08:00",
                "asr" => "16:12:00",
                "maghrib" => "19:11:00",
                "isha" => "20:20:00"
            ]
        ];
    }
}
