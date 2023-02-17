<?php

enum ResponseCode : string {
    case QUERY_FAIL = "0";
    case LOGIN_SUCCESS = "1";
    case USERNAME_NOTFOUND = "2";
    case USERNAME_BLOCKED = "3";
    case USERNAME_IS_SIGNIN = "4";
    case PASSWORD_INCORRECT "5";

    case REGISTER_SUCCESS = "6";
    case REGISTER_USERNAME_EXISTED = "7";

    case LOGOUT_SUCCESS = "8";

    case NUM_PRACTICE = "9";
    case SHOW_PRACTICE_DETAIL = "10";

    case NUM_EXAM = "11";
    case SHOW_EXAM_DETAIL = "12";

    case SHOW_LIST_TOPIC = "13";

    case ADD_NEW_PRACTICE_SUCCESSFUL = "14";
    case ADD_NEW_EXAM_SUCCESSFUL = "15";

    case LIST_QUESTION_ID = "16";
    case QUESTION = "17";

    case ANSWER_CORRECT = "18";
    case ANSWER_INCORRECT = "19";
}