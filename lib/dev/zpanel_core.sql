-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 14, 2011 at 08:31 PM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zpanel_core`
--

-- --------------------------------------------------------

CREATE DATABASE `zpanel_core`;
USE `zpanel_core`;

--
-- Table structure for table `z_accounts`
--

CREATE TABLE IF NOT EXISTS `z_accounts` (
  `ac_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ac_user_vc` varchar(10) DEFAULT NULL,
  `ac_pass_vc` varchar(64) DEFAULT NULL,
  `ac_package_fk` int(6) DEFAULT NULL,
  `ac_paiduntil_ts` int(30) DEFAULT NULL,
  `ac_reseller_fk` int(6) DEFAULT NULL,
  `ac_created_ts` int(30) DEFAULT NULL,
  `ac_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ac_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `z_accounts`
--

INSERT INTO `z_accounts` (`ac_id_pk`, `ac_user_vc`, `ac_pass_vc`, `ac_package_fk`, `ac_paiduntil_ts`, `ac_reseller_fk`, `ac_created_ts`, `ac_deleted_ts`) VALUES
(1, 'tasksys', NULL, 0, 0, NULL, 1265128421, NULL),
(2, 'zadmin', '3a20eb1aba49463ac3f76e1e9fea3957', 1, 0, 2, 1266504603, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `z_aliases`
--

CREATE TABLE IF NOT EXISTS `z_aliases` (
  `al_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `al_acc_fk` int(6) DEFAULT NULL,
  `al_address_vc` varchar(255) DEFAULT NULL,
  `al_destination_vc` varchar(255) DEFAULT NULL,
  `al_created_ts` int(30) DEFAULT NULL,
  `al_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`al_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_aliases`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_bandwidth`
--

CREATE TABLE IF NOT EXISTS `z_bandwidth` (
  `bd_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `bd_acc_fk` int(6) DEFAULT NULL,
  `bd_month_in` int(6) DEFAULT NULL,
  `bd_transamount_bi` bigint(20) DEFAULT NULL,
  `bd_diskamount_bi` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`bd_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=91 ;

--
-- Dumping data for table `z_bandwidth`
--

INSERT INTO `z_bandwidth` (`bd_id_pk`, `bd_acc_fk`, `bd_month_in`, `bd_transamount_bi`, `bd_diskamount_bi`) VALUES
(82, 1, 201103, 0, -1),
(83, 2, 201103, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `z_cronjobs`
--

CREATE TABLE IF NOT EXISTS `z_cronjobs` (
  `ct_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ct_acc_fk` int(6) DEFAULT NULL,
  `ct_script_vc` varchar(255) DEFAULT NULL,
  `ct_description_tx` text,
  `ct_created_ts` int(30) DEFAULT NULL,
  `ct_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ct_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_cronjobs`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_distlists`
--

CREATE TABLE IF NOT EXISTS `z_distlists` (
  `dl_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `dl_acc_fk` int(6) DEFAULT NULL,
  `dl_address_vc` varchar(255) DEFAULT NULL,
  `dl_created_ts` int(30) DEFAULT NULL,
  `dl_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`dl_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_distlists`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_distlistusers`
--

CREATE TABLE IF NOT EXISTS `z_distlistusers` (
  `du_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `du_distlist_fk` int(6) DEFAULT NULL,
  `du_address_vc` varchar(255) DEFAULT NULL,
  `du_created_ts` int(30) DEFAULT NULL,
  `du_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`du_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_distlistusers`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_faqs`
--

CREATE TABLE IF NOT EXISTS `z_faqs` (
  `fq_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `fq_queston_tx` text,
  `fq_answer_tx` text,
  PRIMARY KEY (`fq_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `z_faqs`
--

INSERT INTO `z_faqs` (`fq_id_pk`, `fq_queston_tx`, `fq_answer_tx`) VALUES
(1, 'How can I update my personal contact details?', 'From the control panel homepage please click on the ''My Account'' icon to enable you to update your personal details.'),
(2, 'I need to change my password!', 'Your ZPanel and MySQL password can be easily changed using the ''Password assistant'' icon on the control panel.');

-- --------------------------------------------------------

--
-- Table structure for table `z_forwarders`
--

CREATE TABLE IF NOT EXISTS `z_forwarders` (
  `fw_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `fw_acc_fk` int(6) DEFAULT NULL,
  `fw_address_vc` varchar(255) DEFAULT NULL,
  `fw_destination_vc` varchar(255) DEFAULT NULL,
  `fw_created_ts` int(30) DEFAULT NULL,
  `fw_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`fw_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_forwarders`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_ftpaccounts`
--

CREATE TABLE IF NOT EXISTS `z_ftpaccounts` (
  `ft_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ft_acc_fk` int(6) DEFAULT NULL,
  `ft_user_vc` varchar(20) DEFAULT NULL,
  `ft_directory_vc` varchar(255) DEFAULT NULL,
  `ft_access_vc` varchar(20) DEFAULT NULL,
  `ft_created_ts` int(6) DEFAULT NULL,
  `ft_deleted_ts` int(6) DEFAULT NULL,
  PRIMARY KEY (`ft_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_ftpaccounts`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_htaccess`
--

CREATE TABLE IF NOT EXISTS `z_htaccess` (
  `ht_id_pk` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ht_acc_fk` int(6) DEFAULT NULL,
  `ht_user_vc` varchar(10) DEFAULT NULL,
  `ht_dir_vc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ht_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_htaccess`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_invoiceitems`
--

CREATE TABLE IF NOT EXISTS `z_invoiceitems` (
  `ii_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ii_invoice_fk` int(6) DEFAULT NULL,
  `ii_amount_in` int(4) DEFAULT '1',
  `ii_package_fk` int(6) DEFAULT NULL,
  PRIMARY KEY (`ii_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_invoiceitems`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_invoices`
--

CREATE TABLE IF NOT EXISTS `z_invoices` (
  `in_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `in_account_fk` int(6) DEFAULT NULL,
  `in_month_int` int(6) DEFAULT NULL,
  `in_paid_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`in_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_invoices`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_logs`
--

CREATE TABLE IF NOT EXISTS `z_logs` (
  `lg_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `lg_acc_fk` int(6) DEFAULT NULL,
  `lg_when_ts` int(30) DEFAULT NULL,
  `lg_ipaddress_vc` varchar(15) DEFAULT NULL,
  `lg_details_tx` text,
  PRIMARY KEY (`lg_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_logs`
--

-- --------------------------------------------------------

--
-- Table structure for table `z_mailboxes`
--

CREATE TABLE IF NOT EXISTS `z_mailboxes` (
  `mb_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mb_acc_fk` int(6) DEFAULT NULL,
  `mb_address_vc` varchar(255) DEFAULT NULL,
  `mb_created_ts` int(30) DEFAULT NULL,
  `mb_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`mb_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_mailboxes`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_mysql`
--

CREATE TABLE IF NOT EXISTS `z_mysql` (
  `my_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `my_acc_fk` int(6) DEFAULT NULL,
  `my_name_vc` varchar(40) DEFAULT NULL,
  `my_usedspace_bi` bigint(50) DEFAULT '0',
  `my_created_ts` int(30) DEFAULT NULL,
  `my_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`my_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_mysql`
--


-- --------------------------------------------------------

--
-- Table structure for table `z_packages`
--

CREATE TABLE IF NOT EXISTS `z_packages` (
  `pk_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `pk_name_vc` varchar(30) DEFAULT NULL,
  `pk_reseller_fk` int(6) DEFAULT NULL,
  `pk_enablephp_in` int(1) DEFAULT '0',
  `pk_enablecgi_in` int(1) DEFAULT '0',
  `pk_created_ts` int(30) DEFAULT NULL,
  `pk_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`pk_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `z_packages`
--

INSERT INTO `z_packages` (`pk_id_pk`, `pk_name_vc`, `pk_reseller_fk`, `pk_enablephp_in`, `pk_enablecgi_in`, `pk_created_ts`, `pk_deleted_ts`) VALUES
(1, 'Administration', 2, 1, 1, NULL, NULL),
(5, 'Demo', 2, 0, 0, 1267130176, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `z_permissions`
--

CREATE TABLE IF NOT EXISTS `z_permissions` (
  `pr_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `pr_package_fk` int(6) DEFAULT NULL,
  `pr_admin_in` int(1) DEFAULT '0',
  `pr_reseller_in` int(1) DEFAULT '0',
  PRIMARY KEY (`pr_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `z_permissions`
--

INSERT INTO `z_permissions` (`pr_id_pk`, `pr_package_fk`, `pr_admin_in`, `pr_reseller_in`) VALUES
(1, 1, 1, 1),
(5, 5, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `z_personal`
--

CREATE TABLE IF NOT EXISTS `z_personal` (
  `ap_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ap_acc_fk` int(6) DEFAULT NULL,
  `ap_fullname_vc` varchar(30) DEFAULT NULL,
  `ap_email_vc` varchar(255) DEFAULT NULL,
  `ap_address_tx` text,
  `ap_postcode_vc` varchar(10) DEFAULT NULL,
  `ap_phone_vc` varchar(50) DEFAULT NULL,
  `ap_language_vc` varchar(45) NOT NULL,
  PRIMARY KEY (`ap_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `z_personal`
--

INSERT INTO `z_personal` (`ap_id_pk`, `ap_acc_fk`, `ap_fullname_vc`, `ap_email_vc`, `ap_address_tx`, `ap_postcode_vc`, `ap_phone_vc`, `ap_language_vc`) VALUES
(1, 1, 'TASK SYSTEM', NULL, NULL, NULL, NULL, ''),
(2, 2, 'Development Zadmin', 'development@zpanel.co.uk', '24 Developer Street,\r\nLinus Street,\r\nCray Town,\r\nArea 51', 'G43 KIT', 'N/A', 'en-us'),
(6, 6, 'demo', 'demo12@zpanel.co.uk', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `z_quotas`
--

CREATE TABLE IF NOT EXISTS `z_quotas` (
  `qt_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `qt_package_fk` int(6) DEFAULT NULL,
  `qt_domains_in` int(6) DEFAULT '0',
  `qt_subdomains_in` int(6) DEFAULT '0',
  `qt_parkeddomains_in` int(6) DEFAULT '0',
  `qt_mailboxes_in` int(6) DEFAULT '0',
  `qt_fowarders_in` int(6) DEFAULT '0',
  `qt_distlists_in` int(6) DEFAULT '0',
  `qt_ftpaccounts_in` int(6) DEFAULT '0',
  `qt_mysql_in` int(6) DEFAULT '0',
  `qt_diskspace_bi` bigint(20) DEFAULT '0',
  `qt_bandwidth_bi` bigint(20) DEFAULT '0',
  `qt_bwenabled_in` INT(1) DEFAULT '0', 
  `qt_dlenabled_in` int(1) DEFAULT '0',
  `qt_totalbw_fk` int(30) DEFAULT NULL,
  `qt_minbw_fk` int(30) DEFAULT NULL,
  `qt_maxcon_fk` int(30) DEFAULT NULL,
  `qt_filesize_fk` int(30) DEFAULT NULL,
  `qt_filespeed_fk` int(30) DEFAULT NULL,
  `qt_filetype_vc` varchar(30) NOT NULL DEFAULT '*',
  `qt_modified_in` int(1) DEFAULT '0',
  PRIMARY KEY (`qt_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `z_quotas`
--

INSERT INTO `z_quotas` (`qt_id_pk`, `qt_package_fk`, `qt_domains_in`, `qt_subdomains_in`, `qt_parkeddomains_in`, `qt_mailboxes_in`, `qt_fowarders_in`, `qt_distlists_in`, `qt_ftpaccounts_in`, `qt_mysql_in`, `qt_diskspace_bi`, `qt_bandwidth_bi`) VALUES
(1, 1, 3, 10, 5, 10, 100, 5, 50, 50, 2048000000, 10240000000),
(5, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1024000000);

-- --------------------------------------------------------

--
-- Table structure for table `z_throttle`
--

CREATE TABLE IF NOT EXISTS `z_throttle` (
  `tr_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `tr_package_fk` int(6) DEFAULT NULL,
  `tr_bwenabled_in` int(1) DEFAULT '0',
  `tr_dlenabled_in` int(1) DEFAULT '0',
  `tr_totalbw_fk` int(30) DEFAULT NULL,
  `tr_minbw_fk` int(30) DEFAULT NULL,
  `tr_maxcon_fk` int(30) DEFAULT NULL,
  `tr_filesize_fk` int(30) DEFAULT NULL,
  `tr_filespeed_fk` int(30) DEFAULT NULL,
  `tr_filetype_vc` varchar(30) NOT NULL DEFAULT '*',
  PRIMARY KEY (`tr_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `z_throttle`
--

INSERT INTO `z_throttle` (`tr_id_pk`, `tr_package_fk`, `tr_bwenabled_in`, `tr_dlenabled_in`, `tr_totalbw_fk`, `tr_minbw_fk`, `tr_maxcon_fk`, `tr_filesize_fk`, `tr_filespeed_fk`, `tr_filetype_vc`) VALUES
(1, 1, 0, 0, 0, 0, 0, 0, 10240, '');

-- --------------------------------------------------------

--
-- Table structure for table `z_resellers`
--

CREATE TABLE IF NOT EXISTS `z_resellers` (
  `rc_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `rc_acc_fk` int(6) DEFAULT NULL,
  `rc_company_vc` varchar(100) DEFAULT NULL,
  `rc_template_vc` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`rc_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `z_resellers`
--

INSERT INTO `z_resellers` (`rc_id_pk`, `rc_acc_fk`, `rc_company_vc`, `rc_template_vc`) VALUES
(1, 2, 'Example Company', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `z_settings`
--

CREATE TABLE IF NOT EXISTS `z_settings` (
  `st_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `st_name_vc` varchar(15) NOT NULL,
  `st_value_tx` text,
  `st_desc_tx` text,
  `st_label_vc` varchar(35) DEFAULT NULL,
  `st_inputtype_vc` varchar(20) DEFAULT 'text',
  `st_checkvalue_tx` text,
  `st_editable_in` int(1) DEFAULT NULL,
  PRIMARY KEY (`st_id_pk`,`st_name_vc`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

--
-- Dumping data for table `z_settings`
--

INSERT INTO `z_settings` (`st_id_pk`, `st_name_vc`, `st_value_tx`, `st_desc_tx`, `st_label_vc`, `st_inputtype_vc`, `st_checkvalue_tx`, `st_editable_in`) VALUES
(9, 'zpanel_version', '6.1.1', 'The version of ZPanel currently running on the server.', NULL, 'text', NULL, 0),
(10, 'zpanel_root', '/etc/zpanel/', 'The root directory of ZPanel. (With a trailing slash)', NULL, 'text', NULL, 0),
(11, 'zpanel_template', 'zpanel6', 'The folder name of the ZPanel template to use.', '200', 'text', NULL, 0),
(12, 'zpanel_lang', 'en-us', 'The language folder to use.', '367', 'text', NULL, 0),
(13, 'zpanel_df', 'H:i jS M Y T', 'The PHP date() format to display dates and times in ZPanel.\r\nSee http://uk2.php.net/manual/en/function.date.php', '368', 'text', NULL, 1),
(14, 'server_email', 'no-reply@localhost', 'The email address that emails should be sent from on this server.', '369', 'text', NULL, 1),
(16, 'module_icons_pr', '8', 'This is how many columns to display per row. Its used in displaying the module icons when the user has logged in.', '370', 'text', NULL, 1),
(17, 'webalizer_sd', 'apps/webalizer/', 'The directory to the Webalizer statistics output directory (must has a trailing slash.)', '371', 'text', NULL, 1),
(18, 'servicechk_to', '2', 'Service status timeout period (in seconds) default=2', '372', 'text', NULL, 1),
(19, 'windows_drive', 'C', 'The primary Windows partition letter that ZPanel is installed on.', '373', 'text', NULL, 1),
(20, 'cron_file', '/etc/crontab', 'The location of the crontab file.', '374', 'text', NULL, 1),
(21, 'php_exer', 'php', 'The full system path to the PHP executable.', '375', 'text', NULL, 0),
(22, 'hosted_dir', '/var/zpanel/hostdata/', 'The full system path to the file storage area for user storage space.', '376', 'text', NULL, 1),
(23, 'apache_vhost', '/etc/zpanel/conf/httpd-vhosts.conf', 'The full system patch and filename of the Apache VHOST configuration name.', '377', 'text', NULL, 1),
(24, 'disable_hostsen', 'true', 'For Windows Servers only, this will add a host''s entry on to the server.', '378', 'text', NULL, 1),
(25, 'logfile_dir', '/var/zpanel/logs/domains/', 'Full system path to the Apache log files.', '379', 'text', NULL, 1),
(26, 'directory_index', 'DirectoryIndex index.html index.htm index.php index.asp index.aspx index.jsp index.jspa index.shtml index.shtm', 'Apache directory index line as used the Apache vhost file.', '380', 'text', NULL, 0),
(27, 'php_handler', 'AddType application/x-httpd-php .php\nAddType application/x-httpd-php .php3\nphp_admin_value suhosin.executor.func.blacklist "passthru, show_source, shell_exec, system, pcntl_exec, popen, pclose, proc_open, proc_nice, proc_terminate, proc_get_status, proc_close, leak, apache_child_terminate, posix_kill, posix_mkfifo, posix_setpgid, posix_setsid, posix_setuid, escapeshellcmd, escapeshellarg"\r\n', 'The PHP Handler.', '381', 'text', NULL, 0),
(28, 'cgi_handler', 'AddHandler cgi-script .cgi .pl', 'The CGI Handler', '382', 'text', NULL, 0),
(29, 'vhost_extra', 'ServerSignature Off', 'Extra directives for all apache vhost''s.', '383', 'text', NULL, 0),
(30, 'static_dir', '/etc/zpanel/static/', 'The ZPanel static directory, used for storing welcome pages etc. etc.', '384', 'text', NULL, 0),
(31, 'webalizer_reps', '/etc/zpanel/apps/webalizer/', 'The webalizer reports directory.', '385', 'text', NULL, 1),
(32, 'parking_path', '/etc/zpanel/static/parking/', 'The path to the parking website, this will be used by all clients.', '386', 'text', NULL, 0),
(33, 'hmailserver_db', 'zpanel_postfix', 'The MySQL database name of the hMailServer.', '387', 'text', NULL, 1),
(34, 'hmailserver_et', '2', 'hMailServer account encryption method (2 = Default)', NULL, 'text', NULL, 0),
(35, 'hmailserver_mms', '200', 'The default mailbox storage limit in megabytes. (Default 100MB)', '388', 'text', NULL, 1),
(36, 'filezilla_root', '/etc/zpanel/conf/ftp/', 'The installation directory of where FileZilla server is installed in. (with a trailing slash ''/'')', NULL, 'text', NULL, 0),
(37, 'webalizer_exe', 'webalizer', 'The full path to the webalizer executable.', NULL, 'text', NULL, 0),
(38, 'current_month', '201103', 'The current month number (YYYYMM)', NULL, 'text', NULL, 0),
(39, 'temp_dir', '/var/zpanel/temp', 'The path to the ZPanel temporary directory (with trailing slash)', '389', 'text', NULL, 1),
(40, '7z_exe', 'zip', 'The path and filename of the 7z compression tool.', NULL, 'text', NULL, 0),
(41, 'mysqldump_exe', 'mysqldump', NULL, NULL, 'text', NULL, 0),
(42, 'login_url', 'http://localhost/zpanel/login.php', 'Caches the last know login URL, Speeds up control panel access times.', NULL, 'text', NULL, 0),
(43, 'server_admin', 'ZPanel Developer', 'The name of the server admin', NULL, 'text', NULL, 0),
(44, 'install_date', '1266504603', 'The date that ZPanel was installed.', NULL, 'text', NULL, NULL),
(45, 'last_update', '1304061454', 'The date that ZPanel was last updated.', NULL, 'text', NULL, NULL),
(46, 'zms_host', '', 'The hostname or IP of an active ZPanel Master Server. (For centralised monitoring and reports)', '390', 'text', NULL, 1),
(47, 'auto_ftpuser', 'false', 'Enable automatic creation of a root FTP user account on ZPanel user creation.', '391', 'text', NULL, 1),
(48, 'zpanel_lockdown', '0', 'Enable Zpanel Lockdown, Admins Only', '392', 'text', '', 1),
(49, 'htpasswd_exe', 'htpasswd', 'Path to htpasswd.exe for potecting directories with .htaccess', NULL, 'text', NULL, NULL),
(50, 'lsn_apache', 'apache2', '*NIX Service name for Apache', NULL, 'text', NULL, 0),
(51, 'lsn_proftpd', 'proftpd', '*NIX Service name for ProFTPd', NULL, 'text', NULL, 0),
(52, 'shared_domains', 'no-ip,dydns', 'Domains entered here can be shared across multiple accounts. Seperate domains with , example: no-ip,dydns,test', '399', 'text', NULL, 1),
(53, 'mod_bw', '/etc/zpanel/conf/', 'Path to mod_bw configuration files for packages', NULL, 'text', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `z_vhosts`
--

CREATE TABLE IF NOT EXISTS `z_vhosts` (
  `vh_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `vh_acc_fk` int(6) DEFAULT NULL,
  `vh_name_vc` varchar(255) DEFAULT NULL,
  `vh_directory_vc` varchar(255) DEFAULT NULL,
  `vh_restrict_vc` varchar(255) DEFAULT NULL,
  `vh_type_in` int(1) DEFAULT '1',
  `vh_active_in` int(1) DEFAULT '0',
  `vh_created_ts` int(30) DEFAULT NULL,
  `vh_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`vh_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=83 ;

--
-- Dumping data for table `z_vhosts`
--

