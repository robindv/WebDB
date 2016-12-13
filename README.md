This web application is used for managing servers for a course about web programming. The application is capable of
creating servers, configuring them, creating users and providing this information to the students following the course.
The application assumes that students are divided amongst different groups and that every group has exactly one server.

Environment
-----------
* An Apache CloudStack server for running the VMs.
* A webserver running this application.

Installing
----------
1. The website itself is just an ordinary Laravel application. Configure Apache to use the `public` directory as
web-root and execute `composer install` to install all dependencies.
2. Create a local copy of .env (by copying the .env.example file) and fill in the blanks.
