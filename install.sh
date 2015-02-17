#!/bin/bash
#  /* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4:
#  Codificación: UTF-8
#  +----------------------------------------------------------------------+
#  | Elastix version 2.2.0-29                                             |
#  | http://www.elastix.org                                               |
#  +----------------------------------------------------------------------+
#  | Copyright (c) 2006 Palosanto Solutions S. A.                         |
#  +----------------------------------------------------------------------+
#  | Cdla. Nueva Kennedy Calle E 222 y 9na. Este                          |
#  | Telfs. 2283-268, 2294-440, 2284-356                                  |
#  | Guayaquil - Ecuador                                                  |
#  | http://www.palosanto.com                                             |
#  +----------------------------------------------------------------------+
#  | The contents of this file are subject to the General Public License  |
#  | (GPL) Version 2 (the "License"); you may not use this file except in |
#  | compliance with the License. You may obtain a copy of the License at |
#  | http://www.opensource.org/licenses/gpl-license.php                   |
#  |                                                                      |
#  | Software distributed under the License is distributed on an "AS IS"  |
#  | basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See  |
#  | the License for the specific language governing rights and           |
#  | limitations under the License.                                       |
#  +----------------------------------------------------------------------+
#  | The Original Code is: Elastix Open Source.                           |
#  | The Initial Developer of the Original Code is PaloSanto Solutions    |
#  +----------------------------------------------------------------------+
#  $Id: install.sh,v 1.0 2015-02-17 15:22:13 Federico Pereira fpereira@iperfex.com Exp $ */

GIT_SRC_DIR=/usr/src/elxmtapi
SRC_DIR=/usr/src

APP_ECHO=/bin/echo
APP_GIT=/usr/bin/git
APP_YUM=/usr/bin/yum
APP_MYSQL=/usr/bin/mysql
APP_CHOWN=/bin/chown
APP_CHMOD=/bin/chmod
APP_CP=/bin/cp

$APP_ECHO -e "\n==== Download git ELXMTAPI ===="
if [ ! -d $GIT_SRC_DIR/.git ]
then
        $APP_ECHO -e "\n==== Install tool packages ===="
        $APP_YUM install git -y

        cd $SRC_DIR
        $APP_GIT clone https://github.com/lordbasex/elxmtapi.git

  $APP_CP -fra $GIT_SRC_DIR/elxmtapi /var/www/html
  $APP_CHOWN asterisk:asterisk -R /var/www/html/elxmtapi
  $APP_CHMOD +x /var/www/html/elxmtapi/elxmtapi.php

  . /etc/elastix.conf &> /dev/null
  $APP_MYSQL -uroot -p$mysqlrootpwd elxpbx < /usr/src/elxmtapi/db/elxmtapi.sql
  $APP_ECHO "INSERT INTO api_user (username, md5_password, create_date, last_login, minute_session, enabled) VALUES ('fpereira', md5('iperfex'), now(), NULL, 10, '1');" | $APP_MYSQL -uroot -p$mysqlrootpwd elxpbx

  $APP_ECHO "\n -->Complete installation<--"


else
        $APP_ECHO "\n==== Checking updates ===="
  $APP_GIT pull

        $APP_CP -fra $GIT_SRC_DIR/elxmtapi /var/www/html
        $APP_CHOWN asterisk:asterisk -R /var/www/html/elxmtapi
        $APP_CHMOD +x /var/www/html/elxmtapi/elxmtapi.php

  $APP_ECHO "\n -->Complete update<--"

fi


exit 0
