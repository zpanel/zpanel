#!/bin/sh
#
# Ubuntu Linux Installation Script for ZpanelX (Development Enviroment)
# Script written by Bobby Allen (ballen@zpanel.co.uk) 14/05/2011
#
# YOU MUST HAVE ACL INSTALLED AND SETUP ON YOUR PARTITION FIRST BEFORE INSTALLING
# FOR INFO READ: http://forge.zpanel.co.uk/bugs/view.php?id=3
#

# Apache HTTPD configuration file path
apache_config=/etc/apache2/apache2.conf

# ProFTPd configuration file path
proftpd_config=/etc/proftpd/proftpd.conf

# Install the required development enviroment packages...
sudo apt-get update
sudo apt-get install lamp-server^ phpmyadmin subversion zip proftpd

# Add 'include' to the Apache configuration file..
echo "# Include the ZPanel HTTPD managed configuration file." >> ${apache_config}
echo "Include /etc/zpanel/conf/httpd-ubuntu.conf" >> ${apache_config}

# Add 'include' to the ProFTPd configuration file..
echo "# Include the ZPanel ProFTPd managed configuration file." >> ${proftpd_config}
echo "Include /etc/zpanel/conf/proftpd-ubuntu.conf" >> ${proftpd_config}

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

# Download the contents of the SVN repository..
sudo svn co http://forge.zpanel.co.uk/svn/zpanelx/trunk /etc/zpanel/

# Set the security on these directories
sudo chmod -R 777 /etc/zpanel/
sudo chmod -R 777 /var/zpanel/
sudo chown -R www-data /etc/zpanel
sudo chmod -R g+s /etc/zpanel
sudo setfacl -R -d -m g::rwx /etc/zpanel
sudo setfacl -R -d -m o::rx /etc/zpanel
sudo chown -R www-data /var/zpanel
sudo chmod -R g+s /var/zpanel
sudo setfacl -R -d -m g::rwx /var/zpanel
sudo setfacl -R -d -m o::rx /var/zpanel

# Restart Apache...
sudo /etc/init.d/apache2 restart

clear
echo "ZPANEL LINUX INSTALLATION SCRIPT"
echo "================================"
echo "Development enviroment has been prepared..."
echo " Just a few more steps..."
echo " "
echo "   1) Open http://localhost/phpmyadmin/ and login as 'root'."
echo "   2) Import the SQL script found in /etc/zpanel/lib/dev/zpanel_core.sql"
echo "   3) Navigate to http://localhost/zpanel/ and login with 'zadmin' and password 'zadmin'...done!"
echo ""
echo ""

