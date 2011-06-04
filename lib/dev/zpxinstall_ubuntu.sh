#!/bin/sh
#
# Ubuntu Linux Installation Script for Zpanel 6.1.0 (Development Enviroment)
# Script written by Bobby Allen (ballen@zpanel.co.uk) 14/05/2011
#
#

# Apache HTTPD configuration file path
apache_config=/etc/apache2/apache2.conf

# ProFTPd configuration file path
proftpd_config=/etc/proftpd/proftpd.conf

clear
echo "#########################################################"
echo "# ZPanel Installation Package for Ubuntu Linux          #"
echo "# --------------------------------------------          #"
echo "# Package maintainer: Bobby Allen (ballen@zpanelcp.com) #"
echo "# Last updated:       04/06/2011                        #"
echo "# Website:            http://www.zpanelcp.com           #"
echo "########################################################"
echo ""
echo "Welcome to the online installer for ZPanel, this will download the required software and install ZPanel."
echo ""
echo "This install script is designed to be used on freshly installed servers or workstations"
echo "due to the nature of the software and amount of system changes it makes we recommend that"
echo "if you want to uninstall ZPanel that you re-install your OS."
echo ""
echo "We also recommend that ZPanel is installed on a dedicated server for security reasons!"
echo ""
echo "Press ENTER to continue with the installation... (or CTRL+C to quit)"
read continue

# Install the required development enviroment packages...
sudo apt-get update
sudo apt-get install apache2 libapache2-mod-php5 libapache2-mod-bw php5 php5-cli php5-common php5-mysql php5-curl php5-gd php-pear php5-imagick php5-imap php5-mcrypt php5-xmlrpc php5-xsl php5-suhosin mysql-server mysql-client subversion zip proftpd webalizer

# Add 'include' to the Apache configuration file..
echo "# Include the ZPanel HTTPD managed configuration file." >> ${apache_config}
echo "Include /etc/zpanel/conf/httpd.conf" >> ${apache_config}

# Add 'include' to the ProFTPd configuration file..
echo "# Include the ZPanel ProFTPd managed configuration file." > ${proftpd_config}
echo "Include /etc/zpanel/conf/proftpd.conf" >> ${proftpd_config}

# Add exception to Sudoers file to enable zsudo execution for restarting Apache etc.
echo "# ZPanel modification to enable automated Apache restarts." >> /etc/sudoers
echo "www-data ALL=NOPASSWD: /etc/zpanel/bin/zsudo" >> /etc/sudoers

# Make the default directories
sudo mkdir /etc/zpanel/
sudo mkdir /var/zpanel/
sudo mkdir /var/zpanel/logs/
sudo mkdir /var/zpanel/backups/
sudo mkdir /var/zpanel/updates/
sudo mkdir /var/zpanel/hostdata/
sudo mkdir /var/zpanel/hostdata/zadmin/
sudo mkdir /var/zpanel/logs/domains/
sudo mkdir /var/zpanel/logs/domains/zadmin/
sudo mkdir /var/zpanel/logs/proftpd/

# Download the contents of the SVN repository..
echo "You may now be asked to accept the SSL certificate for our SVN repository..."
sudo svn co https://svn.zpanelcp.com/svnroot/zpanelcp/trunk /etc/zpanel/

# Set the security on these directories
sudo chown -R www-data /etc/zpanel
sudo chmod -R g+s /etc/zpanel
sudo chown -R www-data /var/zpanel
sudo chmod -R g+s /var/zpanel
sudo chmod -R 777 /etc/zpanel/
sudo chmod -R 777 /var/zpanel/

# Restart ProFTPd and Apache...
sudo /etc/init.d/proftpd restart
sudo /etc/init.d/apache2 restart

echo "Please now enter the root MySQL password so I can import the databases and create the ZPanel DB config file:"
read password

echo "> Importing zpanel_core database.."
mysql -uroot -p${password} < /etc/zpanel/lib/dev/zpanel_core.sql
echo "  ^ Done"
echo "> Importing zpanel_postfix database.."
mysql -uroot -p${password} < /etc/zpanel/lib/dev/zpanel_postfix.sql
echo "  ^ Done!"
echo "> Writing the zpanel database configuration file.."

echo "<?php" >> /etc/zpanel/conf/zcnf.php
echo "" >> /etc/zpanel/conf/zcnf.php
echo "/**" >> /etc/zpanel/conf/zcnf.php
echo " *" >> /etc/zpanel/conf/zcnf.php
echo " * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel." >> /etc/zpanel/conf/zcnf.php
echo " * " >> /etc/zpanel/conf/zcnf.php
echo " * @package ZPanel" >> /etc/zpanel/conf/zcnf.php
echo " * @version $Id$" >> /etc/zpanel/conf/zcnf.php
echo " * @author Bobby Allen - ballen@zpanelcp.com" >> /etc/zpanel/conf/zcnf.php
echo " * @copyright (c) 2008-2011 ZPanel Group - http://www.zpanelcp.com/" >> /etc/zpanel/conf/zcnf.php
echo " * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3" >> /etc/zpanel/conf/zcnf.php
echo " *" >> /etc/zpanel/conf/zcnf.php
echo " * This program (ZPanel) is free software: you can redistribute it and/or modify" >> /etc/zpanel/conf/zcnf.php
echo " * it under the terms of the GNU General Public License as published by" >> /etc/zpanel/conf/zcnf.php
echo " * the Free Software Foundation, either version 3 of the License, or" >> /etc/zpanel/conf/zcnf.php
echo " * (at your option) any later version." >> /etc/zpanel/conf/zcnf.php
echo " *" >> /etc/zpanel/conf/zcnf.php
echo " * This program is distributed in the hope that it will be useful," >> /etc/zpanel/conf/zcnf.php
echo " * but WITHOUT ANY WARRANTY; without even the implied warranty of" >> /etc/zpanel/conf/zcnf.php
echo " * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the" >> /etc/zpanel/conf/zcnf.php
echo " * GNU General Public License for more details." >> /etc/zpanel/conf/zcnf.php
echo " *" >> /etc/zpanel/conf/zcnf.php
echo " * You should have received a copy of the GNU General Public License" >> /etc/zpanel/conf/zcnf.php
echo " * along with this program.  If not, see <http://www.gnu.org/licenses/>." >> /etc/zpanel/conf/zcnf.php
echo " *" >> /etc/zpanel/conf/zcnf.php
echo " */" >> /etc/zpanel/conf/zcnf.php
echo "\$z_db_host = \"localhost\";" >> /etc/zpanel/conf/zcnf.php
echo "\$z_db_name = \"zpanel_core\";" >> /etc/zpanel/conf/zcnf.php
echo "\$z_db_user = \"root\";" >> /etc/zpanel/conf/zcnf.php
echo "\$z_db_pass = \"$password\";" >> /etc/zpanel/conf/zcnf.php
echo "\$zdb = @mysql_pconnect(\$z_db_host, \$z_db_user, \$z_db_pass) or trigger_error('ZPanel Stack Error :: Unable to connect to ZPanel Database Server (' . \$z_db_host . ').');" >> /etc/zpanel/conf/zcnf.php
echo "?>" >> /etc/zpanel/conf/zcnf.php
echo "  ^ Done"


echo ""
echo "ZPanel has now been installed!"
echo " "
echo "   Just a few more steps..."
echo "   ------------------------"
echo " "
echo "   1) Create a new cron job for daemon.php to run hourly ('crontab -e' with the following: '0 * * * * php /etc/zpanel/daemon.php')"
echo " "
echo "   If you need help with the 'final touches' please visit the ZPanel forums here: http://forums.zpanelcp.com."
echo "   Thanks for installing ZPanel! :)"

