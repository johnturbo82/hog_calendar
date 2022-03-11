<?php
class Event {
    var $id;
    var $name;
    var $from;
    var $to;
    var $location;
    
    function __construct($id, $name, $from, $to, $location) {
        $this->id = $id;
        $this->name = $name;
        $this->from = $from;
        $this->to = $to;
        $this->location = $location;
    }

    function get_date_str() {
        if (date("d.m.Y", strtotime($this->from)) == date("d.m.Y", strtotime($this->to))) {
            return date("d.m.Y", strtotime($this->from)) . " " . date("H:i", strtotime($this->from)) . " - " . date("H:i", strtotime($this->to)) . " Uhr";
        } else if (date("H:i", strtotime($this->from)) == "00:00") {
            return date("d.m.Y", strtotime($this->from)) . " - " . date("d.m.Y", (strtotime($this->to) - 3600));
        } else {
            return date("d.m.Y H:i", strtotime($this->from)) . "Uhr - " . date("d.m.Y H:i", strtotime($this->to)) . " Uhr";
        }
    }

    function get_from_str() {
        if (date("H:i", strtotime($this->from)) == "00:00") {
            return date("d.m.Y", strtotime($this->from)) . " - " . date("d.m.Y", (strtotime($this->to) - 3600));
        } else {
            return "am " . date("d.m.Y H:i", strtotime($this->from)) . " Uhr";
        }
    }
}