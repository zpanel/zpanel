#!/bin/bash
#
# CentOS Linux Update Script for Zpanel 6.1.1
# Script written by Bobby Allen (ballen@zpanel.co.uk) 14/05/2011
#
#

# Remove old zdaemon cron
rm -rf /etc/cron.d/zdaemon
# Add updated cron task to run deamon every 60 mins...
touch /etc/cron.d/zdaemon
echo "0 * * * * root /usr/bin/php -q /etc/zpanel/daemon.php >> /dev/null 2>&1" >> /etc/cron.d/zdaemon
# Permissions must be 644 or cron will not run!
sudo chmod 644 /etc/cron.d/zdaemon
service cron restart

sudo chown root /etc/zpanel/bin/zsudo
sudo chmod 4777 /etc/zpanel/bin/zsudo

# Create ZPanel Cron file and set permissions
touch /var/spool/cron/apache
sudo chgrp apache /var/spool/cron
sudo chmod 777 /var/spool/cron
sudo chown apache /var/spool/cron/root
sudo chmod 644 /var/spool/cron/root

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

/etc/zpanel/lib/dev/setso --set -q cron_file /var/spool/cron/apache

# Remove the old version_checker module...
rm -rf /etc/zpanel/modules/admin/version_checker/

# Add new temp directory in correct place..
mkdir /var/zpanel/temp
chmod -R 777 /var/zpanel/temp
