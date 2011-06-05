<?php

/**
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 * 
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@zpanelcp.com
 * @copyright (c) 2008-2011 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (ZPanel) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
# Now we need to declare and cleanup some variables
$acc_fk = $useraccount['ac_id_pk'];
$returnurl = Cleaner('o', $_POST['inReturn']);

# Lets get database ID's for all packages.
$sql = "SELECT * FROM z_packages WHERE pk_reseller_fk=" . $useraccount['ac_id_pk'] . " AND pk_deleted_ts IS NULL";
$listpackages = DataExchange("r", $z_db_name, $sql);
$rowpackages = mysql_fetch_assoc($listpackages);

if ($_POST['inAction'] == 'new') {
    # Check to make sure the packagename is not blank before we go any further...
    if ($_POST['inPackageName'] == '') {
        header("location: " . GetNormalModuleURL($returnurl) . "&r=blank");
        exit;
    }
	# If the user submitted a 'new' request then we will simply add the package to the database...
    $sql = "INSERT INTO z_packages (pk_reseller_fk,
									pk_name_vc,
									pk_enablephp_in,
									pk_enablecgi_in,
									pk_created_ts) VALUES (
									" . $acc_fk . ",
									'" . Cleaner('i', $_POST['inPackageName']) . "',
									" . GetCheckboxValue($_POST['inEnablePHP']) . ",
									" . GetCheckboxValue($_POST['inEnableCGI']) . ",
									" . time() . ");";
    DataExchange("w", $z_db_name, $sql);
    # Now lets pull back the package ID so we can use it in the other tables we are about to manipulate.
    $sql = "SELECT * FROM z_packages WHERE pk_reseller_fk=" . $acc_fk . " ORDER BY pk_id_pk DESC";
    $packageid = DataExchange("l", $z_db_name, $sql);

    $sql = "INSERT INTO z_quotas (qt_package_fk,
									qt_domains_in,
									qt_subdomains_in,
									qt_parkeddomains_in,
									qt_mailboxes_in,
									qt_fowarders_in,
									qt_distlists_in,
									qt_ftpaccounts_in,
									qt_mysql_in,
									qt_diskspace_bi,
									qt_bandwidth_bi) VALUES (
									" . $packageid['pk_id_pk'] . ",
									" . Cleaner('i', $_POST['inNoDomains']) . ",
									" . Cleaner('i', $_POST['inNoSubDomains']) . ",
									" . Cleaner('i', $_POST['inNoParkedDomains']) . ",
									" . Cleaner('i', $_POST['inNoMailboxes']) . ",
									" . Cleaner('i', $_POST['inNoFowarders']) . ",
									" . Cleaner('i', $_POST['inNoDistLists']) . ",
									" . Cleaner('i', $_POST['inNoFTPAccounts']) . ",
									" . Cleaner('i', $_POST['inNoMySQL']) . ",
									" . Cleaner('i', ($_POST['inDiskQuota'] * 1024000)) . ",
									" . Cleaner('i', ($_POST['inBandQuota'] * 1024000)) . ")";
    DataExchange("w", $z_db_name, $sql);
    $sql = "INSERT INTO z_permissions (pr_package_fk) VALUES (" . $packageid['pk_id_pk'] . ");";
    DataExchange("w", $z_db_name, $sql);
	
	# Insert default mod_bw quota limits for package
	$sql = "SELECT * FROM z_throttle WHERE tr_id_pk=1";
    $throttledefaults = DataExchange("l", $z_db_name, $sql);
	$sql = "UPDATE z_quotas SET qt_bwenabled_in = '".$throttledefaults['tr_bwenabled_in']."',
								qt_dlenabled_in = '".$throttledefaults['tr_dlenabled_in']."',
								qt_totalbw_fk   = '".$throttledefaults['tr_totalbw_fk']."',
								qt_minbw_fk     = '".$throttledefaults['tr_minbw_fk']."',
								qt_maxcon_fk    = '".$throttledefaults['tr_maxcon_fk']."',
								qt_filesize_fk  = '".$throttledefaults['tr_filespeed_fk']."',
								qt_filespeed_fk = '".$throttledefaults['tr_filespeed_fk']."',
								qt_filetype_vc  = '".$throttledefaults['tr_filetype_vc']."',
								qt_modified_in  = '1'
								WHERE qt_package_fk  = '".$packageid['pk_id_pk']."'";
							  
	DataExchange("w",$z_db_name,$sql);

    header("location: " . $returnurl . "&r=ok");
    exit;
}


if ($_POST['inAction'] == 'edit') {
	# User has choosen to edit a package...
    $sql = "UPDATE z_packages SET pk_name_vc='" . Cleaner('i', $_POST['inPackageName']) . "',
								pk_enablephp_in=" . GetCheckboxValue($_POST['inEnablePHP']) . ",
								pk_enablecgi_in=" . GetCheckboxValue($_POST['inEnableCGI']) . " WHERE pk_id_pk=" . $_POST['inPackageID'] . "";
    DataExchange("w", $z_db_name, $sql);
    $sql = "UPDATE z_quotas SET qt_domains_in=" . Cleaner('i', $_POST['inNoDomains']) . ",
									qt_subdomains_in=" . Cleaner('i', $_POST['inNoSubDomains']) . ",
									qt_parkeddomains_in=" . Cleaner('i', $_POST['inNoParkedDomains']) . ",
									qt_mailboxes_in=" . Cleaner('i', $_POST['inNoMailboxes']) . ",
									qt_fowarders_in=" . Cleaner('i', $_POST['inNoFowarders']) . ",
									qt_distlists_in=" . Cleaner('i', $_POST['inNoDistLists']) . ",
									qt_ftpaccounts_in=" . Cleaner('i', $_POST['inNoFTPAccounts']) . ",
									qt_mysql_in=" . Cleaner('i', $_POST['inNoMySQL']) . ",
									qt_diskspace_bi=" . Cleaner('i', ($_POST['inDiskQuota'] * 1024000)) . ",
									qt_bandwidth_bi=" . Cleaner('i', ($_POST['inBandQuota'] * 1024000)) . " WHERE qt_package_fk=" . $_POST['inPackageID'] . "";
    DataExchange("w", $z_db_name, $sql);
    $returnurl = GetNormalModuleURL($returnurl) . "&r=ok";
    header("location: " . $returnurl . "");
    exit;
}

if ($_POST['inAction'] == 'delete') {
	# User has choosen to delete a package...
    do {
        if (isset($_POST['inEdit_' . $rowpackages['pk_id_pk']])) {
            header("location: " . $returnurl . "&edit=" . $rowpackages['pk_id_pk'] . "");
            exit;
        }
        if (isset($_POST['inDelete_' . $rowpackages['pk_id_pk']])) {
            $sql = "UPDATE z_packages SET pk_deleted_ts=" . time() . " WHERE pk_id_pk=" . $rowpackages['pk_id_pk'] . "";
            $packageid = DataExchange("w", $z_db_name, $sql);
        }
    } while ($rowpackages = mysql_fetch_assoc($listpackages));
    header("location: " . GetNormalModuleURL($returnurl) . "&r=ok");
    exit;
}
?>