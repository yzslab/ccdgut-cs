#City College of Dongguan University Class Selector
Written under PHP 7.0 zend thread safe version.

Extension requirement as below:
##curl
Using for HTTP request
##pthreads
Using for multithread
##iconv
Convering from GBK to UTF-8
#Usage
##Configuration
Add your teaching system urls to associative array constant SYSTEM_URLS, name as key, url as value.
##Login function
```php login.php USERNAME PASSWORD [SESSION_ID] [VIEW_STATE]```

cs.php will automatically login to all the teaching system urls and print a session id, please record the session id for each system url.
##Class selector
```php cs.php USERNAME POST_URL SYSTEM_URL_NAME|SYSTEM_URL,POST_FIELDS_FILE_PATH1[,POST_FIELDS_FILE_PATH2 ...] [SYSTEM_URL_NAME|SYSTEM_URL,POST_FIELDS_FILE_PATH1[,POST_FIELDS_FILE_PATH2 ...]]```

The content in POST_FIELDS_FILE_PATHn will be automatically submitted to SYSTEM_URL/(SESSION_ID)/POST_URL, so as to select class.
#Example
Start auto login: 

```screen php7.0-zts login.php 201535010200 password```

Start class selector: 

```screen php7.0-zts cs.php 201535010200 "xf_xsqxxxk.aspx?xh=201535010200&xm=%00%00%00%00%00%00&gnmkdm=N000000" "http://10.20.208.11:8088/,/home/username/11_file1,/home/username/11_file2" "http://10.20.208.12/,/home/username/12_file1,/home/username/12_file2"```