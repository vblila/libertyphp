# LibertyPhp
Fast and extensible micro framework for PHP.

# Version
Beta 0.2.20190313

# Requirements
LibertyPhp requires PHP 7.2 or greater.

# License
LibertyPhp is released under the MIT license.

# Installation

Configure your webserver.

For *Apache*, edit your `.htaccess` file with the following:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

For *Nginx*, add the following to your server declaration:

```
server {
    location / {
        try_files $uri $uri/ /index.php;
    }
}
```
