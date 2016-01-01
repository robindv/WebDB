This web application is used for managing servers for a course about web programming. The application is capable of 
creating servers, configuring them, creating users and providing this information to the students following the course.
The application assumes that students are divided amongst different groups and that every group has exactly one server.

Environment
-----------
* A Hyper-V server, hosting all servers and running an [API](https://github.com/goomens/UvA.VM).
* A reverse proxy server (Apache for example) routing all the web-traffic to the different servers.
* A webserver running this application.

Installing
----------
1. The website itself is just an ordinary Laravel application. Configure Apache to use the `public` directory as
web-root and execute `composer install` to install all dependencies.
2. Create a local copy of .env (by copying the .env.example file) and fill in the blanks.
3. Deploy the [Hyper-V API](https://github.com/goomens/UvA.VM) and fill in `WEBDB_API_URL` in the .env file.

Usage
-----
1. Fill the database with your users, students, servers, etc.
2. Create an image for your servers and put it in the directory configured as `ImagesFolder` in [App.config](https://github.com/goomens/UvA.VM/blob/master/UvA.VM.ApiHost/App.config).
3. Use the `artisan webdb:*` commands to rule the world.