# <p align="center">Professional PHP Blog


# <p align="left">🗒️Description

This project is a professional blog developed in PHP, offering a showcase for blog articles and a secure administration interface. The site is divided into two main parts:

## <p align="center">Blog Section 

### Pages accessible to all visitors:

- Home Page
-  Articles Page
-  Article Detail Page 
- Contact Page
- Registration
- Login/Logout
- Privacy Policy
- Legal Notice

### Pages accessible to users with the subscriber role
In addition to the pages accessible to visitors:

- Add Comment
- My Profile
## <p align="center"> Administration Section

### Pages reserved for users with the administrator role
In addition to the pages accessible to visitors and subscribers:

 Administration Menu:

**--Article Management:**

- Add a post
- Edit a post
- Delete a post

**--Comment Management:**

- Approve a comment
- Leave a comment pending
- Reject a comment

**--User Management:**

- Add a user (subscriber or admin)
- Edit a user (subscriber or admin)
- Delete a user (subscriber or admin)

----------

# <p align="left">🛠️Installation

 
Before you begin, ensure that your development environment has the following:

   - PHP: Version 8.2.12 or highe
   - XAMPP: XAMPP with PHP 8.2.12 is recommended

## 🛠️ Configuring XAMPP for Email Sending (Windows)
### 1. Modify the xampp/php/php.ini file:

    - Open the xampp/php/php.ini file with a text editor.
    - Ensure the following lines are configured correctly


```
SMTP=smtp.example.com
smtp_port=587
sendmail_from=your_email@example.com
sendmail_path="\"C:\xampp\sendmail\sendmail.exe\" -t"
```
        
### 2. Modify the xampp/sendmail/sendmail.ini file:
    
    - Open the xampp/sendmail/sendmail.ini file with a text editor.
    
    - Ensure the following lines are configured correctly:
```
smtp_server=smtp.example.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=your_email@example.com
auth_password=your_password
force_sender=your_email@example.com
```
These configurations allow XAMPP on Windows to send emails from your application. Once these configuration steps are completed, you can proceed with cloning and setting up your project.

## 3. Clone the GitHub repository and configure your project:

```bash
git clone https://github.com/Psylvie/Blog.git
cd Blog
```


### 4. Install dependencies:
```bash
composer install
```   

## 🛠️  File configuration
Before you start using the project, follow these steps to configure your configuration files:

### 1. Add your personal information:
 - Open the  ".env.dev"  file and change the envirinment variables

-  Rename the ".env.dev" file to ".env"

### 2. Importing the database schema:

-Create a new database named `My_Blog` in your database management system.

-Download the provided SQL file (my_blog.sql) containing the necessary table structure.

-Import this SQL file into your database to create the required tables for the project.

###  4.Connection to the administration interface:

To access the administration interface as an administrator, use the following credentials:

-Email address: admin@admin.fr

-Password: Password1234
    

# <p align="left">🚀 Getting Started  

To begin using the blog application:

Explore articles, register as a subscriber, or log in as an administrator to manage posts, users, and comments.



# ❤️ Support  
A simple star to this project repo is enough to keep me motivated on this project for days. If you feel very excited about this project, let me know!

If you have any questions, or ideas for developing this blog, do not hesitate to contact me: peuzin.sylvie.sp@gmail.com


# 🙇 Author
### <p align="center">PEUZIN SYLVIE
### <p align="center">  Développeuse d'application PHP/SYMFONY
 Linkedin: [@peuzinsylvie](https://www.linkedin.com/in/sylvie-peuzin/)
