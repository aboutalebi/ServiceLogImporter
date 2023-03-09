## How Can Use
- **Use Laravel 10 for run this project**
- Config DB info in .env for keep logs data, and .env.testing for tests
- For first time run this command and create tables:
   > php artisan migrate
- Run console command and send log file location to this:
   > php artisan app:pars-log-file {FILE_LOCATION}
- - **like this**
   > php artisan app:pars-log-file "d:/logs.txt"
- Query data like this:
   > {LARAVEL_LOCATION}/api/logs/count?serviceName=invoice&statusCode=422&startDate=17/Sep/2022:10:21:53&endDate=17/Sep/2022:10:24:53
- Run tests with this command:
   > php artisan test

## How Did It Work
- I create console command (ParsLogFile)
- This command get log file location from argument
- Then check file exist and not empty
- If file is valid, calculate row count for show progress
- I use LazyCollection for large size file, this method keep only a small part of the file in memory
- I use regex for get info for each log line
- - **I remove "-service" from end of service name in log file for decrease size of column in DB**
- Then insert data to DB
- With ServiceLogController and getCount method can query in DB and get count of results
with this route "api/logs/count" and GET request
- With "Request", first check what kind of data for filter is selected
- With "when" check if parameter is fill, then query this

## Database Structure
Table "service_logs"

| Name         | Type | Len  |
|--------------|---|------|
| service_name | string | 128  |  
| log_at       | timestamp |
| request_type | string | 16   |    
| query_string | string | 1024 |
|  status_code | unsignedSmallInteger |

## Improvements
- Validate log data before insert to DB
- Can use Interface for support other format of logs
- Can get Log time format from console command
- Implement tests for check and validate controller result
- Implement test for check inserted data is valid
- Can use and connect to s3 for get log files
- In this version if process stop with user, The data so far will be imported to DB
- - I think with this size of data, can not use transaction but can keep last
record ID before starting insert new data and if user stop this, remove all
data after this ID
- Can add some console argument for clear old data before insert new
- Can keep log of inserted data or flag column to be able to find with record 
insert from which file
