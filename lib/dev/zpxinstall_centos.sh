#!/bin/sh
#
# Ubuntu Linux Installation Script for Zpanel 6.1.0 (Development Enviroment)
# Script written by Bobby Allen (ballen@zpanel.co.uk) 14/05/2011
#
#

# Apache HTTPD configuration file path
apache_config=/etc/httpd/conf/httpd.conf

# ProFTPd configuration file path
proftpd_config=/etc/proftpd.conf

clear
echo "ZPANEL ONLINE INSTALLER (for CentOS) (by Bobby Allen)"
echo "====================================================="
echo ""
echo "Welcome to the online installer for ZPanel, this will download the latest source over SVN and install it for you."
echo "It will attempt to download and install all the required software too!"
echo "Any bugs should be logged here: http://bugs.zpanelcp.com"
echo "Thanks,"
echo "Bobby (ballen@zpanelcp.com)"

# Install the required development enviroment packages...
sudo yum update
sudo yum remove vsftpd
sudo yum install httpd php php-devel php-gd php-imap php-mysql php-pear php-xml php-xmlrpc curl curl-devel perl-libwww-perl libxml2 libxml2-devel phpmyadmin subversion zip webalizer gcc gcc-c++


# We have to install ProFTPd Manually as CentOS does not have a package for it..
cd /tmp/
wget --passive-ftp http://packages.zpanelcp.com/pkgs/source/proftpd-1.3.3e.tar.gz
tar xvfz proftpd-1.3.3e.tar.gz
cd proftpd-1.3.3e/
./configure --sysconfdir=/etc
make
make install
cd ..
rm -fr proftpd-1.3.3e*
ln -s /usr/local/sbin/proftpd /usr/sbin/proftpd
wget http://packages.zpanelcp.com/pkgs/scripts/proftpd_centos55.txt
mv proftpd_centos55.txt /etc/int.d/proftpd
chmod 755 /etc/init.d/proftpd



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


# Add services to be started
sudo chkconfig --levels 235 httpd on
chkconfig --levels 235 proftpd on
sudo chkconfig --add mysqld
sudo chkconfig mysqld on
sudo service httpd start
sudo service mysqld start
sudo service proftpd start

# Add a cron task to run deamon every 30 mins...
echo "0,30 * * * * php /etc/zpanel/daemon.php" >> /etc/crontab
# Set permissions so Apache can create cronjobs!
sudo chmod 777 /etc/crontab

clear
echo "Will now attempt to create and insert the ZPanel core database into MySQL, please enter the MySQL root password when asked..."
mysql -uroot -p < /etc/zpanel/lib/dev/zpanel_core.sql
echo "Will now attempt to create and insert the ZPanel postfix database into MySQL, please enter the MySQL root password again when asked..."
mysql -uroot -p < /etc/zpanel/lib/dev/zpanel_postfix.sql

clear
echo "CentOS Install Script for ZPanel 6"
echo "=================================="
echo "Enviroment has been prepared..."
echo " Just a few more steps..."
echo " "
echo "   1) Open http://localhost/phpmyadmin/ and login as 'root'."
echo "   3) Add a MySQL user named 'zpanel' and password of 'zpanel' if you choose another account (recommended) you should edit the MySQL username and password in /etc/zpanel/conf/zcnf.php"
echo ""
echo ""

