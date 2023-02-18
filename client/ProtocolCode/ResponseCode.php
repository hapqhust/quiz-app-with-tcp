<?php

namespace ProtocolCode;

class ResponseCode {
    const QUERY_FAIL = "0";
    const LOGIN_SUCCESS = "1";
    const USERNAME_NOTFOUND = "2";
    const USERNAME_BLOCKED = "3";
    const USERNAME_IS_SIGNIN = "4";
    const PASSWORD_INCORRECT = "5";

    const REGISTER_SUCCESS = "6";
    const REGISTER_USERNAME_EXISTED = "7";

    const LOGOUT_SUCCESS = "8";

    const NUM_PRACTICE = "9";
    const SHOW_PRACTICE_DETAIL = "10";

    const NUM_EXAM = "11";
    const SHOW_EXAM_DETAIL = "12";

    const SHOW_LIST_TOPIC = "13";

    const ADD_NEW_PRACTICE_SUCCESSFUL = "14";
    const ADD_NEW_EXAM_SUCCESSFUL = "15";

    const LIST_QUESTION_ID = "16";
    const QUESTION = "17";

    const ANSWER_CORRECT = "18";
    const ANSWER_INCORRECT = "19";
}

?>
