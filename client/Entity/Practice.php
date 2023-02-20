<?php
    class Practice
    {
        // Properties
        private $id;
        private $name;
        private $topic;
        private $time;
        private $num_question;

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
    }
