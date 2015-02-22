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

```bash
php /usr/src/elxmtapi/example/Login_and_Login.php
```
![ScreenShot](https://raw.githubusercontent.com/lordbasex/elxmtapi/master/screenshot/example_1-4.png)

```bash
php /usr/src/elxmtapi/example/CreateOrganization.php
```
![ScreenShot](https://raw.githubusercontent.com/lordbasex/elxmtapi/master/screenshot/example_5.png)

```bash
php /usr/src/elxmtapi/example/ChangeStateOrganization.php

```
![ScreenShot](https://raw.githubusercontent.com/lordbasex/elxmtapi/master/screenshot/example_8.png)

<h2>Code</h2>

| Code  | Method  | Message |
| :------------ |:---------------:| -----:|
| 400      | some wordy text | $1600 |
| 401      | centered        |   $12 |
| 402      | are neat        |    $1 |
