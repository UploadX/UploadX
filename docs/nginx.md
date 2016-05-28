# Nginx And PHP7-FPM Example Configs
**IMPORTANT CONFIG NOTICES**

* These configs are based on a **default** PHP7.0-FPM and Nginx install on **Ubuntu's 16.04 LTS**
* These configs could change between commits and may **not** be up-to-date
* Only use these configs as an example - they may not work in your enviorment


## Nginx Configs
### Site Configs
* The following site config examples should be put into your `/etc/nginx/site-avaliable` folder and symlinked to `/etc/nginx/sites-enabled/`
* Be sure to replace the following:
    * `server_name your_site;` with the DNS entry for your site
        * Ex `server_name my_pics.com;` if your site was my_pics.conf
    * `root /path/to/your/uploadx/site;` to the location of your UploadX site
        * Ex `root /home/web/uploadx;` If your uploadx install is in the folder uploadx in the home directory of the user web.
* The `client_max_body_size 2m;` allows Nginx to accept uploads of up to 2MB - to allow larger you will need to edit your php.ini settings as well as adjust this value

#### Plain Text
**Notes**

* We strongly encourage all website admins of our software to be using HTTPS. 
* You can get a _free_ SSL certificate with [Let's Encrypt](https://letsencrypt.org/) and use [AcmeTool](https://github.com/hlandau/acme) for automating the process. Thus there's really very little reason to not use SSL/TLS!

##### Nginx Plain-Text Site Config

```nginx
client_max_body_size 2m;
server {
	listen 80;
	server_name your_site;

	root /path/to/your/uploadx/site;

	index index.php;

	include snippets/apps/uploadx.conf;
	include snippets/php/7.0.conf;
}
```

#### SSL/TLS Site Config
**Notes**

* You will need a valid SSL certificate from a Certificate Authority,
* We recommend using [Let's Encrypt](https://letsencrypt.org/)for a free TLS certificate 
    * We strongly recommend using [AcmeTool](https://github.com/hlandau/acme) to automate the certificate issuance and renewal process

##### Nginx Site Config
```nginx
client_max_body_size 2m;
server {
    listen 80;
    server_name your_site;

	location / {
		return 301 https://$server_name$request_uri;
	}
}
server {
    listen 443 ssl;
    server_name your_site;
    
    ssl_certificate /full/path/to/your/ssl/certs/full_cert_chain;
    ssl_certificate_key /full/path/to/your/ssl/certs/private_key;
    ssl_session_timeout 1d;
    ssl_session_cache shared:SSL:50m;
    ssl_session_tickets off;
    
    # modern configuration. tweak to your needs.
    ssl_protocols TLSv1.1 TLSv1.2;
    ssl_ciphers 'ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA256';
    ssl_prefer_server_ciphers on;
    
    # OCSP Stapling
    # fetch OCSP records from URL in ssl_certificate and cache them
    ssl_stapling on;
    ssl_stapling_verify on;
    resolver 8.8.8.8;
    
    include snippets/apps/uploadx.conf;
    include snippets/php/7.0.conf;
}
```

### Nginx UploadX Include
This is the actual config file that handles Nginx's rewriting.

* Create the snippets/apps directory  
`# mkdir -p /etc/nginx/snippets/apps` 
* Save the snippet below as `/etc/nginx/snippets/apps/uploadx.conf`

```nginx
location /lib/ {
	deny all;
}

location ~ "^/admin/([^/]+)?\/?([^/]+)?" {
	try_files $uri $uri/ /index.php&opt=$2;
	include snippets/php/7.0.conf;
}
location ~ "^/install/step/([0-9]{1,2})$" {
	try_files $uri $uri/ /install/index.php?step=$2;
	include snippets/php/7.0.conf;
}

location / {
	index index.php;
	try_files $uri $uri/ =404;
}
location ~ "^/([A-Za-z0-9]{3,10})\.([A-Za-z0-9]{3,4})$" {
	try_files $uri $uri/ /index.php1&action=view;
	include snippets/php/7.0.conf;
}
location ~ "^/([A-Za-z0-9]{4})/?(view)?" {
	try_files $uri $uri/ /index.php1&action=$2;
	include snippets/php/7.0.conf;
}
```

### Nginx PHP Include
This should pretty much work out of the box. Save as `/etc/nginx/snippets/php/7.0.conf`

```nginx
location ~* \.php {
    try_files $uri =404;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
    fastcgi_pass unix:/var/run/php7.0-fpm.sock;
    fastcgi_read_timeout 300;
}
```

## Issues?
#### UploadX cannot write new files or settings!
This is typically caused by your PHP-FPM (and possibly Nginx) user(s) not having the proper permissions you can fix it with one of the following:
* Adjust the group (and maybe user) that the files are executing as in PHP-FPM's pool config for your site 
* Add PHP-FPM's group to your list of groups for your site
#### I'm not sure what I'm doing wrong - HELP?
You can join our IRC channel - #UploadX on FreeNode and ask for help. If you do be sure to do the following:

1. Wait patiently after asking for help with the issue
    * We are not all using Nginx (Currently only one of us is) and we are not always around
2. State your issue clearly and in detail
    * If you are unsure where to look for the cause - describe the issue in detail so that we can help you find the proper log file
    * If you have logs please upload them to a pastebin and include them with a general description of the issue
3. Include any changes you might have made out side of this guide