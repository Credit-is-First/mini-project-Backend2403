<?php
class PrayerController extends Controller
{
    public function today($prayer_name)
    {
        $today = date("Y-m-d");
        $prayer = Prayer::where("prayer_name", $prayer_name)->first();

        $schedules = PraySchedule::leftJoin("boxes", "boxes.prayer_id", "=", "schedules.prayer_id")
            ->leftJoin("songs", "songs.box_id", "=", "boxes.box_id")
            ->where(["prayer_id" => $prayer->prayer_id, "date" => $today])
            ->select("schedules.*, song_id, song_name")
            ->get();

        $result = [
            "prayer_name" => $prayer_name,
            "date" => $today,
        ];
        foreach ($schedules as $schedule) {
            $name = $schedule["schedule_name"];
            $time = $schedule["time"];
            $result["song_name"] = $schedule["song_name"];
            $result[$name] = $time;
        }

        echo json_encode($result);
    }

    public function week($prayer_name)
    {
        $prayer = Prayer::where("prayer_name", $prayer_name)->first();

        $today = date("Y-m-d");
        $nextWeekDate = (new DateTime())->modify('+1 week')->modify('-1 day')->format('Y-m-d');

        $schedules = PraySchedule::leftJoin("boxes", "boxes.prayer_id", "=", "schedules.prayer_id")
            ->leftJoin("songs", "songs.box_id", "=", "boxes.box_id")
            ->where("prayer_id", $prayer->prayer_id)
            ->whereBetween("date", $today, $nextWeekDate)
            ->select("schedules.*, song_id, song_name")
            ->get();

        $result =  [];
        foreach ($schedules as $schedule) {
            $date = $schedule["date"];
            if (!isset($result[$date])) {
                $result[$date] = [
                    "prayer_name" => $prayer_name,
                    "date" => $date,
                ];
            }
            $name = $schedule["schedule_name"];
            $time = $schedule["time"];
            $result[$date]["song_name"] = $schedule["song_name"];
            $result[$date][$name] = $time;
        }
        echo json_encode(array_values($result));
    }

    public function get($id)
    {
        throw new Error("This function has not been implemented yet.");
    }

    public function create()
    {
        throw new Error("This function has not been implemented yet.");
    }

    public function delete()
    {
        throw new Error("This function has not been implemented yet.");
    }

    public function update($id)
    {
        throw new Error("This function has not been implemented yet.");
    }
}
