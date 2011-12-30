#!/bin/bash
#
# CentOS Linux Installation Script for Zpanel 6.1.0 (Development Enviroment)
# Script written by Bobby Allen (ballen@zpanel.co.uk) 14/05/2011
#
#

# Apache HTTPD configuration file path
apache_config=/etc/httpd/conf/httpd.conf

# ProFTPd configuration file path
proftpd_config=/etc/proftpd.conf

# PostFix configuration files
postfix_main_config=/etc/postfix/main.cf
postfix_master_config=/etc/postfix/master.cf
mysql_relay_domains_maps=/etc/postfix/mysql_relay_domains_maps.cf
mysql_virtual_alias_maps=/etc/postfix/mysql_virtual_alias_maps.cf
mysql_virtual_domains_maps=/etc/postfix/mysql_virtual_domains_maps.cf
mysql_virtual_mailbox_limit_maps=/etc/postfix/mysql_virtual_mailbox_limit_maps.cf
mysql_virtual_mailbox_maps=/etc/postfix/mysql_virtual_mailbox_maps.cf
mysql_virtual_transport=/etc/postfix/mysql_virtual_transport.cf

# Dovecot configuration files
dovecot_config=/etc/dovecot.conf
dovecot_sql_config=/etc/postfix/dovecot-sql.conf
dovecot_trash_config=/etc/postfix/dovecot-trash.conf

clear
echo "#########################################################"
echo "# ZPanel Installation Package for CentOS Linux          #"
echo "# --------------------------------------------          #"
echo "# Package maintainer: Bobby Allen (ballen@zpanelcp.com) #"
echo "# Last updated:       04/06/2011                        #"
echo "# Website:            http://www.zpanelcp.com           #"
echo "########################################################"
[ $(whoami) == "root" ] && echo "" || echo "You must be root to install ZPanel" && exit
echo "Welcome to the online installer for ZPanel, this will download the required software and install ZPanel."
echo ""
echo "This install script is designed to be used on freshly installed servers or workstations"
echo "due to the nature of the software and amount of system changes it makes we recommend that"
echo "if you want to uninstall ZPanel that you re-install your OS."
echo ""
echo "We also recommend that ZPanel is installed on a dedicated server for security reasons!"
echo ""
echo "Are you sure you want to continue? Press ENTER to continue or CTRL+C to quit!"
read continue

# Install the required development enviroment packages...
echo "#########################################################"
echo "# Updating package repository cache.                    #"
echo "# --------------------------------------------          #"
echo "########################################################"
yum update
echo "#########################################################"
echo "# Installing Apache, PHP, MySQL etc.                    #"
echo "# --------------------------------------------          #"
echo "########################################################"
yum install httpd php53 php53-devel php53-gd php53-mbstring php53-imap php53-mysql php53-xml php53-xmlrpc curl curl-devel perl-libwww-perl libxml2 libxml2-devel mysql-server subversion zip webalizer gcc gcc-c++ httpd-devel.i386 system-switch-mail
#chkconfig --levels 235 sendmail off; /etc/init.d/sendmail stop; yum -y remove sendmail
yum remove vsftpd

echo "#########################################################"
echo "# Installing ProFTPd                                    #"
echo "# --------------------------------------------          #"
echo "########################################################"

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

echo "#########################################################"
echo "# Installing MOD_BW                                     #"
echo "# --------------------------------------------          #"
echo "########################################################"

# We have to install Mod_BW Manually as CentOS does not have a package for it..
cd /tmp/
wget http://forums.zpanelcp.com/pkgs/source/mod_bw-0.92.tgz
tar -zxvf mod_bw-0.92.tgz
apxs -i -a -c mod_bw.c
rm -fr mod_bw*
cd ~

echo "#########################################################"
echo "# Installing Suhosin                                    #"
echo "# --------------------------------------------          #"
echo "########################################################"

# We have to install suhosin Manually as CentOS does not have a package for it...
cd /tmp/
wget http://forums.zpanelcp.com/pkgs/source/suhosin-0.9.29.tgz
tar xvfz suhosin-0.9.29.tgz
cd suhosin-0.9.29
phpize
./configure
make
make install
touch /etc/php.d/suhosin.ini
chmod 755 /etc/php.d/suhosin.ini
echo "extension=suhosin.so" > /etc/php.d/suhosin.ini
echo "[Suhosin]" >> /etc/php.d/suhosin.ini
echo "suhosin.session.encrypt = Off" >> /etc/php.d/suhosin.ini
echo "suhosin.cookie.encrypt = Off" >> /etc/php.d/suhosin.ini
echo "suhosin.memory.limit = 512M" >> /etc/php.d/suhosin.ini
cd ..
rm -fr suhosin-0.9.29*
cd ~

# Add 'include' to the Apache configuration file..
echo "# Include the ZPanel HTTPD managed configuration file." >> ${apache_config}
echo "Include /etc/zpanel/conf/httpd.conf" >> ${apache_config}

# Add 'include' to the ProFTPd configuration file..
echo "# Include the ZPanel ProFTPd managed configuration file." > ${proftpd_config}
echo "Include /etc/zpanel/conf/proftpd.conf" >> ${proftpd_config}

# Add exception to Sudoers file to enable zexecution for restarting Apache etc.
echo "# ZPanel modification to enable automated Apache restarts." >> /etc/sudoers
echo "apache ALL=NOPASSWD: /etc/zpanel/bin/zsudo" >> /etc/sudoers

# Make the default directories
mkdir /etc/zpanel/
mkdir /var/zpanel/
mkdir /var/zpanel/logs/
mkdir /var/zpanel/backups/
mkdir /var/zpanel/updates/
mkdir /var/zpanel/hostdata/
mkdir /var/zpanel/temp/
mkdir /var/zpanel/hostdata/zadmin/
mkdir /var/zpanel/logs/domains/
mkdir /var/zpanel/logs/domains/zadmin/
mkdir /var/zpanel/logs/proftpd/

echo "#########################################################"
echo "# Getting latest ZPanel SVN Revision                    #"
echo "# --------------------------------------------          #"
echo "########################################################"

# Download the contents of the SVN repository..
echo "You may now be asked to accept the SSL certificate for our SVN repository..."
svn co https://zpanelcp.svn.sourceforge.net/svnroot/zpanelcp/trunk /etc/zpanel/

# Set the security on these directories
chown -R apache /etc/zpanel
chmod -R g+s /etc/zpanel
chown -R apache /var/zpanel
chmod -R g+s /var/zpanel
chmod -R 777 /etc/zpanel/
chmod -R 777 /var/zpanel/
chown root /etc/zpanel/bin/zsudo
chmod 4777 /etc/zpanel/bin/zsudo

echo "#########################################################"
echo "# Installing Postfix / Dovecot                          #"
echo "# --------------------------------------------          #"
echo "########################################################"

# We need to get the version of PostFix that has MySQL enabled.
yes | cp /etc/zpanel/lib/dev/pf_confs/CentOS-Base.repo /etc/yum.repos.d/
yum --enablerepo=centosplus install postfix dovecot

# Add services to be started
chkconfig --levels 235 httpd on
chkconfig --levels 235 proftpd on
chkconfig --levels 235 mysqld on
chkconfig --levels 345 postfix on
chkconfig --levels 345 dovecot on
service httpd start
service mysqld start
service proftpd start

echo "#########################################################"
echo "# Configure MySQL Root Password                         #"
echo "# --------------------------------------------          #"
echo "########################################################"

# Now we run the MySQL secure script (to enable the user to set a MySQL root password etc.)
/usr/bin/mysql_secure_installation

# Add a cron task to run deamon every 60 mins...
touch /etc/cron.d/zdaemon
echo "0 * * * * root /usr/bin/php -q /etc/zpanel/daemon.php >> /dev/null 2>&1" >> /etc/cron.d/zdaemon
# Permissions must be 644 or cron will not run!
chmod 644 /etc/cron.d/zdaemon
service crond restart

# Create ZPanel Cron file and set permissions
touch /var/spool/cron/apache
chmod 777 /var/spool/cron
chown apache /var/spool/cron/apache
chmod 644 /var/spool/cron/apache

echo "#################################################################################" > /var/spool/cron/apache
echo "# CRONTAB FOR ZPANEL CRON MANAGER MODULE                                        #" >> /var/spool/cron/apache
echo "# Module Developed by Bobby Allen, 17/12/2009                                   #" >> /var/spool/cron/apache
echo "#                                                                               #" >> /var/spool/cron/apache
echo "#################################################################################" >> /var/spool/cron/apache
echo "# WE DO NOT RECOMMEND YOU MODIFY THIS FILE DIRECTLY, PLEASE USE ZPANEL INSTEAD! #" >> /var/spool/cron/apache
echo "#################################################################################" >> /var/spool/cron/apache
echo "# DO NOT MANUALLY REMOVE ANY OF THE CRON ENTRIES FROM THIS FILE, USE ZPANEL     #" >> /var/spool/cron/apache
echo "# INSTEAD! THE ABOVE ENTRIES ARE USED FOR ZPANEL TASKS, DO NOT REMOVE THEM!     #" >> /var/spool/cron/apache
echo "#################################################################################" >> /var/spool/cron/apache


clear
echo "###############################################################"
echo "# Import ZPanel SQL Databases                                 #"
echo "# -------------------------------------------------           #"
echo "# Please now enter the root MySQL password so I can           #"
echo "# import the databases and create the ZPanel DB config file.. #"
echo "##############################################################"
read defaultpassword
echo "> Importing zpanel_core database.."
mysql -uroot -p${defaultpassword} < /etc/zpanel/lib/dev/zpanel_core.sql
echo "  ^ Done"
echo "> Importing zpanel_postfix database.."
mysql -uroot -p${defaultpassword} < /etc/zpanel/lib/dev/zpanel_postfix.sql
echo "  ^ Done!"
echo "> Importing the zpanel_roundcube database"
mysql -uroot -p${defaultpassword} < /etc/zpanel/lib/dev/zpanel_roundcube.sql
echo "  ^ Done!"
echo "> Writing the zpanel database configuration file.."

# Setup the default virtual host for the control panel and get ZPanel Setup Information
clear
echo "#########################################################"
echo "# ZPanel Configuration Details                          #"
echo "# --------------------------------------------          #"
echo "########################################################"
echo "ADMIN ACCOUNT DETAILS:"
echo "Your first name:"
read firstname
echo "Your last name:"
read lastname
echo "Your email address:"
read email
echo ""
echo "ENTER THE SUBDOMAIN THAT WILL HOST ZPANEL EG. 'CONTROL.YOURDOMAIN.COM'"
read domain

# Update the zpanel_core database with gathered information
zpassword=$(</dev/urandom tr -dc A-Za-z0-9 | head -c6)
password=$(</dev/urandom tr -dc A-Za-z0-9 | head -c8)
echo "SET PASSWORD FOR root@localhost=PASSWORD('${password}');" |mysql -uroot -p${defaultpassword} -hlocalhost
echo "update z_accounts set ac_pass_vc=MD5('${zpassword}') where ac_user_vc='zadmin';" |mysql -u root -p ${password} -h localhost zpanel_core
echo "update z_personal set ap_fullname_vc='${firstname} ${lastname}' where ap_id_pk='2';" |mysql -u root -p ${password} -h localhost zpanel_core
echo "update z_personal set ap_email_vc='${email}' where ap_id_pk='2';" |mysql -u root -p ${password} -h localhost zpanel_core
echo "CREATE USER `zadmin`@`%`;" |mysql -u root -p ${password} -h localhost
echo "GRANT USAGE ON * . * TO `zadmin`@`%`;" |mysql -u root -p ${password} -h localhost

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

# Set phpmyadmin freindly permissions on the config.inc.php (so phpMyAdmin doesn't complain)
chmod 644 /etc/zpanel/apps/phpmyadmin/config.inc.php

#write Apache VHOST file
echo "# ZPanel Apache Master VHOST file." > /etc/zpanel/conf/httpd-vhosts.conf
echo "# Written by Bobby Allen, 15/05/2011" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "#" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "# DO NOT EDIT THIS FILE MANUALLY - USE ZPANEL TO ADD AND REMOVE VHOSTS!" >> /etc/zpanel/conf/httpd-vhosts.conf
echo " " >> /etc/zpanel/conf/httpd-vhosts.conf
echo "NameVirtualHost *:80" >> /etc/zpanel/conf/httpd-vhosts.conf
echo " " >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	# Configuration for ZPanel control panel." >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	<VirtualHost *:80>" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	ServerAdmin ${email}" >> /etc/zpanel/conf/httpd-vhosts.conf
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
echo " " >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	</VirtualHost>" >> /etc/zpanel/conf/httpd-vhosts.conf
echo " " >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	########################################################" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	# ZPanel generated VHOST configurations below.....     #" >> /etc/zpanel/conf/httpd-vhosts.conf
echo "	########################################################" >> /etc/zpanel/conf/httpd-vhosts.conf

# Add ZPanel CP to hosts file
echo "127.0.0.1			${domain}">> /etc/hosts

################################################################################################
# BEGIN Configure Postfix Mail Server ##########################################################
################################################################################################
# Create a vmail user to store email files
mkdir -p /var/zpanel/vmail
chmod -R 777 /var/zpanel/vmail
chmod -R g+s /var/zpanel/vmail
groupadd -g 5000 vmail
useradd -m -g vmail -u 5000 -d /var/zpanel/vmail -s /bin/bash vmail
chown -R vmail.vmail /var/zpanel/vmail

# Postfix Master.cf
echo "# Dovecot LDA" >> ${postfix_master_config}
echo "dovecot   unix  -       n       n       -       -       pipe" >> ${postfix_master_config}
echo "  flags=DRhu user=vmail:mail argv=/usr/libexec/dovecot/deliver -d ${recipient}" >> ${postfix_master_config}

# Postfix Main.cf
echo "#########################################################################" > ${postfix_main_config}
echo "# HOST CONFIGURATION" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "myhostname = ${domain}" >> ${postfix_main_config}
echo "mydomain   = ${domain}" >> ${postfix_main_config}
echo "myorigin   = \$myhostname" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "# MAIN CONFIGURATION" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "mynetworks          = all" >> ${postfix_main_config}
echo "inet_interfaces     = all" >> ${postfix_main_config}
echo "mydestination       = \$myhostname, localhost.\$mydomain, localhost, \$mydomain" >> ${postfix_main_config}
echo "queue_directory     = /var/spool/postfix" >> ${postfix_main_config}
echo "command_directory   = /usr/sbin" >> ${postfix_main_config}
echo "daemon_directory    = /usr/libexec/postfix" >> ${postfix_main_config}
echo "mail_owner          = postfix" >> ${postfix_main_config}
echo "alias_maps          = hash:/etc/aliases" >> ${postfix_main_config}
echo "alias_database      = hash:/etc/aliases" >> ${postfix_main_config}
echo "sendmail_path       = /usr/sbin/sendmail.postfix" >> ${postfix_main_config}
echo "newaliases_path     = /usr/bin/newaliases.postfix" >> ${postfix_main_config}
echo "mailq_path          = /usr/bin/mailq.postfix" >> ${postfix_main_config}
echo "setgid_group        = postdrop" >> ${postfix_main_config}
echo "html_directory      = no" >> ${postfix_main_config}
echo "manpage_directory   = /usr/share/man" >> ${postfix_main_config}
echo "sample_directory    = /usr/share/doc/postfix-2.3.3/samples" >> ${postfix_main_config}
echo "readme_directory    = /usr/share/doc/postfix-2.3.3/README_FILES" >> ${postfix_main_config}
echo "mailbox_size_limit  = 0" >> ${postfix_main_config}
echo "recipient_delimiter = +" >> ${postfix_main_config}
echo "smtpd_helo_required             = yes" >> ${postfix_main_config}
echo "disable_vrfy_command            = yes" >> ${postfix_main_config}
echo "non_fqdn_reject_code            = 450" >> ${postfix_main_config}
echo "invalid_hostname_reject_code    = 450" >> ${postfix_main_config}
echo "maps_rbl_reject_code            = 450" >> ${postfix_main_config}
echo "#unverified_sender_reject_code  = 550" >> ${postfix_main_config}
echo "unknown_local_recipient_reject_code = 550" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "# SASL CONFIGURATION" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "smtpd_sasl_auth_enable         = yes" >> ${postfix_main_config}
echo "broken_sasl_auth_clients       = yes" >> ${postfix_main_config}
echo "smtpd_sasl_exceptions_networks = \$mynetworks" >> ${postfix_main_config}
echo "smtpd_sasl_type                = dovecot" >> ${postfix_main_config}
echo "smtpd_sasl_path                = private/auth" >> ${postfix_main_config}
echo "smtpd_sasl_security_options    = noanonymous" >> ${postfix_main_config}
echo "smtpd_recipient_restrictions   = permit_mynetworks," >> ${postfix_main_config}
echo "		         permit_sasl_authenticated," >> ${postfix_main_config}
echo "				 reject_unauth_destination," >> ${postfix_main_config}
echo "				 reject_unauth_pipelining," >> ${postfix_main_config}
echo "				 reject_non_fqdn_sender," >> ${postfix_main_config}
echo "				 reject_non_fqdn_recipient," >> ${postfix_main_config}
echo "				 reject_unknown_sender_domain," >> ${postfix_main_config}
echo "				 reject_unknown_recipient_domain," >> ${postfix_main_config}
echo "				 reject_invalid_helo_hostname," >> ${postfix_main_config}
echo "        		 warn_if_reject reject_non_fqdn_helo_hostname," >> ${postfix_main_config}
echo "        		 warn_if_reject reject_unknown_helo_hostname," >> ${postfix_main_config}
echo "        		 warn_if_reject reject_unknown_client," >> ${postfix_main_config}
echo "        		 reject_rbl_client zen.spamhaus.org," >> ${postfix_main_config}
echo "        		 reject_rbl_client bl.spamcop.net," >> ${postfix_main_config}
echo "        		 reject_rbl_client dnsbl.sorbs.net=127.0.0.2," >> ${postfix_main_config}
echo "        		 reject_rbl_client dnsbl.sorbs.net=127.0.0.3," >> ${postfix_main_config}
echo "        		 reject_rbl_client dnsbl.sorbs.net=127.0.0.4," >> ${postfix_main_config}
echo "        		 reject_rbl_client dnsbl.sorbs.net=127.0.0.5," >> ${postfix_main_config}
echo "        		 reject_rbl_client dnsbl.sorbs.net=127.0.0.7," >> ${postfix_main_config}
echo "        		 reject_rbl_client dnsbl.sorbs.net=127.0.0.9," >> ${postfix_main_config}
echo "        		 reject_rbl_client dnsbl.sorbs.net=127.0.0.11," >> ${postfix_main_config}
echo "        		 reject_rbl_client dnsbl.sorbs.net=127.0.0.12," >> ${postfix_main_config}
echo "        		 warn_if_reject reject_rhsbl_sender dsn.rfc-ignorant.org," >> ${postfix_main_config}
echo "        		 warn_if_reject reject_rhsbl_sender abuse.rfc-ignorant.org," >> ${postfix_main_config}
echo "        		 warn_if_reject reject_rhsbl_sender whois.rfc-ignorant.org," >> ${postfix_main_config}
echo "        		 warn_if_reject reject_rhsbl_sender bogusmx.rfc-ignorant.org," >> ${postfix_main_config}
echo "        		 warn_if_reject reject_rhsbl_sender postmaster.rfc-ignorant.org" >> ${postfix_main_config}
echo "smtpd_sender_restrictions      = permit_mynetworks," >> ${postfix_main_config}
echo "				 permit_sasl_authenticated," >> ${postfix_main_config}
echo "				 reject_unauth_pipelining," >> ${postfix_main_config}
echo "				 reject_non_fqdn_sender," >> ${postfix_main_config}
echo "				 reject_unknown_sender_domain" >> ${postfix_main_config}
echo "smtpd_data_restrictions        = reject_unauth_pipelining," >> ${postfix_main_config}
echo "        			 reject_multi_recipient_bounce" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "# TLS CONFIGURATION" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "#smtp_tls_CAfile                   = /etc/pki/tls/certs/cert.pem" >> ${postfix_main_config}
echo "#smtp_tls_cert_file                = /etc/pki/tls/certs/myserver.example.com.crt" >> ${postfix_main_config}
echo "#smtp_tls_key_file                 = /etc/pki/tls/private/myserver.example.com.key" >> ${postfix_main_config}
echo "##Postfix 2.5 or greater must use:##" >> ${postfix_main_config}
echo "##smtp_tls_session_cache_database  = btree:\$data_directory/smtp_tls_session_cache" >> ${postfix_main_config}
echo "#smtp_tls_session_cache_database   = btree:/var/spool/postfix/smtp_tls_session_cache" >> ${postfix_main_config}
echo "#smtp_tls_security_level = may" >> ${postfix_main_config}
echo "#smtpd_tls_CAfile                  = /etc/pki/tls/certs/cert.pem" >> ${postfix_main_config}
echo "#smtpd_tls_cert_file               = /etc/pki/tls/certs/myserver.example.com.crt" >> ${postfix_main_config}
echo "#smtpd_tls_key_file                = /etc/pki/tls/private/myserver.example.com.key" >> ${postfix_main_config}
echo "##Postfix 2.5 or greater must use:##" >> ${postfix_main_config}
echo "##smtpd_tls_session_cache_database = btree:\$data_directory/smtpd_tls_session_cache" >> ${postfix_main_config}
echo "#smtpd_tls_session_cache_database  = btree:/var/spool/postfix/smtpd_tls_session_cache" >> ${postfix_main_config}
echo "#smtpd_tls_dh1024_param_file       = \$config_directory/dh_1024.pem" >> ${postfix_main_config}
echo "#smtpd_tls_dh512_param_file        = \$config_directory/dh_512.pem" >> ${postfix_main_config}
echo "#smtpd_tls_security_level          = may" >> ${postfix_main_config}
echo "#smtpd_tls_received_header         = yes" >> ${postfix_main_config}
echo "#smtpd_tls_ask_ccert               = yes" >> ${postfix_main_config}
echo "#smtpd_tls_loglevel                = 1" >> ${postfix_main_config}
echo "#tls_random_source                 = dev:/dev/urandom" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "# SPECIAL CONFIGURATION EXTRAS" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "#default_privs        = nobody" >> ${postfix_main_config}
echo "#proxy_interfaces     = 1.2.3.4" >> ${postfix_main_config}
echo "#relay_domains        = \$mydestination" >> ${postfix_main_config}
echo "#relayhost            = [gateway.my.domain]" >> ${postfix_main_config}
echo "#relayhost            = [an.ip.add.ress]" >> ${postfix_main_config}
echo "#relay_recipient_maps = hash:/etc/postfix/relay_recipients" >> ${postfix_main_config}
echo "#in_flow_delay        = 1s" >> ${postfix_main_config}
echo "#recipient_delimiter  = +" >> ${postfix_main_config}
echo "#home_mailbox         = Maildir/" >> ${postfix_main_config}
echo "#mail_spool_directory = /var/spool/mail" >> ${postfix_main_config}
echo "#mailbox_command      = /some/where/procmail" >> ${postfix_main_config}
echo "#mailbox_transport    = cyrus" >> ${postfix_main_config}
echo "#fallback_transport   = lmtp:unix:/var/lib/imap/socket/lmtp" >> ${postfix_main_config}
echo "#luser_relay          = \$user@other.host" >> ${postfix_main_config}
echo "#header_checks        = regexp:/etc/postfix/header_checks" >> ${postfix_main_config}
echo "#fast_flush_domains   = \$relay_domains" >> ${postfix_main_config}
echo "#smtpd_banner         = \$myhostname ESMTP $mail_name ($mail_version)" >> ${postfix_main_config}
echo "#local_destination_concurrency_limit   = 2" >> ${postfix_main_config}
echo "#default_destination_concurrency_limit = 20" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "# DEBUG CONFIGURATION" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "#debug_peer_list = 127.0.0.1" >> ${postfix_main_config}
echo "#debug_peer_list = some.domain" >> ${postfix_main_config}
echo "debugger_command =" >> ${postfix_main_config}
echo "	 PATH=/bin:/usr/bin:/usr/local/bin:/usr/X11R6/bin" >> ${postfix_main_config}
echo "	 xxgdb $daemon_directory/\$process_name \$process_id & sleep 5" >> ${postfix_main_config}
echo "# debugger_command =" >> ${postfix_main_config}
echo "#	PATH=/bin:/usr/bin:/usr/local/bin; export PATH; (echo cont;" >> ${postfix_main_config}
echo "#	echo where) | gdb \$daemon_directory/\$process_name \$process_id 2>&1" >> ${postfix_main_config}
echo "#	>\$config_directory/\$process_name.\$process_id.log & sleep 5" >> ${postfix_main_config}
echo "#" >> ${postfix_main_config}
echo "# debugger_command =" >> ${postfix_main_config}
echo "#	PATH=/bin:/usr/bin:/sbin:/usr/sbin; export PATH; screen" >> ${postfix_main_config}
echo "#	-dmS \$process_name gdb \$daemon_directory/\$process_name" >> ${postfix_main_config}
echo "#	\$process_id & sleep 1" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "# ZPANEL CONFIGURATION" >> ${postfix_main_config}
echo "#########################################################################" >> ${postfix_main_config}
echo "#transport_maps                = mysql:${mysql_virtual_transport}" >> ${postfix_main_config}
echo "#relay_domains                 = mysql:${mysql_relay_domains_maps}" >> ${postfix_main_config}
echo "virtual_alias_maps             = mysql:${mysql_virtual_alias_maps}" >> ${postfix_main_config}
echo "virtual_mailbox_domains        = mysql:${mysql_virtual_domains_maps}" >> ${postfix_main_config}
echo "virtual_mailbox_maps           = mysql:${mysql_virtual_mailbox_maps}" >> ${postfix_main_config}
echo "virtual_mailbox_limit          = 51200000" >> ${postfix_main_config}
echo "virtual_minimum_uid            = 5000" >> ${postfix_main_config}
echo "virtual_uid_maps               = static:5000" >> ${postfix_main_config}
echo "virtual_gid_maps               = static:5000" >> ${postfix_main_config}
echo "virtual_mailbox_base           = /var/zpanel/vmail" >> ${postfix_main_config}
echo "virtual_transport              = virtual" >> ${postfix_main_config}
echo "virtual_create_maildirsize     = yes" >> ${postfix_main_config}
echo "virtual_mailbox_extended       = yes" >> ${postfix_main_config}
echo "virtual_mailbox_limit_maps     = mysql:${mysql_virtual_mailbox_limit_maps}" >> ${postfix_main_config}
echo "virtual_mailbox_limit_override = yes" >> ${postfix_main_config}
echo "virtual_maildir_limit_message  = Sorry, the user's maildir has no space available in their inbox." >> ${postfix_main_config}
echo "virtual_overquota_bounce       = yes" >> ${postfix_main_config}
echo "local_transport                = virtual" >> ${postfix_main_config}
echo "dovecot_destination_recipient_limit = 1" >> ${postfix_main_config}

# Dovecot Conf
echo "protocols = imap imaps pop3 pop3s" > ${dovecot_config}
echo "log_timestamp = '%Y-%m-%d %H:%M:%S'" >> ${dovecot_config}
echo "mail_location = maildir:/var/zpanel/vmail/%d/%n" >> ${dovecot_config}
echo "protocol pop3 {" >> ${dovecot_config}
echo "    pop3_uidl_format = %08Xu%08Xv" >> ${dovecot_config}
echo "}" >> ${dovecot_config}
echo "" >> ${dovecot_config}
echo "auth default {" >> ${dovecot_config}
echo "    mechanisms = plain login" >> ${dovecot_config}
echo "    user = root" >> ${dovecot_config}
echo "" >> ${dovecot_config}
echo "    passdb sql {" >> ${dovecot_config}
echo "        args = ${dovecot_sql_config}" >> ${dovecot_config}
echo "    }" >> ${dovecot_config}
echo "" >> ${dovecot_config}
echo "    userdb sql {" >> ${dovecot_config}
echo "        args = ${dovecot_sql_config}" >> ${dovecot_config}
echo "    }" >> ${dovecot_config}
echo "" >> ${dovecot_config}
echo "    socket listen {" >> ${dovecot_config}
echo "        client {" >> ${dovecot_config}
echo "            path = /var/spool/postfix/private/auth" >> ${dovecot_config}
echo "            mode = 0660" >> ${dovecot_config}
echo "            user = postfix" >> ${dovecot_config}
echo "            group = postfix" >> ${dovecot_config}
echo "        }" >> ${dovecot_config}
echo "    }" >> ${dovecot_config}
echo "}" >> ${dovecot_config}
echo "plugin {" >> ${dovecot_config}
echo "  #quota = maildir:storage=10240:messages=1000" >> ${dovecot_config}
echo "  #acl  = vfile:/etc/dovecot/acls" >> ${dovecot_config}
echo "  trash = ${dovecot_trash_config}" >> ${dovecot_config}
echo "}" >> ${dovecot_config}

# Postfix and dovecot sql mappings
touch ${dovecot_sql_config}
chmod 777 ${dovecot_sql_config}
echo "driver = mysql" > ${dovecot_sql_config}
echo "connect = host=127.0.0.1 dbname=zpanel_postfix user=root password=${password}" >> ${dovecot_sql_config}
echo "default_pass_scheme = PLAIN" >> ${dovecot_sql_config}
echo "password_query = SELECT password FROM mailbox WHERE username = '%u'" >> ${dovecot_sql_config}
echo "user_query = SELECT maildir, 5000 AS uid, 5000 AS gid FROM mailbox WHERE username = '%u' AND active = '1'" >> ${dovecot_sql_config}

touch ${dovecot_trash_config}
chmod 777 ${dovecot_trash_config}
echo "1 Spam" > ${dovecot_trash_config}
echo "2 Trash" >> ${dovecot_trash_config}
echo "3 Junk" >> ${dovecot_trash_config}

touch ${mysql_relay_domains_maps}
chmod 777 ${mysql_relay_domains_maps}
echo "user = root" > ${mysql_relay_domains_maps}
echo "password = ${password}" >> ${mysql_relay_domains_maps}
echo "hosts = 127.0.0.1" >> ${mysql_relay_domains_maps}
echo "dbname = zpanel_postfix" >> ${mysql_relay_domains_maps}
echo "table = domain" >> ${mysql_relay_domains_maps}
echo "select_field = domain" >> ${mysql_relay_domains_maps}
echo "where_field = domain" >> ${mysql_relay_domains_maps}
echo "additional_conditions = and backupmx = '1'" >> ${mysql_relay_domains_maps}

touch ${mysql_virtual_alias_maps}
chmod 777 ${mysql_virtual_alias_maps}
echo "user = root" > ${mysql_virtual_alias_maps}
echo "password = ${password}" >> ${mysql_virtual_alias_maps}
echo "hosts = 127.0.0.1" >> ${mysql_virtual_alias_maps}
echo "dbname = zpanel_postfix" >> ${mysql_virtual_alias_maps}
echo "table = alias" >> ${mysql_virtual_alias_maps}
echo "select_field = goto" >> ${mysql_virtual_alias_maps}
echo "where_field = address" >> ${mysql_virtual_alias_maps}

touch ${mysql_virtual_domains_maps}
chmod 777 ${mysql_virtual_domains_maps}
echo "user = root" > ${mysql_virtual_domains_maps}
echo "password = ${password}" >> ${mysql_virtual_domains_maps}
echo "hosts = 127.0.0.1" >> ${mysql_virtual_domains_maps}
echo "dbname = zpanel_postfix" >> ${mysql_virtual_domains_maps}
echo "table = domain" >> ${mysql_virtual_domains_maps}
echo "select_field = domain" >> ${mysql_virtual_domains_maps}
echo "where_field = domain" >> ${mysql_virtual_domains_maps}
echo "#additional_conditions = and backupmx = '0' and active = '1'" >> ${mysql_virtual_domains_maps}

touch ${mysql_virtual_mailbox_limit_maps}
chmod 777 ${mysql_virtual_mailbox_limit_maps}
echo "user = root" > ${mysql_virtual_mailbox_limit_maps}
echo "password = ${password}" >> ${mysql_virtual_mailbox_limit_maps}
echo "hosts = 127.0.0.1" >> ${mysql_virtual_mailbox_limit_maps}
echo "dbname = zpanel_postfix" >> ${mysql_virtual_mailbox_limit_maps}
echo "table = mailbox" >> ${mysql_virtual_mailbox_limit_maps}
echo "select_field = quota" >> ${mysql_virtual_mailbox_limit_maps}
echo "where_field = username" >> ${mysql_virtual_mailbox_limit_maps}
echo "#additional_conditions = and active = '1'" >> ${mysql_virtual_mailbox_limit_maps}

touch ${mysql_virtual_mailbox_maps}
chmod 777 ${mysql_virtual_mailbox_maps}
echo "user = root" > ${mysql_virtual_mailbox_maps}
echo "password = ${password}" >> ${mysql_virtual_mailbox_maps}
echo "hosts = 127.0.0.1" >> ${mysql_virtual_mailbox_maps}
echo "dbname = zpanel_postfix" >> ${mysql_virtual_mailbox_maps}
echo "table = mailbox" >> ${mysql_virtual_mailbox_maps}
echo "select_field = maildir" >> ${mysql_virtual_mailbox_maps}
echo "where_field = username" >> ${mysql_virtual_mailbox_maps}
echo "#additional_conditions = and active = '1'" >> ${mysql_virtual_mailbox_maps}

touch ${mysql_virtual_transport}
chmod 777 ${mysql_virtual_transport}
echo "user = root" > ${mysql_virtual_transport}
echo "password = ${password}" >> ${mysql_virtual_transport}
echo "hosts = 127.0.0.1" >> ${mysql_virtual_transport}
echo "dbname = zpanel_postfix" >> ${mysql_virtual_transport}
echo "table = domain" >> ${mysql_virtual_transport}
echo "select_field = transport" >> ${mysql_virtual_transport}
echo "where_field = domain" >> ${mysql_virtual_transport}

# Roundcube Webmail Config
echo "<?php" > /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config = array();" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_dsnw'] = 'mysql://root:${password}@localhost/zpanel_roundcube';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_dsnr'] = '';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_max_length'] = 512000;" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_persistent'] = FALSE;" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_table_users'] = 'users';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_table_identities'] = 'identities';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_table_contacts'] = 'contacts';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_table_session'] = 'session';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_table_cache'] = 'cache';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_table_messages'] = 'messages';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_sequence_users'] = 'user_ids';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_sequence_identities'] = 'identity_ids';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_sequence_contacts'] = 'contact_ids';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_sequence_cache'] = 'cache_ids';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "\$rcmail_config['db_sequence_messages'] = 'message_ids';" >> /etc/zpanel/apps/webmail/config/db.inc.php
echo "?>" >> /etc/zpanel/apps/webmail/config/db.inc.php

chgrp postfix /etc/postfix/mysql_*.cf
chmod 777 /etc/postfix/mysql_*.cf

# Set the correct service names in the database for this distrubion...
/etc/zpanel/lib/dev/setso --set -q lsn_apache httpd
/etc/zpanel/lib/dev/setso --set -q lsn_proftpd proftpd
/etc/zpanel/lib/dev/setso --set -q cron_file /var/spool/cron/apache

service postfix start
service dovecot start
service httpd restart

################################################################################################
# END Configure Postfix Mail Server ############################################################
################################################################################################

echo "=========================================================="
echo "ZPanel has now been installed!"
echo " "
echo "IMPORTANT: Ensure you make a note of these settings"
echo "           for future reference and to access the"
echo "           control panel for the first time..."
echo " "
echo "           NEW MYSQL ROOT ACCOUNT"
echo "           =============================================="
echo "           PASSWORD: ${password}"
echo " "
echo "           ZPANEL ADMIN ACCOUNT LOGIN"
echo "           =============================================="
echo "           CONTROL PANEL URL: http://${domain}"
echo "           USERNAME: zadmin"
echo "           PASSWORD: ${zpassword}"
echo ""
