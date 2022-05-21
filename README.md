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

# Installation

##### 1. In root directory execute
```
cd src && php composer.phar install
```
or
```
composer install
```
##### 2. Go back to root directory and execute
```
docker-compose -f docker/docker-compose.yml --env-file docker/sample.env up --build
```

# Use

# Tests