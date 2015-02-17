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
#  $Id: uninstall.sh,v 1.0 2015-02-17 15:22:13 Federico Pereira fpereira@iperfex.com Exp $ */

GIT_SRC_DIR=/usr/src/elxmtapi
HTTP_SRC_DIR=/var/www/html/elxmtapi

APP_ECHO=/bin/echo
APP_RM=/bin/rm
APP_MYSQL=/usr/bin/mysql

$APP_RM -fr $HTTP_SRC_DIR
$APP_RM -fr $GIT_SRC_DIR

. /etc/elastix.conf &> /dev/null
$APP_ECHO "DROP TABLE IF EXISTS api_session;" | $APP_MYSQL -uroot -p$mysqlrootpwd elxpbx
$APP_ECHO "DROP TABLE IF EXISTS api_user" | $APP_MYSQL -uroot -p$mysqlrootpwd elxpbx

$APP_ECHO "-->Complete uninstallation<--"

exit 0
