# Skeleton API

**This project is still under development and is not ready for use in production.**

This project helps to cut down on development time for PHP based APIs. Just by cloning the project you can create, update,
find and delete users. The application supports [JWT](http://jwt.io/)-based authentication, CORS for cross-site requests, and resource based 
urls. Logging is also set up already and application logs will be stored in `logs/` directory. 

## Installation
*It is assumed that composer is installed and available globally, if you need to install composer follow the steps [here](https://getcomposer.org/download/)*

Run this command from the directory in which you want to develop your new API.

    composer create-project gurpreetatwal/skeleton-api [my-app-name] -s dev

Replace `[my-app-name]` with the desired directory name for your new API. You'll want to:

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writable.
* Create your `environment.ini` by copying the example and replacing the default values.

To run your API locally, you can use PHP's built-in sever by running the following command:

     php -S localhost:8888 -t public/ public/index.php

That's it! Now go build something cool.

Originally a fork of [Slim-Skeleton](https://github.com/slimphp/Slim-Skeleton)