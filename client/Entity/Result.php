<?php
class Result
{
    // Properties
    private $name;
    private $score;
    private $time;

    function set_name($name)
    {
        $this->name = $name;
    }
    function get_name()
    {
        return $this->name;
    }
    function set_score($score)
    {
        $this->score = $score;
    }
    function get_score()
    {
        return $this->score;
    }

    function set_time($time)
    {
        $this->time = $time;
    }
    function get_time()
    {
        return $this->time;
    }
}
