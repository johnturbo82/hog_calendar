<?php
class Poll
{
    var $id;
    var $name;
    var $description;
    var $options;
    var $multichoice;
    var $poll_results;

    function __construct($id, $name, $description, $options, $multichoice, $poll_results_from_db = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->options = $this->process_options($options);
        $this->multichoice = ($multichoice == 0) ? false : true;
        $this->poll_results = $this->evaluate_poll_results($poll_results_from_db);
    }

    function process_options($options)
    {
        return explode("\n", $options);
    }

    function evaluate_poll_results($poll_results_from_db)
    {
        if ($poll_results_from_db == null) {
            return null;
        }
        $result_arr = array();
        for ($i = 0; $i < count($this->options); $i++) {
            $result_arr[$i] = 0;
        }
        $votes = 0;
        foreach ($poll_results_from_db as $result) {
            $result_arr[$result['value']] = $result_arr[$result['value']] + 1;
            $votes++;
        }
        foreach ($result_arr as $k => $result) {
            $result_arr[$k] = $result / $votes * 100;
        }
        return $result_arr;
    }
}
