# Suggested Tweaks
## Allow Larger Uploads

####  Common (Apache and Nginx) 
 
|   Software    |     Change Item     | Suggested Value | See |
|:-------------:|:-------------------:|:---------------:|:---:|
| PHP (php.ini) | upload_max_filesize | Max upload size |  1  |
| PHP (php.ini) |    post_max_size    | Max upload size |  1  |

#### Nginx-only

|     Software     |        Change Item        | Suggested Value | See |
|:----------------:|:-------------------------:|:---------------:|:---:|
|  PHP (php.ini)   |    max_execution_time     |       300       |  2  |
| PHP (pool conf)  | request_terminate_timeout |       300       |  2  |
| Nginx (location) |   fast_cgi_read_timeout   |       300       |  3  |
|   Nginx (http)   |   client_max_body_size    | Max upload size |  4  |


## Notes 

1. Allows larger uploads
    * Setting is a formatted value (Eg 100M = 100MB). 
    * You must change **both** to be the **same value**
2. This allows PHP-FPM to take longer to process scripts.
    * Settings are in seconds 
    * Both the **PHP-FPM** and **Nginx** changes **must* be the **same value**
3. This tells Nginx to allow larger uploads.
    * Setting is a formatted value (Eg 100M = 100MB)
    * You should have this set to the **same** as the **php max upload** settings!