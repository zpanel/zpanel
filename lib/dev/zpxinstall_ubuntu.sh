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
echo "ZPANEL ONLINE INSTALLER (by Bobby Allen)"
echo "========================================"
echo ""
echo "Welcome to the online installer for ZPanel, this will download the latest source over SVN and install it for you."
echo "This script has only been tested on Ubuntu Linux, It will attempt to download and install all the required software too!"
echo "Any bugs should be logged here: http://bugs.zpanelcp.com"
echo "Thanks,"
echo "Bobby (ballen@zpanelcp.com)"

# Install the required development enviroment packages...
sudo apt-get update
sudo apt-get install lamp-server^ phpmyadmin subversion zip proftpd

# Add 'include' to the Apache configuration file..
echo "# Include the ZPanel HTTPD managed configuration file." >> ${apache_config}
echo "Include /etc/zpanel/conf/httpd-ubuntu.conf" >> ${apache_config}

# Add 'include' to the ProFTPd configuration file..
echo "# Include the ZPanel ProFTPd managed configuration file." > ${proftpd_config}
echo "Include /etc/zpanel/conf/proftpd-ubuntu.conf" >> ${proftpd_config}

# Add exception to Sudoers file to enable zsudo execution for restarting Apache etc.
echo "# ZPanel modification to enable automated Apache restarts." >> /etc/sudoers
echo "www-data ALL=NOPASSWD: /etc/zpanel/bin/zsudo" >> /etc/sudoers

# Make the default directories
sudo mkdir /etc/zpanel/
sudo mkdir /var/zpanel/
sudo mkdir /var/zpanel/logs/
sudo mkdir /var/zpanel/logs/zadmin/
sudo mkdir /var/zpanel/backups/
sudo mkdir /var/zpanel/updates/
sudo mkdir /var/zpanel/hostdata/
sudo mkdir /var/zpanel/hostdata/zadmin/
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

# Restart Apache...
sudo /etc/init.d/apache2 restart

clear
echo "Will now attempt to create and insert the ZPanel database into MySQL, please enter the MySQL root password when asked..."
mysql -uroot -p < /etc/zpanel/lib/dev/zpanel_core.sql

clear
echo "Ubuntu Install Script for ZPanel 6"
echo "=================================="
echo "Enviroment has been prepared..."
echo " Just a few more steps..."
echo " "
echo "   1) Open http://localhost/phpmyadmin/ and login as 'root'."
echo "   3) Add a MySQL user named 'zpanel' and password of 'zpanel' if you choose another account (recommended) you should edit the MySQL username and password in /etc/zpanel/conf/zcnf.php"
echo ""
echo ""

