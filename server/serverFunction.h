#ifndef _SERVER_FUNC_H
#define _SERVER_FUNC_H
#include "protocol.h"

void handle_message(char*, int);
int registerUser(char*, int);
int loginUser(char*, int);
int logoutUser(char*, int);
void showListPractices(char *, int );
void showListExams(char *, int );
void getListTopic(char *, int );
void addNewPractice(char *message, int socket);
void addNewExam(char *message, int socket);
int sendPracticeQuestion(char*, int);
int sendExamQuestion(char*, int);
int answerQuestion(char* message, int socket);

int loadGame(int);
void encryptPassword(char*);
void finish_with_error(MYSQL* con);
int calculateScore(char*, int, REQUEST_CODE);
void showDashboard(int socket);
int getHighScore(int socket);
#endif  // _SERVER_FUNC_H
