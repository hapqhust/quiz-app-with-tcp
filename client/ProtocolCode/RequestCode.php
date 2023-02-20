<?php

namespace ProtocolCode;

abstract class RequestCode{
    const LOGIN = "0";
    const REGISTER = "1";
    const LOGOUT = "2";
    const SHOW_LIST_PRACTICE = "3";
    const SHOW_LIST_EXAM = "4";
    const GET_LIST_TOPIC = "5";
    const ADD_NEW_PRACTICE = "6";
    const ADD_NEW_EXAM  = "7";
    const JOIN_PRACTICE = "8";
    const JOIN_EXAM = "9";
    const SAVE_RESULT = "10";
    const ANSWER = "11";
    const CHECK_JOINED_EXAM = "12";
    const DASHBOARD = "13";
    // const STOP = "13";
}

?>