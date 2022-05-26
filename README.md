# Project Task

Build an application using JavaScript and/or PHP which has the following features:  

- 3 button components which trigger API GET requests   
- A component that renders the API results in a presentable way

Clicking each button should fetch data using their own API URL (provided below). Once the data is fetched, you will need to store it in a local database.   

If a record has a poster, you should store the poster in its own table and create a relevant relation to the record.  

**You should fetch data on the back-end.**   
**You should only store unique data in the database.**  

- Button 1 URL:  http://www.omdbapi.com/?s=Matrix&apikey=720c3666  
- Button 2 URL:  http://www.omdbapi.com/?s=Matrix%20Reloaded&apikey=720c3666  
- Button 3 URL:  http://www.omdbapi.com/?s=Matrix%20Revolutions&apikey=720c3666  

You can use front-end libraries such as Bootstrap or Material UI.  

You will earn bonus points for testing your application and demonstrating architecture skills.  

# Requirements

1. Docker
2. PHP any version (to run composer)
3. free ports 8081 (for API) and 8080 (for UI)

# Installation

##### 1. In root directory execute
```
php composer.phar install --ignore-platform-reqs
```
##### 2. Go back to root directory and execute
```
docker-compose -f docker/docker-compose.yml --env-file docker/sample.env up --build
```

##### MySQL connection:
- DATABASE = sample
- PASSWORD = secret
- USER = myuser

# Usage

## API

GET `http://127.0.0.1:8081/api/publications?q=Mad+Max`

Use parameter "q" as search parameter

## UI

`http://127.0.0.1:8080`

# Tests
After composer install in step 1, you can run tests:

`./vendor/bin/phpunit tests`

API CODE COVERAGE IS 100%! 
SEE https://take.ms/xFXxu 

# Debuging

To show errors in API edit `docker/sample.env` file variable `APP_SHOW_ERRORS`.