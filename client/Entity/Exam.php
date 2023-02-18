<?php
    class Exam
    {
        // Properties
        public $id;
        public $name;
        public $topic;
        public $time;
        public $num_question;
        public $time_start;
        public $time_close;

        // Methods
        function set_id($id)
        {
            $this->id = $id;
        }
        function get_id()
        {
            return $this->id;
        }

        function set_name($name)
        {
            $this->name = $name;
        }
        function get_name()
        {
            return $this->name;
        }
        function set_topic($topic)
        {
            $this->topic = $topic;
        }
        function get_topic()
        {
            return $this->topic;
        }

        function set_time($time)
        {
            $this->time = $time;
        }
        function get_time()
        {
            return $this->time;
        }

        function set_num_question($num_question)
        {
            $this->num_question = $num_question;
        }
        function get_num_question()
        {
            return $this->num_question;
        }
        function set_time_start($time_start)
        {
            $this->time_start = $time_start;
        }
        function get_time_start()
        {
            return $this->time_start;
        }
        function set_time_close($time_close)
        {
            $this->time_close = $time_close;
        }
        function get_time_close()
        {
            return $this->time_close;
        }
    }
?>
