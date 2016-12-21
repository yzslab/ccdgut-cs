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
```php cs.php SYSTEM_URL_NAME|SYSTEM_URL USERNAME POST_URL POST_FIELD_FILE1_PATH [POST_FIELD_FILE2_PATH ...]```
The content in POST_FIELD_FILEn_PATH will be automatically submitted to SYSTEM_URL/(SESSION_ID)/POST_URL, so as to select class.