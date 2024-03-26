<?php
class Prayer extends Model
{
    protected $_table = "prayers";
    protected $_pk_id = "prayer_id";
    
    public function getTodaySchedule($prayer_id) {
        $sql = "SELECT * from prayers RIGHT JOIN schedules ON prayers.prayer_id = schedules.prayer_id WHERE schedules.prayer_id = $prayer_id";
        return $this->_db->query($sql)->results();
    }
}
