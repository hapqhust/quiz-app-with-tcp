#include <mysql/mysql.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

void encryptPassword(char *password)
{
    for (int i = 0; i < strlen(password); i++)
    {
        if ((int)password[i] > i)
        {
            password[i] = password[i] - i;
        }
    }
}

int main(int argc, char const *argv[])
{
  MYSQL *con = mysql_init(NULL);

  if (con == NULL)
  {
    printf("%s\n", mysql_error(con));
    exit(1);
  }

  char *server = "127.0.0.1";
  char *user = "root";
  char *password = "123456";

  if (mysql_real_connect(con, server, user, password, NULL, 3306, NULL, 0) == NULL)
  {
    printf("%s\n", mysql_error(con));
    mysql_close(con);
    exit(1);
  }
  if (!mysql_set_character_set(con, "utf8"))
  {
    printf("New client character set: %s\n",
           mysql_character_set_name(con));
  }

  //****CREATE DATABASE*****
  if (mysql_query(con, "CREATE DATABASE IF NOT EXISTS quiz_app CHARACTER SET utf8 COLLATE utf8_unicode_ci"))
  {
    if (strcmp(mysql_error(con),
               "Can't create database 'test'; database exists") == 0)
    {
      printf("Database is exists");
    }
    else
    {
      fprintf(stderr, "%s\n", mysql_error(con));
      mysql_close(con);
      exit(1);
    }
  }
  printf("%s\n", "Create database succesfully ...");

  // ****SELECT DATABASE****
  if (mysql_query(con, "USE quiz_app"))
  {
    fprintf(stderr, "%s\n", mysql_error(con));
    mysql_close(con);
    exit(1);
  }
  printf("%s\n", "Using database ...");

  // ****CREATE USER TABLE****
  if (mysql_query(con, "DROP TABLE IF EXISTS users"))
  {
    fprintf(stderr, "%s\n", mysql_error(con));
    mysql_close(con);
    exit(1);
  }

  if (mysql_query(
          con,
          "CREATE TABLE users(id INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(50) UNIQUE, password VARCHAR(100), role VARCHAR(10))"))
  {
    fprintf(stderr, "%s\n", mysql_error(con));
    mysql_close(con);
    exit(1);
  }
  // char pass1[10] = "123456", query[1024];
  // encryptPassword(pass1);
  // sprintf(query, "INSERT INTO users(username, password, role) VALUES('admin1', '%s', 'admin'), ('hapham', '%s', 'admin')", pass1, pass1);
  // if (mysql_query(
  //         con, query))
  // {
  //   fprintf(stderr, "%s\n", mysql_error(con));
  //   mysql_close(con);
  //   exit(1);
  // }
  printf("%s\n", "Create table users succesfully ...");

  // ****CREATE USER QUESTION****
  if (mysql_query(
          con,
          "CREATE TABLE IF NOT EXISTS questions(id INT PRIMARY KEY AUTO_INCREMENT, question VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci,"
          "answer1 VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci, answer2 VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci,"
          "answer3 VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci, answer4 VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci, trueanswer INT, topic VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci)"))
  {
    fprintf(stderr, "%s\n", mysql_error(con));
    mysql_close(con);
    exit(1);
  }
  printf("%s\n", "Create table questions succesfully ...");

  if (mysql_query(con, "CREATE TABLE IF NOT EXISTS practice_info(id INT PRIMARY KEY AUTO_INCREMENT,"
                       "practice_name VARCHAR(200), topic VARCHAR(100), num_question INT, time INT, created_at VARCHAR(30))"))
  {
    fprintf(stderr, "%s\n", mysql_error(con));
    mysql_close(con);
    exit(1);
  }
  printf("%s\n", "Create table practice_info succesfully ...");


  if (mysql_query(con, "CREATE TABLE IF NOT EXISTS exam_info(id INT PRIMARY KEY AUTO_INCREMENT,"
                       "exam_name VARCHAR(200), topic VARCHAR(100), num_question INT, time INT, start_at VARCHAR(30), close_at VARCHAR(30))"))
  {
    fprintf(stderr, "%s\n", mysql_error(con));
    mysql_close(con);
    exit(1);
  }
  printf("%s\n", "Create table exam_info succesfully ...");

  // if (mysql_query(
  //         con,
  //         "INSERT INTO questions(question, answer1, answer2, answer3, answer4, trueanswer, topic) "
  //         "VALUES('Tinh nghiem cua phuong trinh sau 5x + 4 = 0 ?', 'A. x = 0', 'B. x = -5/4', 'C. x = -4/5', 'D. x = 4/5', 3, 'Math'),"
  //         "('Me co 10 qua tao, me cho An 3 qua, me an 2 qua tao roi cat so con lai vao tu lanh. Hoi trong tu lanh con may qua ?', 'A. 3 qua ', 'B. 4 qua', 'C. 5 qua', 'D. 6 qua', 3, 'Math'),"
  //         "('Thuc hien phep tinh sau: 85 + 99 + 1 = .....', 'A. 185 ', 'B. 186', 'C.187', 'D.188', 1,'Math'),"
  //         "('Tuoi me va tuoi con cong lai bang 42 tuoi. Me hon con 30 tuoi. Hoi me bao nhieu tuoi, con bao nhieu tuoi ?', 'A. me 34 tuoi, con 8 tuoi', 'B. me 37 tuoi, con 5 tuoi', 'C. me 35 tuoi, con 7 tuoi', 'D.me 36 tuoi, con 6 tuoi', 4, 'Math'),"
  //         "('Mot hinh chu nhat co chieu dai la a, chieu rong la b (a, b cung mot don vi do). Chu vi hinh chu nhat do la .....', 'A. a * b ', 'B. (a + b) * 2', 'C. (a - b) / 2', 'D. a + b / 2', 2, 'Math'),"
  //         "('Mot cua hang ngay thu nhat ban duoc 2632kg duong, ngay thu hai ban duoc it hon ngay thu nhat 264kg. Hoi ca hai ngay cua hang ban duoc bao nhieu kg duong ?', 'A. 5528kg ', 'B. 5090kg', 'C. 5400kg', 'D. 5000kg', 4, 'Math'),"
  //         "('Tim x biet: x − 425 = 625 ?', 'A. x = 1050', 'B. x = 1000', 'C. x = 1010', 'D. x = 1040', 1, 'Math'),"
  //         "('Neu a = 8, b = 5, c = 2 thi a x b x c = ......', 'A. 90', 'B. 70', 'C. 80', 'D. 60', 3 ,'Math'),"
  //         "('So lon nhat co 4 chu so la:', 'A. 1000 ', 'B. 6666', 'C.8999', 'D.9999 ', 4,'Math'),"
  //         "('Chon hai duong thang ax va by vuong goc voi nhau tai O. Hay chi ra cau sai trong cac cau sau:', 'A. ∠xOy = 90° ', 'B. ∠aOb = 90°', 'C. ax va by khong the cat nhau', 'D. ax la duong phan giac cua goc bet bOy', 3,'Math'),"
  //         "('What year did 9/11 happen ?', 'A. 1999 ', 'B. 2001', 'C.2004', 'D.2000', 2 ,'English'),"
  //         "('Who were the defeated finalists at Euro 2008 ?', 'A. Germany ', 'B. England', 'C.France', 'D.Poland', 1,'English'),"
  //         "('How many independent countries border the Caspian sea ?', 'A. Four ', 'B. Six', 'C.Two', 'D.Five', 4,'English'),"
  //         "('What is the Closest Planet to Earth?', 'A. Venus ', 'B. Mars', 'C.Jupiter', 'D.Saturn', 1,'English'),"
  //         "('How many teeth do adult human have in their mouth ?', 'A. 30', 'B. 38', 'C. 32', 'D. 28', 3, 'English'),"
  //         "('What country declared independence from Serbia in 2008 ?',  'A. Albania', 'B. Kosovo', 'C. Montenegro', 'D. Macedonia', 2,'English'),"
  //         "('Who invented Mobile phone?', 'A. Martin Cooper', 'B. Alexander Graham Bell', 'C. Thomas Edison', 'D. Albert Einstein', 1, 'English'),"
  //         "('Which football club in England has won the most trophies ?', 'A. Liverpool', 'B. Chelsea', 'C. Arsenal', 'D. Manchester United', 4, 'English'),"
  //         "('Entomology is the science that studies ?','A. The formation of rocks', 'B. Behavior of human beings', 'C. Insects', 'D. Computers', 3, 'English'),"
  //         "('Grand Central Terminal, Park Avenue, New York is the world ?', 'A. Highest railway station', 'B. Largest railway station', 'C. Longest railway station', 'D. Largest airport', 2, 'English'),"
  //         "('Hitler party which came into power in 1933 is known as ?',  'A. Nazi Party', 'B. Labour Party', 'C. Ku-Klux-Klan', 'D. Democratic Party', 1,'English'),"
  //         "('Dong nao neu dung cac tac pham duoc xep vao loai trao phung ?',  'A. Chu nguoi tu tu, Hai dua tre', 'B. So do, Tinh than the duc', 'C. So do, Chi Pheo', 'D. Tinh than the duc, Chi Pheo', 2,'Literature'),"
  //         "('Bai tho nao nguyen van bang chu Han ?',  'A. Tu tinh', 'B. Cau ca mua thu', 'C. Bai ca ngan di tren bai cat', 'D. Bai ca phong canh Huong Son', 3, 'Literature'),"
  //         "('Dong nao neu dung ten cac nha tho trung dai: “cong khai khang dinh ca tinh doc dao cua minh. Tho van cua ho the hien su buc boi cua lich su muon tung pha cai khuan kho chat hep, tu tung, va gia doi cua che do phong kien trong thoi ki suy thoai”.', 'A. Ho Xuan Huong, Cao Ba Quat, Nguyen Dinh Chieu', 'B. Ho Xuan Huong, Nguyen Cong Chu, Cao Ba Quat', 'C. Ho Xuan Huong, Nguyen Cong Chu, Nguyen Khuyen', 'D. Ho Xuan Huong, Nguyen Khuyen, Nguyen Dinh Chieu', 2, 'Literature'),"
  //         "('Tim tu lay trong cac tu duoi day ?', 'A. Tuoi tot', 'B. Tuoi dep', 'C. Tuoi tan', 'D. Tuoi tham', 3, 'Literature'),"
  //         "('Don vi cau tao tu la gi ?',  'A. Tieng', 'B. Tu', 'C. Chu cai', 'D. Nguyen am', 1,'Literature'),"
  //         "('Tu tieng Viet duoc chia lam may loai ?', 'A. 2', 'B. 3', 'C. 4', 'D. 5', 1, 'Literature'),"
  //         "('Tu phuc gom may tieng ?', 'A. 2 hoac nhieu hon 2', 'B. 3', 'C. 4', 'D. nhieu hon 2', 1, 'Literature'),"
  //         "('Tu “khanh khach” la tu gi ?', 'A. Tu don', 'B. Tu ghep dang lap', 'C. Tu ghep chinh phu', 'D. Tu lay tuong thanh', 4, 'Literature'),"
  //         "('Khi giai thich \"Cau hon: xin duoc lay lam vo\" la da giai thich nghia cua tu bang cach nao ?', 'A. Dung tu dong nghia voi tu can duoc giai thich.', 'B. Trinh bay khai niem ma tu bieu thi.', 'C. Ket hop giua dung tu dong nghia voi trinh bay khai niem ma tu bieu thi.', 'D. Dung tu trai nghia voi tu duoc giai thich.', 2, 'Literature'),"
  //         "('Chon tu thich hop dien vao cho trong trong cau sau : Xe toi bi hong vi vay  toi...di bo di hoc.', 'A. Bi', 'B. Duoc', 'C. Can', 'D. Phai', 4, 'Literature'),"
  //         "('Chu de bai tho May va song la gi ?', 'A. Tinh mau tu thieng lieng', 'B. Tinh ban be tham thiet', 'C.Tinh anh em sau nang', 'D. Tinh yeu sau sac', 1, 'Literature'),"
  //         "('Dong nao sau day nhan dinh khong dung ve nhan vat em be trong bai May va song ?', 'A. Yeu duoi, khong thich cac tro choi', 'B. Ham choi, tinh nghich', 'C. Hom hinh, sang tao', 'D. Hon nhien, yeu thuong me tha thiet', 1, 'Literature')"))
  // {
  //   fprintf(stderr, "%s\n", mysql_error(con));
  //   mysql_close(con);
  //   exit(1);
  // }

  // mysql_close(con);
  // exit(0);
}
