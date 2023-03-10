//
// Created by hapq on 19/1/2023.
//
#ifndef NETWORKPROGRAMMING_PROTOCOL_H
#define NETWORKPROGRAMMING_PROTOCOL_H

typedef enum
{
    LOGIN,
    REGISTER,
    LOGOUT,
    SHOW_LIST_PRACTICE,
    SHOW_LIST_EXAM,
    GET_LIST_TOPIC,
    ADD_NEW_PRACTICE,
    ADD_NEW_EXAM,
    JOIN_PRACTICE,
    JOIN_EXAM,
    SAVE_RESULT,
    ANSWER,
    CHECK_JOINED_EXAM,
    DASHBOARD,
    STOP,
    BREAK,

    
    EXIT,
} REQUEST_CODE;

typedef enum
{
    QUERY_FAIL,
    LOGIN_SUCCESS,
    USERNAME_NOTFOUND,
    USERNAME_BLOCKED,
    USERNAME_IS_SIGNIN,
    PASSWORD_INCORRECT,

    REGISTER_SUCCESS,
    REGISTER_USERNAME_EXISTED,

    LOGOUT_SUCCESS,

    NUM_PRACTICE,
    SHOW_PRACTICE_DETAIL,

    NUM_EXAM,
    SHOW_EXAM_DETAIL,

    SHOW_LIST_TOPIC,

    ADD_NEW_PRACTICE_SUCCESSFUL,

    ADD_NEW_EXAM_SUCCESSFUL,

    LIST_QUESTION_ID,
    QUESTION,

    ANSWER_CORRECT,
    ANSWER_INCORRECT,

    SAVE_RESULT_SUCCESSFUL,

    PARTICIPATED,
    NOT_PARTICIPATED,

    DASHBOARD_NUM_ROW,
    DASHBOARD_INFO,

    END_GAME,
} RESPONSE_CODE;

#endif // NETWORKPROGRAMMING_PROTOCOL_H
