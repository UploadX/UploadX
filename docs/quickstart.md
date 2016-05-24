# UploadX Quick Start Guide
**NOTICE lease read this guide fully to correctly setup UploadX**
<sup>Last Updated May, 23, 2016</sup>


## Pre-Reqs
|       Software        | Version  |                Notes                 |
|:---------------------:|:--------:|:------------------------------------:|
|         MySQL         |   5.5+   | MySQL Forks should work but untested |
|          PHP          | 5.6, 7.x |                                      |
|   PHP MySQLi Module   |    -     |                                      |
| Compatible Web Server |    -     |      See Compatible Web Servers      |

#### Compatible Web Servers
| Web Server | Versions |
|:----------:|:--------:|
|   Apache   |  2.4.7+  |
|   Nginx    |  1.4.6+  |

## MySQL Setup (SSH)
1. Create a MySQL Database
2. Create a MySQL User for the database
4. Import the MySQL file `uploadx.sql`
    * Ex: ```mysql -u uploadx_user -p uploadx_database < /path/to/uploadx/docs/uploadx.sql```
    * Replace **uploadx_user** with the user you created for this in step **2**
    * Replace **uploadx_database** with the database you created in step **1**
    * Repalce **/path/to/uploadx/docs/** with the full path to your uploadx documentation directory
## Configuration File
1. Copy **lib/config.php.example** to **lib/config.php**
2. Open **lib/config.php**  in your favorite file editor (you can download it via SFTP or FTP if easier)
	* Somethings to Note
		* All configuration is in the config array
		* All MySQL configuration is in a sub array named mysql
3. Configure MySQL (see below for the section)
```
  'mysql' => array(
    'host'     => '127.0.0.1',
    'user'     => 'uploadx',
    'pass'     => 'password',
    'database' => 'uploadx',
  ),
```
	1. Set the MySQL hostname - Optional
		* Example with MySQL Server at 192.168.1.2:
		`'host'     => '192.168.1.2',` 
	2. Set the MySQL user - **RECOMMENDED**
        * If your username was **awesome_uploads**:
        `'user'     => 'awesome_uploads',`
	3. Set the MySQL user's password - **REQUIRED** 
	    * Example if password is **awesomesauce!**:
	    `'pass'     => 'awesomesauce!',`
	4. Set the database - **RECOMMENDED** 
		* Example if you had created the MySQL database **awesome** in the MySQL part above it would be:
        `'database' => 'awesome',`
4. Set your full path to your base upload folder
	* Notes:
		* You **must** include a trailing slash - **/** at the end of the path
		* You **should** put this outside of your public web files
	* Default: 
	`'location' => '/full/path/to/your/images/upload-base-directory/',`
	* Example if you had your files under the user web and the folder for the uploads was named uploads
	`'location' => '/home/web/uploads/',`
