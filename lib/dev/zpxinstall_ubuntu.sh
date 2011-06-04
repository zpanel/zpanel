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
sudo apt-get install apache2 libapache2-mod-php5 libapache2-mod-bw php5 php5-cli php-common php5_php5-mysql php5-curl php5-gd php-pear php5-imagick php5-imap php5-mcrypt php5-xmlrpc php5-xsl php5-suhosin mysql-server mysql-client subversion zip proftpd webalizer

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

echo "Will now attempt to create and insert the ZPanel core database into MySQL, please enter the MySQL root password when asked..."
mysql -uroot -p < /etc/zpanel/lib/dev/zpanel_core.sql
echo "Will now attempt to create and insert the ZPanel postfix database into MySQL, please enter the MySQL root password again when asked..."
mysql -uroot -p < /etc/zpanel/lib/dev/zpanel_postfix.sql

echo ""
echo "ZPanel has now been installed!"
echo " "
echo "   Just a few more steps..."
echo "   ------------------------"
echo " "
echo "   1) Add a MySQL user named 'zpanel' and password of 'zpanel' if you choose another account (recommended)"
echo "      you should edit the MySQL username and password in /etc/zpanel/conf/zcnf.php"
echo "   2) Create a new cron job for daemon.php to run hourly ('crontab -e' with the following: '0 * * * * php /etc/zpanel/daemon.php')"
echo " "
echo "   If you need help with the 'final touches' please visit the ZPanel forums here: http://forums.zpanelcp.com."
echo "   Thanks for installing ZPanel! :)"

