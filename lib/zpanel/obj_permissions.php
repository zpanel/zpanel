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
# Now we turn the values that are avaliable to be saved...
$sql = "SELECT * FROM z_permissions";
$listpermissions = DataExchange("r", $z_db_name, $sql);
$rowpermissions = mysql_fetch_assoc($listpermissions);

do {
    $rowid = $rowpermissions['pr_id_pk'];
    # Show only the packages user has access to...
    $sql = "SELECT * FROM z_packages WHERE pk_id_pk = " . $rowpermissions['pr_package_fk'] . " AND pk_deleted_ts IS NULL";
    $package = DataExchange("l", $z_db_name, $sql);
    if ($package['pk_reseller_fk'] == $useraccount['ac_id_pk']) {
        # Check to ensure that the permissions set hasnt been deleted...
        DataExchange("w", $z_db_name, "UPDATE z_permissions SET pr_reseller_in='" . GetCheckboxValue($_POST['inIsReseller_' . $rowid . '']) . "' WHERE pr_id_pk = '" . $rowid . "'");
        DataExchange("w", $z_db_name, "UPDATE z_permissions SET pr_admin_in='" . GetCheckboxValue($_POST['inIsAdmin_' . $rowid . '']) . "' WHERE pr_id_pk = '" . $rowid . "'");
    }
} while ($rowpermissions = mysql_fetch_assoc($listpermissions));
$returnurl = GetNormalModuleURL($returnurl) . "&r=ok";
header("location: " . $returnurl . "&r=ok");
exit;
?>