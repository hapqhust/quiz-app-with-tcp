#include <arpa/inet.h>
#include <errno.h>
#include <libgen.h>
#include <mysql/mysql.h>
#include <netdb.h>
#include <netinet/in.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>

#include "protocol.h"
#include "serverFunction.h"
#define BUFF_SIZE 1024

extern MYSQL *con;

void finish_with_error(MYSQL *con)
{
    fprintf(stderr, "%s\n", mysql_error(con));
    mysql_close(con);
    exit(1);
}

void handle_message(char *message, int socket)
{
    if (strlen(message) <= 0)
        return;
    char subtext[BUFF_SIZE], *token;
    strcpy(subtext, message);
    token = strtok(subtext, "|");
    REQUEST_CODE type = atoi(token);
    //  char server_message[200] = "\0";
    printf("request: %s\n", token);
    switch (type)
    {
    case LOGIN:
        printf("handle login\n");
        loginUser(message, socket);
        break;
    case REGISTER:
        printf("handle register\n");
        registerUser(message, socket);
        break;
    case LOGOUT:
        printf("Handle logout\n");
        logoutUser(message, socket);
        break;
    case SHOW_LIST_PRACTICE:
        printf("Handle list practices\n");
        showListPractices(message, socket);
        break;
    case SHOW_LIST_EXAM:
        printf("Handle list exams\n");
        showListExams(message, socket);
        break;
    case GET_LIST_TOPIC:
        printf("Handle list topic\n");
        getListTopic(message, socket);
        break;
    case ADD_NEW_PRACTICE:
        printf("Handle add new practice\n");
        addNewPractice(message, socket);
        break;
    case ADD_NEW_EXAM:
        printf("Handle add new exam\n");
        addNewExam(message, socket);
        break;
    case JOIN_PRACTICE:
    {
        printf("handle join practice\n");
        sendPracticeQuestion(message, socket);
        break;
    }
    case JOIN_EXAM:
    {
        printf("handle join practice\n");
        sendExamQuestion(message, socket);
        break;
    }
    case ANSWER:
    {
        printf("Handle answer \n");
        answerQuestion(message, socket);
        break;
    }
    case DASHBOARD:
    {
        printf("Handle dashboard\n");
        showDashboard(socket);
        break;
    }
    case STOP:
    case BREAK:
    {
        printf("Handle score\n");
        calculateScore(message, socket, type);
        break;
    }
    default:
        break;
    }
}
void showDashboard(int socket)
{
    char query[BUFF_SIZE] = "\0";
    char serverMess[BUFF_SIZE] = "\0";
    char temp[100] = "\0";
    sprintf(query, "SELECT username, highScore from users ORDER BY highScore DESC");
    //    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
    }
    MYSQL_RES *result = mysql_store_result(con);
    if (mysql_num_rows(result) == 0)
    {
        sprintf(serverMess, "%d|Cannot show dashboard\n", QUERY_FAIL);
        send(socket, serverMess, strlen(serverMess), 0);
    }
    else
    {
        MYSQL_ROW row;
        while (row = mysql_fetch_row(result))
        {
            sprintf(temp, "%d|%s|%s|", DASHBOARD_INFO, row[0], row[1]);
            strcat(serverMess, temp);
        }
        printf("%s\n", serverMess);
    }
    send(socket, serverMess, strlen(serverMess), 0);
}

int answerQuestion(char *message, int socket)
{
    int question_id;
    int answer, trueAnswer;
    char *token;
    char serverMess[BUFF_SIZE] = "\0";
    char query[200] = "\0";
    RESPONSE_CODE code;
    // Split message
    token = strtok(message, "|");
    token = strtok(NULL, "|");
    question_id = atoi(token);
    token = strtok(NULL, "|");
    answer = atoi(token);
    printf("%d\n", answer);

    // Query question and answer from database
    sprintf(query, "SELECT * from questions where id = %d", question_id);
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return 0;
    }
    MYSQL_RES *result = mysql_store_result(con);
    if (mysql_num_rows(result) == 0)
    {
        sprintf(serverMess, "%d|Question not found\n", QUERY_FAIL);
        printf("Cannot find questions \n");
        send(socket, serverMess, strlen(serverMess), 0);
        return 0;
    }
    else
    {
        MYSQL_ROW row;
        row = mysql_fetch_row(result);
        printf("True answer is: %d\n", atoi(row[6]));
        if (atoi(row[6]) == answer)
        {
            printf("%s\n", row[6]);
            code = ANSWER_CORRECT;
            sprintf(serverMess, "%d|Answer correct|", code);
        }
        else
        {
            code = ANSWER_INCORRECT;
            sprintf(serverMess, "%d|Answer incorrect|", code);
        }
        send(socket, serverMess, strlen(serverMess), 0);
    }
}

int calculateScore(char *message, int socket, REQUEST_CODE code)
{
    char *token;
    char username[50];
    char serverMess[BUFF_SIZE] = "\0";
    int position;
    int score;
    int highScore;
    char query[BUFF_SIZE] = "\0";
    // Get username and position
    token = strtok(message, "|");
    token = strtok(NULL, "|");
    strcpy(username, token);
    token = strtok(NULL, "|");
    position = atoi(token);

    if (code == STOP)
    {
        score = position;
    }
    else
    {
        if (position == 0)
            score = 0;
        else if (position >= 1 && position < 5)
            score = 1;
        else if (position >= 5 && position < 10)
            score = 5;
        else if (position > 10 && position < 15)
            score = 10;
        else if (position == 15)
            score = 15;
    }
    // Get username from database
    sprintf(query, "SELECT * from users where username='%s'", username);
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        printf("Query fail\n");
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return 0;
    }
    MYSQL_RES *result = mysql_store_result(con);
    printf("Number of row %lld\n", mysql_num_rows(result));
    MYSQL_ROW row;
    if (row = mysql_fetch_row(result))
    {
        printf("Score %s\n", row[3]);
        highScore = atoi(row[3]);
    }
    else
        printf("Cannot fetch\n");
    //    return highScore;
    // Update highscore in database
    if (score > highScore)
    {
        sprintf(query, "UPDATE users SET highScore = '%d' where username='%s'", score, username);
        printf("%s\n", query);
        if (mysql_query(con, query))
        {
            printf("Query fail\n");
            sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
            return 0;
        }
    }
    sprintf(serverMess, "%d|%d|", SCORE_INFO, score);
    send(socket, serverMess, strlen(serverMess), 0);
    printf("New Score %d\n", score);
}
int sendPracticeQuestion(char *message, int socket)
{
    printf("Start send question \n");
    char serverMess[BUFF_SIZE] = "\0", list_question[BUFF_SIZE] = "\0";
    char query[200] = "\0";
    int practice_id, position;
    char temp[BUFF_SIZE];
    char *token;
    char question[BUFF_SIZE];
    int question_id, count;
    char buff[BUFF_SIZE];
    int bytes_received = 0;
    int send_status = 0;

    // Get position
    printf("%s\n", message);
    token = strtok(message, "|");

    token = strtok(NULL, "|");
    strcpy(temp, token);
    practice_id = atoi(temp);

    token = strtok(NULL, "|");
    strcpy(temp, token);
    position = atoi(temp);

    printf("Position %d", position);

    // Get question from database
    if (position == 0)
    {
        sprintf(query, "SELECT * FROM practice_question WHERE practice_id = %d ORDER BY RAND()", practice_id);
        printf("%s\n", query);
        if (mysql_query(con, query))
        {
            sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
            return 0;
        }

        MYSQL_RES *result = mysql_store_result(con);
        if (mysql_num_rows(result) == 0)
        {
            sprintf(serverMess, "%d|Question not found|\n", QUERY_FAIL);
            printf("Cannot find questions \n");
            send(socket, serverMess, strlen(serverMess), 0);
            return 0;
        }
        else
        {
            count = mysql_num_rows(result);
            MYSQL_ROW row;
            while ((row = mysql_fetch_row(result)) != NULL)
            {
                strcat(list_question, row[1]);
                strcat(list_question, "|");
            }
            sprintf(serverMess, "%d|%d|%s", LIST_QUESTION_ID, count, list_question);
            send(socket, serverMess, strlen(serverMess), 0);
        }
    }
    else
    {
        question_id = position;
        sprintf(query, "SELECT * FROM questions WHERE id = %d", question_id);
        printf("%s\n", query);
        if (mysql_query(con, query))
        {
            sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
        }
        MYSQL_RES *result_ques = mysql_store_result(con);
        MYSQL_ROW row_question = mysql_fetch_row(result_ques);
        sprintf(question, "%d|%s|%s|%s|%s|%s\n", atoi(row_question[0]), row_question[1], row_question[2], row_question[3], row_question[4], row_question[5]);
        sprintf(serverMess, "%d|%s|", QUESTION, question);
        send(socket, serverMess, strlen(serverMess), 0);
    }
}

int sendExamQuestion(char *message, int socket)
{
    printf("Start send question \n");
    char serverMess[BUFF_SIZE] = "\0", list_question[BUFF_SIZE] = "\0";
    char query[200] = "\0";
    int exam_id, position;
    char temp[BUFF_SIZE];
    char *token;
    char question[BUFF_SIZE];
    int question_id, count;
    char buff[BUFF_SIZE];
    int bytes_received = 0;
    int send_status = 0;

    // Get position
    printf("%s\n", message);
    token = strtok(message, "|");

    token = strtok(NULL, "|");
    strcpy(temp, token);
    exam_id = atoi(temp);

    token = strtok(NULL, "|");
    strcpy(temp, token);
    position = atoi(temp);

    printf("Position %d", position);

    // Get question from database
    if (position == 0)
    {
        sprintf(query, "SELECT * FROM exam_question WHERE exam_id = %d ORDER BY RAND()", exam_id);
        printf("%s\n", query);
        if (mysql_query(con, query))
        {
            sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
            return 0;
        }
        MYSQL_RES *result = mysql_store_result(con);
        if (mysql_num_rows(result) == 0)
        {
            sprintf(serverMess, "%d|Question not found|\n", QUERY_FAIL);
            printf("Cannot find questions \n");
            send(socket, serverMess, strlen(serverMess), 0);
            return 0;
        }
        else
        {
            count = mysql_num_rows(result);
            MYSQL_ROW row;
            while ((row = mysql_fetch_row(result)) != NULL)
            {
                strcat(list_question, row[1]);
                strcat(list_question, "|");
            }
            sprintf(serverMess, "%d|%d|%s", LIST_QUESTION_ID, count, list_question);
            printf("%s\n", serverMess);
            send(socket, serverMess, strlen(serverMess), 0);
        }
    }
    else
    {
        question_id = position;
        sprintf(query, "SELECT * FROM questions WHERE id = %d", question_id);
        printf("%s\n", query);
        if (mysql_query(con, query))
        {
            sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
        }
        MYSQL_RES *result_ques = mysql_store_result(con);
        MYSQL_ROW row_question = mysql_fetch_row(result_ques);
        sprintf(question, "%d|%s|%s|%s|%s|%s\n", atoi(row_question[0]), row_question[1], row_question[2], row_question[3], row_question[4], row_question[5]);
        sprintf(serverMess, "%d|%s|", QUESTION, question);
        send(socket, serverMess, strlen(serverMess), 0);
    }
}
int registerUser(char *message, int socket)
{
    char username[255] = "\0";
    char password[255] = "\0";
    char serverMess[BUFF_SIZE] = "\0";
    char query[BUFF_SIZE] = "\0";
    char *token;

    // Split message to get username and password
    token = strtok(message, "|");
    token = strtok(NULL, "|");
    strcpy(username, token);
    token = strtok(NULL, "|");
    strcpy(password, token);

    // Check username is existed ?
    sprintf(query, "SELECT * FROM users WHERE username = '%s' ",
            username);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s|\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return 0;
    }
    MYSQL_RES *result = mysql_store_result(con);
    if (mysql_num_rows(result))
    {
        sprintf(serverMess, "%d|This username is existed|\n", REGISTER_USERNAME_EXISTED);
        send(socket, serverMess, strlen(serverMess), 0);
        return 0;
    }
    else
    {
        // Insert new account into database
        sprintf(query, "INSERT INTO users (username, password, role) VALUES ('%s', '%s', 'user')",
                username, password);
        if (mysql_query(con, query))
        {
            sprintf(serverMess, "%d|%s|\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
            return 0;
        }

        // Insert this account into signing in accounts
        sprintf(query, "INSERT INTO using_accounts (username) VALUES ('%s')", username);
        if (mysql_query(con, query))
        {
            sprintf(serverMess, "%d|%s|\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
            return 0;
        }

        sprintf(serverMess, "%d|%s|Successfully registered|\n", REGISTER_SUCCESS, "user");
        send(socket, serverMess, sizeof(serverMess), 0);
        return 1;
    }
}

int loginUser(char *message, int socket)
{
    printf("Start handle login\n");
    char username[255] = "\0";
    char password[255] = "\0";
    char role[20] = "\0";
    char serverMess[BUFF_SIZE] = "\0";
    char *token;
    char query[BUFF_SIZE] = "\0";
    // Split message to get username and password
    token = strtok(message, "|");
    token = strtok(NULL, "|");
    strcpy(username, token);
    token = strtok(NULL, "|");
    strcpy(password, token);
    //    printf("%s %s\n",username, password);

    // Query to validate account
    // Check username

    if (mysql_query(
            con,
            "CREATE TABLE IF NOT EXISTS using_accounts(id INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(255) UNIQUE)"))
    {
        fprintf(stderr, "%s\n", mysql_error(con));
        mysql_close(con);
        return 0;
    }
    sprintf(query, "SELECT * from users where username='%s'", username);
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s|\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return 0;
    }
    MYSQL_RES *result = mysql_store_result(con);
    if (mysql_num_rows(result) == 0)
    {
        sprintf(serverMess, "%d|Invalid username|\n", USERNAME_NOTFOUND);
        send(socket, serverMess, strlen(serverMess), 0);
        return 0;
    }
    else
    {
        // Check password
        MYSQL_ROW row = mysql_fetch_row(result);
        if (strcmp(row[2], password) != 0)
        {
            sprintf(serverMess, "%d|Password is incorrect|\n", PASSWORD_INCORRECT);
            send(socket, serverMess, strlen(serverMess), 0);
            return 0;
        }
        else
        {
            strcpy(role, row[3]);
            // Check account is signing in other device
            char server_message[100] = "\0";
            char temp[512];
            sprintf(query, "SELECT * from using_accounts where username='%s'", username);
            if (mysql_query(con, query))
            {
                sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
                send(socket, serverMess, strlen(serverMess), 0);
                return 0;
            }
            MYSQL_RES *result = mysql_store_result(con);
            if (mysql_num_rows(result) == 0)
            {
                // Push account into signing in account table
                sprintf(query, "INSERT INTO using_accounts (username) VALUES ('%s')", username);
                mysql_query(con, query);

                sprintf(server_message, "%d|%s|Successfully logged in|\n", LOGIN_SUCCESS, role);
                send(socket, server_message,sizeof(server_message), 0);
                return 0;
            }
            else
            {
                sprintf(server_message, "%d|Your account is signing in other device|\n", USERNAME_IS_SIGNIN);
                send(socket, server_message, sizeof(server_message), 0);
                return 0;
            }
        }
    }
}
int logoutUser(char *message, int socket)
{
    printf("Start handle logout\n");
    char username[255] = "\0";
    char server_message[BUFF_SIZE] = "\0";
    char *token;
    char query[300] = "\0";

    // Split message to get username
    token = strtok(message, "|");
    token = strtok(NULL, "|");
    strcpy(username, token);

    // Delete in database
    sprintf(query, "DELETE FROM using_accounts where username='%s'", username);
    if (mysql_query(con, query))
    {
        sprintf(server_message, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, server_message, strlen(server_message), 0);
        return 0;
    }
    printf("logout successful!\n");
    sprintf(server_message, "%d|\n", LOGOUT_SUCCESS);
    send(socket, server_message, strlen(server_message), 0);

    return 1;
}

void showListPractices(char *message, int socket)
{
    printf("Start send list practices\n");
    int position;
    char temp[BUFF_SIZE];
    char serverMess[BUFF_SIZE] = "\0";
    char query[200] = "\0";
    char *token;
    char question[BUFF_SIZE];
    int level;

    // Get position
    printf("%s\n", message);
    token = strtok(message, "|");
    token = strtok(NULL, "|");
    strcpy(temp, token);
    position = atoi(temp);
    printf("Position %d\n", position);
    // Get position to choose appropriate question
    if (position == 0)
    {
        sprintf(query, "SELECT * FROM practice_info");
        printf("%s\n", query);
        if (mysql_query(con, query))
        {
            sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
            return;
        }
        MYSQL_RES *result = mysql_store_result(con);
        sprintf(serverMess, "%d|%lld\n", NUM_PRACTICE, mysql_num_rows(result));
    }
    else
    {
        sprintf(query, "SELECT * FROM practice_info WHERE id = %d", position);
        printf("%s\n", query);
        if (mysql_query(con, query))
        {
            sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
            return;
        }
        MYSQL_RES *result = mysql_store_result(con);
        MYSQL_ROW row;
        if ((row = mysql_fetch_row(result)) != NULL)
        {
            sprintf(serverMess, "%d|%s|%s|%s|%s|%s|%s\n", SHOW_PRACTICE_DETAIL, row[0], row[1], row[2], row[3], row[4], row[5]);
            // sprintf(serverMess, "%s", list_topic);
            // printf("thông điệp là: %s\n", serverMess);
        }
    }
    send(socket, serverMess, strlen(serverMess), 0);
    return;
}

void showListExams(char *message, int socket)
{
    printf("Start send list exams\n");
    int position;
    char temp[BUFF_SIZE];
    char serverMess[BUFF_SIZE] = "\0";
    char query[200] = "\0";
    char *token;
    char question[BUFF_SIZE];
    int level;

    // Get position
    printf("%s\n", message);
    token = strtok(message, "|");
    token = strtok(NULL, "|");
    strcpy(temp, token);
    position = atoi(temp);
    printf("Position %d\n", position);
    // Get position to choose appropriate question
    if (position == 0)
    {
        sprintf(query, "SELECT * FROM exam_info");
        printf("%s\n", query);
        if (mysql_query(con, query))
        {
            sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
            return;
        }
        MYSQL_RES *result = mysql_store_result(con);
        sprintf(serverMess, "%d|%lld\n", NUM_EXAM, mysql_num_rows(result));
    }
    else
    {
        sprintf(query, "SELECT * FROM exam_info WHERE id = %d", position);
        printf("%s\n", query);
        if (mysql_query(con, query))
        {
            sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
            send(socket, serverMess, strlen(serverMess), 0);
            return;
        }
        MYSQL_RES *result = mysql_store_result(con);
        MYSQL_ROW row;
        if ((row = mysql_fetch_row(result)) != NULL)
        {
            sprintf(serverMess, "%d|%s|%s|%s|%s|%s|%s|%s\n", SHOW_EXAM_DETAIL, row[0], row[1], row[2], row[3], row[4], row[5], row[6]);
            // sprintf(serverMess, "%s", list_topic);
        }
    }
    printf("thông điệp là: %s\n", serverMess);
    send(socket, serverMess, strlen(serverMess), 0);
    return;
}

void getListTopic(char *message, int socket)
{
    char serverMess[BUFF_SIZE] = "\0";
    char query[200] = "\0";
    RESPONSE_CODE code;

    sprintf(query, "SELECT DISTINCT topic FROM questions");
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    MYSQL_RES *result = mysql_store_result(con);
    if (mysql_num_rows(result) == 0)
    {
        sprintf(serverMess, "%d|There is not any topic\n", QUERY_FAIL);
        printf("There is not any topic \n");
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    else
    {
        int num_row = mysql_num_rows(result);
        char list_topic[100];
        MYSQL_ROW row;
        sprintf(list_topic, "%d|%d|", SHOW_LIST_TOPIC, num_row);
        while ((row = mysql_fetch_row(result)) != NULL)
        {
            printf("%s\n", row[0]);
            strcat(list_topic, row[0]);
            strcat(list_topic, "|");
        }
        sprintf(serverMess, "%s", list_topic);
        printf("thông điệp là: %s\n", serverMess);
        send(socket, serverMess, strlen(serverMess), 0);
    }
}

void addNewPractice(char *message, int socket)
{
    char serverMess[BUFF_SIZE] = "\0";
    char name[BUFF_SIZE], topic[BUFF_SIZE], temp[BUFF_SIZE], created_at[BUFF_SIZE];
    char *token;
    int num_question, time, id;
    char query[200] = "\0";

    printf("%s\n", message);
    token = strtok(message, "|");

    token = strtok(NULL, "|");
    strcpy(name, token);

    token = strtok(NULL, "|");
    strcpy(topic, token);

    token = strtok(NULL, "|");
    strcpy(temp, token);
    num_question = atoi(temp);

    token = strtok(NULL, "|");
    strcpy(temp, token);
    time = atoi(temp);

    token = strtok(NULL, "|");
    strcpy(created_at, token);

    sprintf(query, "INSERT INTO practice_info(practice_name, topic, num_question, time, created_at)"
                   "VALUES('%s','%s',%d,%d,'%s')",
            name, topic, num_question, time, created_at);
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }

    sprintf(query, "SELECT id FROM practice_info WHERE created_at = '%s'", created_at);
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    MYSQL_RES *result = mysql_store_result(con);
    if (mysql_num_rows(result) == 0)
    {
        sprintf(serverMess, "%d|Query fail when select id from practice_info table\n", QUERY_FAIL);
        printf("Query fail when select id from practice_info table \n");
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    else
    {
        MYSQL_ROW row;
        if ((row = mysql_fetch_row(result)) != NULL)
        {
            printf("Id la: %d\n", atoi(row[0]));
            id = atoi(row[0]);
        }
    }

    sprintf(query, "CREATE TABLE IF NOT EXISTS practice_question(practice_id INT, question_id INT,"
                   "FOREIGN KEY (practice_id) REFERENCES practice_info(id),FOREIGN KEY (question_id) REFERENCES questions(id))");
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    sprintf(query, "SELECT id FROM questions WHERE topic = '%s' ORDER BY RAND() LIMIT %d", topic, num_question);
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    result = mysql_store_result(con);
    if (mysql_num_rows(result) == 0)
    {
        sprintf(serverMess, "%d|Query fail\n", QUERY_FAIL);
        printf("Query fail \n");
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    else
    {
        MYSQL_ROW row;
        while ((row = mysql_fetch_row(result)) != NULL)
        {
            printf("Id la: %d\n", atoi(row[0]));
            // id = row[0];

            sprintf(query, "INSERT INTO practice_question(practice_id, question_id) VALUES(%d,%d)",
                    id, atoi(row[0]));
            printf("%s\n", query);
            if (mysql_query(con, query))
            {
                sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
                send(socket, serverMess, strlen(serverMess), 0);
            }
        }
    }

    sprintf(serverMess, "%d|%s\n", ADD_NEW_PRACTICE_SUCCESSFUL, "Add new practice successful");
    send(socket, serverMess, strlen(serverMess), 0);
}

void addNewExam(char *message, int socket)
{
    char serverMess[BUFF_SIZE] = "\0";
    char name[BUFF_SIZE], topic[BUFF_SIZE], temp[BUFF_SIZE], start_at[BUFF_SIZE], close_at[BUFF_SIZE];
    char *token;
    int num_question, time, id;
    char query[200] = "\0";

    printf("%s\n", message);
    token = strtok(message, "|");

    token = strtok(NULL, "|");
    strcpy(name, token);

    token = strtok(NULL, "|");
    strcpy(topic, token);

    token = strtok(NULL, "|");
    strcpy(temp, token);
    num_question = atoi(temp);

    token = strtok(NULL, "|");
    strcpy(temp, token);
    time = atoi(temp);

    token = strtok(NULL, "|");
    strcpy(start_at, token);

    token = strtok(NULL, "|");
    strcpy(close_at, token);

    sprintf(query, "INSERT INTO exam_info(exam_name, topic, num_question, time, start_at, close_at)"
                   "VALUES('%s','%s',%d,%d,'%s','%s')",
            name, topic, num_question, time, start_at, close_at);
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }

    sprintf(query, "SELECT id FROM exam_info WHERE start_at = '%s'", start_at);
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    MYSQL_RES *result = mysql_store_result(con);
    if (mysql_num_rows(result) == 0)
    {
        sprintf(serverMess, "%d|Query fail when select id from exam_info table\n", QUERY_FAIL);
        printf("Query fail when select id from exam_info table \n");
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    else
    {
        MYSQL_ROW row;
        if ((row = mysql_fetch_row(result)) != NULL)
        {
            printf("Id la: %d\n", atoi(row[0]));
            id = atoi(row[0]);
        }
    }

    sprintf(query, "CREATE TABLE IF NOT EXISTS exam_question(exam_id INT, question_id INT,"
                   "FOREIGN KEY (exam_id) REFERENCES exam_info(id),FOREIGN KEY (question_id) REFERENCES questions(id))");
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    sprintf(query, "SELECT id FROM questions WHERE topic = '%s' ORDER BY RAND() LIMIT %d", topic, num_question);
    printf("%s\n", query);
    if (mysql_query(con, query))
    {
        sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    result = mysql_store_result(con);
    if (mysql_num_rows(result) == 0)
    {
        sprintf(serverMess, "%d|Query fail\n", QUERY_FAIL);
        printf("Query fail \n");
        send(socket, serverMess, strlen(serverMess), 0);
        return;
    }
    else
    {
        MYSQL_ROW row;
        while ((row = mysql_fetch_row(result)) != NULL)
        {
            printf("Id la: %d\n", atoi(row[0]));
            // id = row[0];

            sprintf(query, "INSERT INTO exam_question(exam_id, question_id) VALUES(%d,%d)",
                    id, atoi(row[0]));
            printf("%s\n", query);
            if (mysql_query(con, query))
            {
                sprintf(serverMess, "%d|%s\n", QUERY_FAIL, mysql_error(con));
                send(socket, serverMess, strlen(serverMess), 0);
            }
        }
    }

    sprintf(serverMess, "%d|%s\n", ADD_NEW_EXAM_SUCCESSFUL, "Add new exam successful");
    send(socket, serverMess, strlen(serverMess), 0);
}