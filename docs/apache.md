# Apache2 & PHP7 Example Configs
**IMPORTANT CONFIG NOTICES**

* These configs are based on a **default** Apache 2.4 and PHP7 install on **Ubuntu Server 16.04 LTS**
* These configs could change between commits and may **not** be up-to-date
* Only use these configs as an example - they may not work in your environment


## Apache Configs
  * Ensure mod_rewrite is enabled via `a2enmod rewrite`

### Site Configs
* The following site config examples should be put into your `/etc/apache2/sites-avaliable` folder and symlinked to `/etc/apache2/sites-enabled/`
* Be sure to replace the following:
    * `ServerName your_site` with the DNS entry for your site
        * Ex `ServerName my_pics.com` if your site was my_pics.com
    * `ServerAlias www.your_site` for it to be available with `www.`
        * Ex `ServerAlias www.my_pics.com`
    * `DocumentRoot /path/to/your/uploadx/site` to the location of your UploadX site
        * Ex `DocumentRoot /home/web/uploadx` If your UploadX install is in the folder `uploadx` in the home directory of the user web.
    * If you'd like to allow uploads larger than 2MB, see [tweaks](https://github.com/UploadX/UploadX/blob/master/docs/tweaks.md)

#### Plain Text
**Notes**

* We strongly encourage all website admins of our software to be using HTTPS.
* You can get a _free_ SSL certificate with [Let's Encrypt](https://letsencrypt.org/) and use [AcmeTool](https://github.com/hlandau/acme) or [CertBot](https://certbot.eff.org/) for automating the process. Thus there's really very little reason to not use SSL/TLS!

##### Apache Plain-Text Site Config

```
<VirtualHost *:80>
  ServerName yoursite.com
  ServerAlias www.yoursite.com

  DocumentRoot /path/to/your/uploadx/site

  <Directory /path/to/your/uploadx/site>
    AllowOverrides All
  </Directory>
</VirtualHost>
```

#### SSL/TLS Site Config
**Notes**

* You will need a valid SSL certificate from a Certificate Authority,
* We recommend using [Let's Encrypt](https://letsencrypt.org/) for a free TLS certificate
    * We strongly recommend using [AcmeTool](https://github.com/hlandau/acme) to automate the certificate issuance and renewal process

##### Apache Site Config
```
<VirtualHost *:80>
  ServerName your_site.com
  ServerAlias www.your_site.com

  DocumentRoot /path/to/your/uploadx/site

  <Directory /path/to/your/uploadx/site>
    AllowOverride All
    Require all granted
    allow from all
  </Directory>
</VirtualHost>

<VirtualHost *:443>
    ServerName your_site.com
    ServerAlias www.your_site

    DocumentRoot /path/to/your/uploadx/site

    <Directory /path/to/your/uploadx/site>
            AllowOverride All
            Require all granted
            allow from all
    </Directory>

    SSLEngine On
    SSLCertificateFile /path/to/your/ssl/certs/full_chain;
    SSLCertificateKeyFile /full/path/to/your/ssl/certs/private_key
    SSLProtocol all -SSLv2 -SSLv3
    SSLCipherSuite ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:DHE-DSS-AES128-GCM-SHA256:kEDH+AESGCM:ECDHE-RSA-AES128-SHA256:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA:ECDHE-ECDSA-AES128-SHA:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA:ECDHE-ECDSA-AES256-SHA:DHE-RSA-AES128-SHA256:DHE-RSA-AES128-SHA:DHE-DSS-AES128-SHA256:DHE-RSA-AES256-SHA256:DHE-DSS-AES256-SHA:DHE-RSA-AES256-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384:AES128-SHA256:AES256-SHA256:AES128-SHA:AES256-SHA:AES:CAMELLIA:DES-CBC3-SHA:!aNULL:!eNULL:!EXPORT:!DES:!RC4:!MD5:!PSK:!aECDH:!EDH-DSS-DES-CBC3-SHA:!EDH-RSA-DES-CBC3-SHA:!KRB5-DES-CBC3-SHA
    SSLHonorCipherOrder on
    SSLCompression off
    SSLOptions +StrictRequire

    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
    ProtocolsHonorOrder On
    Protocols h2 h2c http/1.1
</VirtualHost>
```

## Issues?
#### UploadX cannot write new files or settings!
This is almost always because your permissions aren't set correctly.
* Adjust the group (and maybe user) that the files are executing as.
  * Ex. `chown -R www-data:www-data /path/to/your/uploadx/site`

#### I'm not sure what I'm doing wrong - HELP?
You can join our IRC channel - #UploadX on FreeNode and ask for help. If you do be sure to do the following:

1. Wait patiently after asking for help with the issue
    * We're not always around. It may be a while before we respond.
2. State your issue clearly and in detail
    * If you are unsure where to look for the cause - describe the issue in detail so that we can help you find the proper log file
    * If you have logs please upload them to a pastebin and include them with a general description of the issue
3. Include any changes you might have made beyond this guide
