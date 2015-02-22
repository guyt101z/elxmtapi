# elxmtapi - Elastix MT API

![ScreenShot](https://raw.githubusercontent.com/lordbasex/elxmtapi/master/logo/elxmtapi.png)

Lord BaseX (c) 2014-2015
 Federico Pereira <lord.basex@gmail.com>

This code is distributed under the GNU LGPL v3.0 license.

## Introduction


## Installation

Install the git package and follow the instructions.

```bash
/usr/src
wget https://raw.githubusercontent.com/lordbasex/elxmtapi/master/install.sh --no-check-certificate -O /usr/src/install.sh
chmod +x install.sh
./install.sh
```

OR


```bash
cd /usr/src/
yum -y install git
git clone https://github.com/lordbasex/elxmtapi.git
cp -fra /usr/src/elxmtapi/elxmtapi /var/www/html
chown asterisk:asterisk -R /var/www/html/elxmtapi
chmod +x /var/www/html/elxmtapi/elxmtapi.php
. /etc/elastix.conf &> /dev/null
mysql -uroot -p$mysqlrootpwd elxpbx < /usr/src/elxmtapi/db/elxmtapi.sql
echo "INSERT INTO api_user (username, md5_password, create_date, last_login, minute_session, enabled) VALUES ('fpereira', md5('iperfex'), now(), NULL, 10, '1');" | mysql -uroot -p$mysqlrootpwd elxpbx
```

## Method

<h2>API</h2>

<ol start="1">
  <li>Login</li>
  <li>UserDisable</li>
  <li>UserEnable</li>
  <li>Logout</li>
</ol>

<h2>Organization</h2>

<ol start="5">
  <li>CreateOrganization</li>
  <li>DeleteOrganization</li>
  <li>ShowOrganization</li>
  <li>ChangeStateOrganization</li>
</ol>

<h2>Example</h2>

<h4>1- Login & 4- Logout</h4>
```bash
php /usr/src/elxmtapi/example/Login_and_Login.php
```
![ScreenShot](https://raw.githubusercontent.com/lordbasex/elxmtapi/master/screenshot/example_1-4.png)

<h4>5- CreateOrganization</h4>
```bash
php /usr/src/elxmtapi/example/CreateOrganization.php
```
![ScreenShot](https://raw.githubusercontent.com/lordbasex/elxmtapi/master/screenshot/example_5.png)

<h4>6- Logout</h4>
```bash
php /usr/src/elxmtapi/example/DeleteOrganization.php
```
![ScreenShot](https://raw.githubusercontent.com/lordbasex/elxmtapi/master/screenshot/example_6.png)

<h4>8- ChangeStateOrganization</h4>
```bash
php /usr/src/elxmtapi/example/ChangeStateOrganization.php

```
![ScreenShot](https://raw.githubusercontent.com/lordbasex/elxmtapi/master/screenshot/example_8.png)

<h2> Message Code</h2>

<h3>Return</h3>

| Code  | Method  | Message |
| :------------ |:---------------:| :-----: |
| 400      | Login | Authenticated user.|
| 401      | UserDisable| User has been disabled.|
| 402      | UserEnable | User has been enabled.|
| 403      | Logout | Session completed successfully.|
| 404      | CreateOrganization | Creation organization completed successfully.|
| 405      | ChangeStateOrganization| Change State 'suspend/unsuspend/terminate' completed successfully.|
| 406      | DeleteOrganization | Delete Organization completed successfully.|
| 407      |  - | - |
| 408      |  - | - |
| 409      |  - | - |
| 410      |  - | - |

<h3>Return Error</h3>

| Code  | Method  | Message |
| :------------ |:---------------:| :-----: |
| 500      | Login | Unregistered user.|
| 501      | Login | User not authenticated.|
| 502      |  -  |  -  |
| 503      | Logout | It was not possible to conclude session.|
| 504      | CreateOrganization | Required Field: name, domain, country, city, address, country_code, area_code, quota, email_contact, max_num_user, max_num_exten, max_num_queues, admin_password |
| 505      | CreateOrganization | Already exist other organization with the same domain.|
| 506      | CreateOrganization | Asterisk can't be reloaded.|
| 507      | ChangeStateOrganization | Required Field: Domain incorrect,Invalid Organization State|
| 508      | DeleteOrganization | Required Field: Domain incorrect|
| 509      | DeleteOrganization | Domain not found. |
| 510      | - | - |

