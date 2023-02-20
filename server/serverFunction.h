#ifndef _SERVER_FUNC_H
#define _SERVER_FUNC_H
#include "protocol.h"

void finish_with_error(MYSQL* con);
void handle_message(char*, int);

void loginUser(char*, int);
void registerUser(char*, int);
void logoutUser(char*, int);
void showListPractices(char *, int);
void showListExams(char *, int);
void getListTopic(char *, int);
void addNewPractice(char *message, int socket);
void addNewExam(char *message, int socket);
void sendPracticeQuestion(char*, int);
void sendExamQuestion(char*, int);
void answerQuestion(char* message, int socket);
void saveResult(char *message, int socket);
void checkJoinExam(char *message, int socket);

int calculateScore(char*, int, REQUEST_CODE);
void showDashboard(char *message, int socket);
#endif  // _SERVER_FUNC_H
