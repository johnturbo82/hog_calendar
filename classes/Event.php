<?php
class Event
{
    var $id;
    var $name;
    var $from;
    var $to;
    var $location;
    var $description;
    var $registrations;
    var $is_closed;
    var $days = array(
        0 => "So",
        1 => "Mo",
        2 => "Di",
        3 => "Mi",
        4 => "Do",
        5 => "Fr",
        6 => "Sa"
    );

    function __construct($id, $name, $from, $to, $location, $description = "", $registrations = 0, $closed = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->from = $from;
        $this->to = $to;
        $this->location = $location;
        $this->description = $description;
        $this->registrations = $registrations;
        $this->is_closed = ($closed) ? true : $this->is_closed();
    }

    function get_date_str()
    {
        if (date("d.m.Y", strtotime($this->from)) == date("d.m.Y", strtotime($this->to))) {
            return $this->days[date("w", strtotime($this->from))] . ", " . date("d.m.Y", strtotime($this->from)) . " " . date("H:i", strtotime($this->from)) . " - " . date("H:i", strtotime($this->to)) . " Uhr";
        } else if (date("H:i", strtotime($this->from)) == "00:00") {
            return $this->days[date("w", strtotime($this->from))] . ", " . date("d.m.Y", strtotime($this->from)) . " - " . $this->days[date("w", strtotime($this->to) - 3600)] . ", " . date("d.m.Y", (strtotime($this->to) - 3600));
        } else {
            return $this->days[date("w", strtotime($this->from))] . ", " . date("d.m.Y H:i", strtotime($this->from)) . "Uhr - " . date("d.m.Y H:i", strtotime($this->to)) . " Uhr";
        }
    }

    function get_from_str()
    {
        if (date("H:i", strtotime($this->from)) == "00:00") {
            return date("d.m.Y", strtotime($this->from)) . " - " . date("d.m.Y", (strtotime($this->to) - 3600));
        } else {
            return "am " . date("d.m.Y H:i", strtotime($this->from)) . " Uhr";
        }
    }

    function is_closed()
    {
        $now = new DateTime('NOW');
        $dt = new DateTime($this->from);
        $hour_str = '-' . HOURS_TO_EVENT_TO_CLOSE_BOOKING  . ' hours';
        if ($now >= $dt->modify($hour_str)) {
            return true;
        }
        return false;
    }
}
