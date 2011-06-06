#!/bin/sh
#
# CentOS Linux Installation Script for Zpanel 6.1.0 (Development Enviroment)
# Script written by Bobby Allen (ballen@zpanel.co.uk) 14/05/2011
#
#

# Apache HTTPD configuration file path
apache_config=/etc/httpd/conf/httpd.conf

# ProFTPd configuration file path
proftpd_config=/etc/proftpd.conf

clear
echo "#########################################################"
echo "# ZPanel Installation Package for CentOS Linux          #"
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
sudo yum update
sudo yum install httpd php53 php53-devel php53-gd php53-mbstring php53-imap php53-mysql php53-xml php53-xmlrpc curl curl-devel perl-libwww-perl libxml2 libxml2-devel mysql-server subversion zip webalizer gcc gcc-c++ httpd-devel.i386 postfix dovecot system-switch-mail
#sudo chkconfig --levels 235 sendmail off; /etc/init.d/sendmail stop; yum -y remove sendmail
sudo yum remove vsftpd

# We have to install ProFTPd Manually as CentOS does not have a package for it..
cd /tmp/
wget http://forums.zpanelcp.com/pkgs/source/proftpd-1.3.3e.tar.gz
tar xvfz proftpd-1.3.3e.tar.gz
cd proftpd-1.3.3e/
./configure --sysconfdir=/etc
make
make install
cd ..
rm -fr proftpd-1.3.3e*
ln -s /usr/local/sbin/proftpd /usr/sbin/proftpd
cd ~
wget http://forums.zpanelcp.com/pkgs/scripts/proftpd_centos55.txt
mv proftpd_centos55.txt proftpd
mv proftpd /etc/init.d/proftpd
chmod 755 /etc/init.d/proftpd

# We have to install Mod_BW Manually as CentOS does not have a package for it..
cd /tmp/
wget http://ivn.cl/files/source/mod_bw-0.92.tgz
tar -zxvf mod_bw-0.92.tgz
apxs -i -a -c mod_bw.c
rm -fr mod_bw*
cd ~

# We have to install suhosin Manually as CentOS does not have a package for it...
cd /tmp/
wget http://download.suhosin.org/suhosin-0.9.29.tgz
tar xvfz suhosin-0.9.29.tgz
cd suhosin-0.9.29
phpize
./configure
make
make install
touch /etc/php.d/suhosin.ini
chmod 755 /etc/php.d/suhosin.ini
echo "extension=suhosin.so" > /etc/php.d/suhosin.ini
cd ..
rm -fr suhosin-0.9.29*
cd ~


# Add 'include' to the Apache configuration file..
echo "# Include the ZPanel HTTPD managed configuration file." >> ${apache_config}
echo "Include /etc/zpanel/conf/httpd.conf" >> ${apache_config}

# Add 'include' to the ProFTPd configuration file..
echo "# Include the ZPanel ProFTPd managed configuration file." > ${proftpd_config}
echo "Include /etc/zpanel/conf/proftpd.conf" >> ${proftpd_config}

# Add exception to Sudoers file to enable zsudo execution for restarting Apache etc.
echo "# ZPanel modification to enable automated Apache restarts." >> /etc/sudoers
echo "apache ALL=NOPASSWD: /etc/zpanel/bin/zsudo" >> /etc/sudoers

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
sudo svn co https://zpanelcp.svn.sourceforge.net/svnroot/zpanelcp/trunk /etc/zpanel/

# Set the security on these directories
sudo chown -R apache /etc/zpanel
sudo chmod -R g+s /etc/zpanel
sudo chown -R apache /var/zpanel
sudo chmod -R g+s /var/zpanel
sudo chmod -R 777 /etc/zpanel/
sudo chmod -R 777 /var/zpanel/


# Add services to be started
sudo chkconfig --levels 235 httpd on
sudo chkconfig --levels 235 proftpd on
sudo chkconfig --levels 235 mysqld on
service httpd start
service mysqld start
service proftpd start

# Now we run the MySQL secure script (to enable the user to set a MySQL root password etc.)
/usr/bin/mysql_secure_installation

# Add a cron task to run deamon every 30 mins...
echo "0,30 * * * * php /etc/zpanel/daemon.php" >> /etc/crontab
# Set permissions so Apache can create cronjobs!
sudo chmod 777 /etc/crontab

clear
echo "Will now attempt to create and insert the ZPanel core database into MySQL, please enter the MySQL root password when asked..."
echo "Enter MySQL root password:"
read password
mysql -uroot -p${password} < /etc/zpanel/lib/dev/zpanel_core.sql
echo "Will now attempt to create and insert the ZPanel postfix database into MySQL, please enter the MySQL root password again when asked..."
mysql -uroot -p${password} < /etc/zpanel/lib/dev/zpanel_postfix.sql

echo "<?php" > /etc/zpanel/conf/zcnf.php
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
echo "\$z_db_pass = \"${password}\";" >> /etc/zpanel/conf/zcnf.php
echo "\$zdb = @mysql_pconnect(\$z_db_host, \$z_db_user, \$z_db_pass) or trigger_error('ZPanel Stack Error :: Unable to connect to ZPanel Database Server (' . \$z_db_host . ').');" >> /etc/zpanel/conf/zcnf.php
echo "?>" >> /etc/zpanel/conf/zcnf.php
echo "  ^ Done"

# Setup the default virtual host for the control panel
clear
echo "ENTER THE DOMAIN/SUBDOMAIN THAT WILL HOST ZPANEL EG. 'CONTROL.YOURDOMAIN.COM'"
echo " "
read domain
echo "# ZPanel Apache Master VHOST file." > /etc/zpanel/conf/httpd-vhosts.conf
echo "# Written by Bobby Allen, 15/05/2011" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "#" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "# DO NOT EDIT THIS FILE MANUALLY - USE ZPANEL TO ADD AND REMOVE VHOSTS!" >> /etc/zpanel/conf/httpd-vhosts.conf
echo " " >> /etc/zpanel/conf/httpd-vhosts.conf
echo "NameVirtualHost *:80" >> /etc/zpanel/conf/httpd-vhosts.conf
echo " " >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	# Configuration for ZPanel control panel." >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	<VirtualHost *:80>" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	ServerAdmin zadmin@${domain}" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "    	DocumentRoot \"/etc/zpanel\"" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "    	ServerName ${domain}" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "    	ServerAlias *.${domain}" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	AddType application/x-httpd-php .php" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	<Directory \"/etc/zpanel\">" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	Options FollowSymLinks" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "    	AllowOverride None" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "    	Order allow,deny" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "    	Allow from all" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	</Directory>" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "  	<location /modbw>" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "    	SetHandler modbw-handler" >> /etc/zpanel/conf/httpd-vhosts.conf
echo " 	</location>" >> /etc/zpanel/conf/httpd-vhosts.conf
echo " " >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	Header add X-Hello \"time %D\"" >> /etc/zpanel/conf/httpd-vhosts.conf
echo " " >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	</VirtualHost>" >> /etc/zpanel/conf/httpd-vhosts.conf
echo " " >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	########################################################" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	# ZPanel generated VHOST configurations below.....     #" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	########################################################" >> /etc/zpanel/conf/httpd-vhosts.conf

service httpd restart

echo "127.0.0.1			${domain}">> /etc/hosts

clear
echo "===================================================="
echo "ZPanel has now been installed!"
echo " "
echo "IMPORTANT, ENSURE YOU MAKE NOTE OF THESE SETTINGS!!!"
echo " "
echo "      NEW MYSQL ROOT ACCOUNT"
echo "      =============================================="
echo "      PASSWORD: ${password}"
echo " "
echo "      ZPANEL ADMIN ACCOUNT LOGIN"
echo "      =============================================="
echo "      CONTROL PANEL URL: http://${domain}"
echo "      USERNAME: zadmin"
echo "      PASSWORD: zadmin"

