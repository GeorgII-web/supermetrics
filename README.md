# Supermetrics project

## Performance
- It has a linear execution time depending on the number of posts on cold start and logarithmic on hot start.
- Memory usage is constant, no changes when the number of posts increases.
- The level of memory usage on cold start may be adjusted by urls chunk size (the number of links that requested in parallel, now 5 links).

![Perfomance](data/images/stat.png?raw=true "Perfomance")

![Results](data/images/work.png?raw=true "Results")

![PEST test](data/images/pest.png?raw=true "PEST test")

![PSALM result](data/images/psalm.png?raw=true "PSALM result")

## Features
- NO use of existing frameworks (created NanoFramework just for task)
- NO use of external modules/libraries
- Docker container for php8 & composer
- GitHub [Actions](https://github.com/GeorgII-web/supermetrics/runs/1999544319?check_suite_focus=true) CI\CD
- Composer & PSR autoload
- Curl multi execution, query urls in parallel
- Custom exceptions
- PSR Logs/Cache
- SPL FixedArray  
- DB - file/table for a yielding reading by rows
- Console colored prints to the screen
- Psalm static analysis tool
- PHPUnit + tests

## Program steps

#### Cold start
1. Get token for user & put it to the user cache (1 hour)
2. Generate urls chunks (by 5 links)
3. Send each chunk to the http client
4. For each chunk, get multiple links results in parallel
5. Save each chunk results to the DB in append mode (cache for 1 minute, posts may change)
6. Start reading from DB by row/post
7. Send each post to Statistic calculator that aggregate data

#### Hot start
1. Start reading from DB by row/post
2. Send each post to Statistic calculator that aggregate data

## Clone app
```sh
$ git clone https://github.com/GeorgII-web/supermetrics
```

## Run locally (required php8 & composer)

#### Install
```sh
$ cp config.example.php config.php
$ composer install
```
Change config values.

#### Run
```sh
$ php command statistics
$ php command help
$ php command cache clear
```

## Or run in docker

#### Install docker
```sh
$ docker-compose up -d --build
$ docker exec -it supermetrics_php bash
```
#### Install in the docker bash
```sh
$ cp config.example.php config.php
$ composer install
```

Change config values.

#### Run in the docker bash
```sh
$ php command statistics
$ php command help
$ php command cache clear
```

## Additionally
```sh
$ php ./vendor/bin/psalm
$ php ./vendor/bin/pest
```
