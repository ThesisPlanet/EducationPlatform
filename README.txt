Education Platform - By Thesis Planet, LLC.
==============

Copyright 2009-2013 © Thesis Planet, LLC. All rights reserved. See License.MD for licensing details.

* Every organization should be able to deliver an excellent educational environment to its employees and volunteers.
At this point in time, solutions that provide a decent digital learning environment are extremely expensive, or overly complex to implement and thereby unavailable to most small-to-medium sized businesses and non-profits as a practical way of delivering education within an organization.


Technical Goals:
--------
* Provide a platform where individuals and organizations can offer training material on a variety of topics to authorized users (typically employees/customers).
* Create a superior learning experience through writing high-quality code deployed on well-architected infrastructure.

More holistic goals:
* Create a universal training system that can be used by individuals, businesses, and non-profits to enhance individual productivity by creating a more collaborative learning environment.
* Increase the efficiency by which training is delivered.
* Increase the ease by which people can collaborate on learning
* the system should be secure and support high-security, high-privacy requirements.

Infrastructure notes:
--------
Virtualized RHEL/Centos x64 OS.
Configurations/System consistency managed via Puppet (on Fully Managed instances of Thesis Planet's Education Platform).
Application packaged as an RPM file and installed via YUM (based on puppet system profile).

Website: http://www.thesisplanet.com


Note from Jack Peterson - When I first started working on Education Platform, my goal was to help improve how education was delivered at companies and in the non-profit sector. 
During the last three years, the online education sector has seen a number of excellent products pop up that are designed to provide global education that may be useful to a majority of the population; however, those applications are not being made available for the benefit of anyone other than the investors/owners of the software. 

Education Platform is a little different in that each instance is designed to be used on-site for a single organization or within a business (instead of a behemoth platform for everyone to use for the sole benefit of the owners of the platform). 

With no further adieu, I would like to invite you (and anyone else you know) to participate in improving how Education occurs for your organization, your school, and everyone else by asking you to use Education Platform. 
If you are a developer, you are needed! If you can read and write, you are sorely needed to help create documentation. If you are more of a sys-admin type of person, you too are needed to help file bugs. 
If you are just someone who wants to use the software, you too are needed to help identify new features that you would like to see.
This article will be focused on how to install Education Platform and get started. Later articles will be focused on how to contribute back!



WARNINNG: From here on out, this "README" becomes rather technical.
Perform a clean CentOS 6.x x86_64 installation onto a server of your choice.
Server specifications: At this point in time, 1 GB of RAM should be sufficient. I typically give my servers 16GB of hard drive space and one NIC. Education Platform stores a few files during its operations (e.g., a user uploads a video). Those files are then pushed up to Amazon Web Services Simple Storage Service and then subsequently deleted from the server.

Video of the below steps can be found at http://www.youtube.com/watch?v=VB0K8zH7TmE (~ 25 minutes).


Install system updates (I started with CentOS 6.4).
$ yum upgrade -y
Install EPEL
$ wget http://dl.fedoraproject.org/pub/epel/6/i386/epel-release-6-8.noarch.rpm
$ rpm -ivh epel-release-6-8.noarch.rpm
$ rm epel-release-6-8.noarch.rpm
Install Remi Repository (More recent PHP and GearmanD support ).
$ cat >/etc/yum.repos.d/remi.repo <<EOF
[remi]
name=remi
mirrorlist=http://rpms.famillecollet.com/enterprise/\$releasever/remi/mirror
enabled=1
gpgcheck=1
gpgkey=file:///etc/pki/rpm-gpg/RPM-GPG-KEY-remi
exclude=s3cmd
EOF

$ cat >/etc/pki/rpm-gpg/RPM-GPG-KEY-remi <<EOF
-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: GnuPG v1.4.7 (GNU/Linux)

mQGiBEJny1wRBACRnbQgZ6qLmJSuGvi/EwrRL6aW610BbdpLQRL3dnwy5wI5t9T3
/JEiEJ7GTvAwfiisEHifMfk2sRlWRf2EDQFttHyrrYXfY5L6UAF2IxixK5FL7PWA
/2a7tkw1IbCbt4IGG0aZJ6/xgQejrOLi4ewniqWuXCc+tLuWBZrGpE2QfwCggZ+L
0e6KPTHMP97T4xV81e3Ba5MD/3NwOQh0pVvZlW66Em8IJnBgM+eQh7pl4xq7nVOh
dEMJwVU0wDRKkXqQVghOxALOSAMapj5mDppEDzGLZHZNSRcvGEs2iPwo9vmY+Qhp
AyEBzE4blNR8pwPtAwL0W3cBKUx7ZhqmHr2FbNGYNO/hP4tO2ochCn5CxSwAfN1B
Qs5pBACOkTZMNC7CLsSUT5P4+64t04x/STlAFczEBcJBLF1T16oItDITJmAsPxbY
iee6JRfXmZKqmDP04fRdboWMcRjfDfCciSdIeGqP7vMcO25bDZB6x6++fOcmQpyD
1Fag3ZUq2yojgXWqVrgFHs/HB3QE7UQkykNp1fjQGbKK+5mWTrQkUmVtaSBDb2xs
ZXQgPFJQTVNARmFtaWxsZUNvbGxldC5jb20+iGAEExECACAFAkZ+MYoCGwMGCwkI
BwMCBBUCCAMEFgIDAQIeAQIXgAAKCRAATm9HAPl/Vv/UAJ9EL8ioMTsz/2EPbNuQ
MP5Xx/qPLACeK5rk2hb8VFubnEsbVxnxfxatGZ25AQ0EQmfLXRAEANwGvY+mIZzj
C1L5Nm2LbSGZNTN3NMbPFoqlMfmym8XFDXbdqjAHutGYEZH/PxRI6GC8YW5YK4E0
HoBAH0b0F97JQEkKquahCakj0P5mGuH6Q8gDOfi6pHimnsSAGf+D+6ZwAn8bHnAa
o+HVmEITYi6s+Csrs+saYUcjhu9zhyBfAAMFA/9Rmfj9/URdHfD1u0RXuvFCaeOw
CYfH2/nvkx+bAcSIcbVm+tShA66ybdZ/gNnkFQKyGD9O8unSXqiELGcP8pcHTHsv
JzdD1k8DhdFNhux/WPRwbo/es6QcpIPa2JPjBCzfOTn9GXVdT4pn5tLG2gHayudK
8Sj1OI2vqGLMQzhxw4hJBBgRAgAJBQJCZ8tdAhsMAAoJEABOb0cA+X9WcSAAn11i
gC5ns/82kSprzBOU0BNwUeXZAJ0cvNmY7rvbyiJydyLsSxh/la6HKw==
=6Rbg
-----END PGP PUBLIC KEY BLOCK-----
EOF

Install PHP-FPM, NGINX, MySQL, and Supervisor.
PHP-FPM
$ yum install php php-fpm php-mbstring php-cli php-gd php-devel php-mysqlnd php-mcrypt php-soap php-pecl-gearman php-xml php-pdo php-pecl-mysqlnd-ms php-pecl-zendopcache
	NGINX (you could use apache if you prefer)
$ yum install nginx
	MySQL Client and Server
$ yum install mysql mysql-server
Next, we need to start up the MySQL server and ensure that we can connect to it.
$ /etc/init.d/mysqld start
It'll complain about the username/password. Go ahead and reset it at your discretion.
Next we need to add the database and user to mysql.
$ mysql
?	create database dep;
o	Query OK 1 row affected (duration)
?	create USER 'dep'@'localhost' IDENTIFIED BY 'dep';
o	Query OK 0 rows affected (duration)
?	GRANT ALL PRIVILEGES ON dep.* TO 'dep'@'localhost';
o	Query OK 0 rows affected (duration)
?	Flush privileges;
?	Quit;
Let's validate that we have connectivity into the database with our new user.
$ mysql -u dep -p
?	Show databases;
o	dep should be listed in there.
?	Use dep;
o	database changed
?	quit;
Supervisor (ensures that the job queue is running).
$ yum install supervisor
Next we need to install a few PHP dependencies into the appropriate directory - Doctrine ORM and Zend Framework 1.x
$ curl -sS https://getcomposer.org/installer | php
$ mv composer.phar /usr/local/bin/composer
$ composer --stability=dev --dev create-project zendframework/zendframework1 /usr/share/composer/Zend 1.12.3
$ composer --stability=stable --no-dev create-project doctrine/ORM /usr/share/composer/DoctrineORM 2.3.3
Install gearman (Gearman provides the infrastructure to run jobs in the background which gives users a better experience when they upload content because that task does not wait for PHP to perform upload to AWS, wait for Zencoder to Transcode, etc).
$ yum install gearmand
Add the 'web' user.
$ useradd web;
$ passwd web
-	Pick a password.
OK, now we need to fix ownership of various directories and files to the web user.
$ chown -R web /var/lib/php/session
$ chown -R web /usr/share/composer
Replace some lines in /etc/php.ini to match these:
memory_limit = 1024M
include_path = ".:/usr/share/pear:/usr/share/composer/Zend/library";
post_max_size = 2048M
file_uploads = On
upload_max_filesize = 2048M
Add the Thesis Planet RPM repository.

$ cat >/etc/yum.repos.d/thesisplanet.repo <<EOF
[thesisplanet]
name=Thesis Planet Repository
baseurl=http://rpm.thesisplanet.com
enabled=1
gpgcheck=1
gpgkey=file:///etc/pki/rpm-gpg/RPM-GPG-KEY-thesisplanet
failovermethod=priority
EOF

Add the Thesis Planet GPG Key
cat >/etc/pki/rpm-gpg/RPM-GPG-KEY-thesisplanet <<EOF
-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: GnuPG v2.0.14 (GNU/Linux)

mQENBFEVeW8BCADfaczUS9wjnCpED/YLR5ROHCoBqF7f4y78fMAL2k+LYOtblLow
VsTobH1pc7D3Ng/Uoq32Byx9KLw7YPQh8v02wbHhseOySpvsbOd03LvwHtIETZU2
qosmzzE1d0PeLhSrEflEOHJwI7fuchIvjzMpTKFu2ELnDbPPkwXuWZO7juHVvEXR
iGHFMcYknmRotmXxeGmZ8WlUlKn9sihobXQuKAROYY+C5Ihv/3mWXiLh545EibhC
ArguxtTR3MymYroLbjkcxWexiZUl8BRkV0EVEZY53OKA8F69PbfrvIkLja2pDhWE
lkhczbe02ILdKcE7dj+jk52GiO4kt3ZUl5oHABEBAAG0LlRoZXNpcyBQbGFuZXQg
PGphY2sucGV0ZXJzb25AdGhlc2lzcGxhbmV0LmNvbT6JATgEEwECACIFAlEVeW8C
GwMGCwkIBwMCBhUIAgkKCwQWAgMBAh4BAheAAAoJEEN5Z0m08riSUMwIALYGWpcJ
WryDtHT26XEn9Hmth4+VeUY62D/zGpRpekZsTUrP8yRkkLIfkPrUJW6JG8ZqfvXb
RLR+cZNMOcVlidiIGwViayG/zss1P/2/OsEofsqmDmyB0KlDYs/Alo4jl5D5kdBz
3R/Ic5trN9PQR6l9csALwganmSVALrPvZXB7wo1uAXynfJKnrINp9Ma4iM4Ko+8R
//VHm8oXNZcPqidaiYzRQP7WG2/Jb+66EpPr9ecRxmHKAcvjaQlm2Dgg2P3Qj5KQ
Mo1+SXQYog7teGw/9GcXotD+wVgHrYkyROqDSKLTlhbrEam7Gk7KF0pWikBa2rNx
0e3FKk9N49U5ur65AQ0EURV5bwEIAOoEPPAY7fGPo0aUX/cnOKwA6NshiswV1Ba6
nIxzq2ukr4mM1XtG7H9DwaJ1Q4y1Xg3kZz/YyABmAYh/BFZPUKgu551uVSiurdrM
tauSEaalVGH99gL4SVp9k1wwmIcjc+Nz0pxAY6/aRc0fQf8QaeO/nvRRoKypwGC+
sc83jp9qfZS0RsQkfyRkt/A4uf5TdkYbEkfRIyHazgRynxSGEelofaNAkPR9siR/
SDzEaq+hXISsuczgOjEXK5+KrDEmVUUCnltfrW8oXKdJuhncw1lelA+dp17fPzCM
hkSXlninRd++dcnAoBm5zSllKtxXHtBx4Ac79QGZvugtkTGmQH8AEQEAAYkBHwQY
AQIACQUCURV5bwIbDAAKCRBDeWdJtPK4kvQaB/4l0csU5Ma3QqtJZy/3p++/u5LD
M09HZSkG2Z0xn7Ee519j8Xl0yggKiomHBhv6PYUOGCVd6OP+j8Au78F7ov69X8ph
+HtFme61ISKghkTS8Uiw3G2wtOGphegwLmf9kXtrVLZxH8+ogsgKY2JVDDFgFsGt
VYhoCUsguS64Uk6KAaSB5B71VFFeqoNhldiVTAxVFvmNon6CLIqEVqBEZeaPDHv7
Zu+w8ijU3K0MJX1fvWZiwbDesevSYa3AWKrZWGIURjHvdjA4YmEWVL3TeSEsXl85
KYsjX5Mem1EnFnkb76WbLr/mSGOBqeTHCFwn4qA2D5+Xm8mEzVFDakeEfL9P
=TJpu
-----END PGP PUBLIC KEY BLOCK-----
EOF

Time to install Education Platform!
$ yum install TP-DEP
Assuming no errors were generated the database has been created and the php files were placed in /usr/share/TP-DEP.
Time to configure the NGINX web-server
$ mkdir -p /etc/nginx/sites-enabled
$ mkdir -p /etc/nginx/sites-available
$ cat >/etc/nginx/nginx.conf <<EOF
user web;
worker_processes  1;
error_log  /var/log/nginx/error.log;
pid        /var/run/nginx.pid;
events {
    worker_connections  1024;
    # multi_accept on;
}
http {
    include       /etc/nginx/mime.types;
    access_log  /var/log/nginx/access.log;
    sendfile        on;
    keepalive_timeout  65;
    tcp_nodelay        on;
    gzip  on;
    gzip_disable "MSIE [1-6]\.(?!.*SV1)";
    include /etc/nginx/sites-enabled/*;
    server_tokens off;
    client_max_body_size 2G;
}
EOF

$ cat >/etc/nginx/sites-available/dep <<EOF
server {
        listen 80;

        server_name  localhost tpseadepimvl005 tpseadepimvl005.prod.thesisplanet.com ;

        access_log  /var/log/nginx/dep_access.log;
        error_log /var/log/nginx/error.log;

        root /usr/share/TP-DEP/src/public;


        location / {
                index   index.php;

        try_files \$uri \$uri/ /index.php?\$args;
        autoindex off;
        }
                location ~ \.php$ {


                fastcgi_pass unix:/var/run/php-fpm.sock;

                fastcgi_param APPLICATION_ENV production;



                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
                include /etc/nginx/includes/fastcgi_params.inc;


         fastcgi_param  HTTPS off;



        }

        location ~ /\.ht {
                deny  all;
        }


}
EOF

$ ln -s /etc/nginx/sites-available/dep /etc/nginx/sites-enabled/dep

$ mkdir -p /etc/nginx/includes
$ cat >/etc/nginx/includes/fastcgi_params.inc <<EOF
fastcgi_param  QUERY_STRING       \$query_string;
fastcgi_param  REQUEST_METHOD     \$request_method;
fastcgi_param  CONTENT_TYPE       \$content_type;
fastcgi_param  CONTENT_LENGTH     \$content_length;
fastcgi_param  SCRIPT_NAME        \$fastcgi_script_name;
fastcgi_param  REQUEST_URI        \$request_uri;
fastcgi_param  DOCUMENT_URI       \$document_uri;
fastcgi_param  DOCUMENT_ROOT      \$document_root;
fastcgi_param  SERVER_PROTOCOL    \$server_protocol;
fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
fastcgi_param  SERVER_SOFTWARE    nginx/\$nginx_version;
fastcgi_param  REMOTE_ADDR        \$remote_addr;
fastcgi_param  REMOTE_PORT        \$remote_port;
fastcgi_param  SERVER_ADDR        \$server_addr;
fastcgi_param  SERVER_PORT        \$server_port;
fastcgi_param  SERVER_NAME        \$server_name;

# PHP only, required if PHP was built with --enable-force-cgi-redirect
fastcgi_param  REDIRECT_STATUS    200;
EOF

$ cat >/etc/nginx/sites-available/dep_ssl <<EOF
server {
listen 443 ssl;
    server_name  tpseadepimvl001.prod.thesisplanet.com;
    access_log  /var/log/nginx/dep_ssl_access.log;
    root /usr/share/TP-DEP/src/public;
        ssl  on;
    ssl_certificate  /etc/nginx/ssl/dep_ssl.pem;
        ssl_certificate_key  /etc/nginx/ssl/dep_ssl.key;
        ssl_session_timeout  5m;
        ssl_protocols  SSLv2 SSLv3 TLSv1;
        ssl_ciphers  ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP;
        ssl_prefer_server_ciphers   on;
        location / {
                index   index.php;

        try_files \$uri \$uri/ /index.php?\$args;
        autoindex off;
        }
                location ~ \.php$ {


                fastcgi_pass unix:/var/run/php-fpm.sock;

                fastcgi_param APPLICATION_ENV production;



                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
                include /etc/nginx/includes/fastcgi_params.inc;


         fastcgi_param  HTTPS on;



        }

        location ~ /\.ht {
                deny  all;
        }


}
EOF

$ ln -s /etc/nginx/sites-available/dep_ssl /etc/nginx/sites-enabled/dep_ssl


Generate an SSL certificate for you server (or use your own).

$ mkdir -p /etc/nginx/ssl

$ /usr/bin/openssl req -new -inform PEM -x509 -nodes -days 999 -subj \
        '/C=ZZ/ST=AutoSign/O=AutoSign/localityName=AutoSign/commonName=localhost/organizationalUnitName=AutoSign/emailAddress=AutoSign/' \
        -newkey rsa:2048 -out /etc/nginx/ssl/dep_ssl.pem -keyout /etc/nginx/ssl/dep_ssl.key

Modify /etc/php-fpm.d/www.conf (What NGINX uses to talk to php)

listen = /var/run/php-fpm.sock
;listen.allowed_clients = 127.0.0.1
user = web
group = web


SupervisorD is used to ensure that /usr/share/TP-DEP/src/library/Gearman/Worker.php is running. It also ensure that if it crashes that the worker gets restarted. The worker takes long-running jobs and processes them in the background.


$ cat >/etc/supervisord.conf <<EOF
[unix_http_server]
file=/tmp/supervisor.sock   ; (the path to the socket file)

[supervisord]
logfile=/var/log/supervisor/supervisord.log                      ; (main log file;default $CWD/supervisord.log)
logfile_maxbytes=500MB    ; (max main logfile bytes b4 rotation;default 50MB)
logfile_backups=10      ; (num of main logfile rotation backups;default 10)
loglevel=info                    ; (log level;default info; others: debug,warn,trace)
pidfile=/var/run/supervisord.pid            ; (supervisord pidfile;default supervisord.pid)
nodaemon=false                              ; (start in foreground if true;default false)
minfds=1024                        ; (min. avail startup file descriptors;default 1024)
minprocs=200                    ; (min. avail process descriptors;default 200)
childlogdir=/var/log/supervisor              ; ('AUTO' child log dir, default $TEMP)
nocleanup=false
umask=022
user=root
[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

[supervisorctl]
serverurl=unix:///tmp/supervisor.sock ; use a unix:// URL  for a unix socket

[include]
files = /etc/supervisord.d/*.ini
EOF

IMPORTANT NOTE: If supervisor is older than 3.x (e.g, 2.x) the /etc/supervisord.d/ThesisPlanet.ini contents must be appended to the bottom of /etc/supervisord.conf. Otherwise when audio/video/files are uploaded to Education Platform, they will not be processed.

$ yum list installed | grep supervisor

supervisor.noarch    2.1-8.el6          @epel

$ cat /etc/supervisord.d/ThesisPlanet.ini >> /etc/supervisord.conf


Configure the firewall on the server to allow port 80, 443, and port 22 (ssh). Everything else will be dropped.

$ cat >/etc/sysconfig/iptables <<EOF
*filter
:INPUT ACCEPT [0:0]
:FORWARD ACCEPT [0:0]
:OUTPUT ACCEPT [0:0]
-A INPUT -p icmp -m comment --comment "000 accept all icmp" -j ACCEPT
-A INPUT -i lo -m comment --comment "001 accept all to lo interface" -j ACCEPT
-A INPUT -m comment --comment "002 accept related established rules" -m state --state RELATED,ESTABLISHED -j ACCEPT
-A INPUT -p tcp -m multiport --ports 22 -m comment --comment "003 accept SSH" -j ACCEPT
-A INPUT -p tcp -m multiport --ports 80,443 -m comment --comment "100 allow TCP HTTPS/HTTPS access" -j ACCEPT
-A INPUT -m comment --comment "999 drop all" -j DROP
COMMIT
EOF

Configure the services to start up when the server boots up!
$ /sbin/chkconfig nginx on
$ /sbin/chkconfig php-fpm on
$ /sbin/chkconfig mysqld on
$ /sbin/chkconfig supervisord on
$ /sbin/chkconfig gearmand on

Let's restart the server just to ensure that everything started properly ?
Navigate over to http://YOUR_IP_ADDRESS_OR_DOMAIN_NAME.

If everything worked out then you should have a working Education Platform server. 

