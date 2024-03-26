<?php
class PrayerController extends Controller
{
    public function index()
    {
    }

    public function today($prayer_name)
    {
        $today = date("Y-m-d");
        $prayer = Prayer::where("prayer_name", $prayer_name)->first();

        $boxes = PraySchedule::leftJoin("boxes", "boxes.prayer_id", "=", "schedules.prayer_id")
            ->leftJoin("songs", "songs.box_id", "=", "boxes.box_id")
            ->where(["prayer_id" => $prayer->prayer_id, "date" => $today])
            ->select("schedules.*, song_id, song_name")
            ->get();

        $result = [
            "prayer_name" => $prayer_name,
            "date" => $today,
        ];
        foreach ($boxes as $box) {
            $name = $box["schedule_name"];
            $time = $box["time"];
            $result[$name] = $time;
        }

        echo json_encode($result);
    }

    public function get()
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

    public function update()
    {
        throw new Error("This function has not been implemented yet.");
    }
}
