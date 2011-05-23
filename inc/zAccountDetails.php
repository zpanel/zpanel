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
session_start();
include('conf/zcnf.php');
include_once('inc/zDataExchange.php');
if (isset($_SESSION['zUserID'])) {
    # User is logged in, lets gather account details for the account.
    $useraccount = DataExchange("l", $z_db_name, "SELECT * FROM z_accounts WHERE ac_id_pk=" . $_SESSION['zUserID'] . " AND ac_deleted_ts IS NULL");
    # Gather package infomation for the account.
    $packageinfo = DataExchange("l", $z_db_name, "SELECT * FROM z_packages WHERE pk_id_pk=" . $useraccount['ac_package_fk'] . "");
    # Gather quota infomation for the account.
    $quotainfo = DataExchange("l", $z_db_name, "SELECT * FROM z_quotas WHERE qt_package_fk=" . $useraccount['ac_package_fk'] . "");
    # Gather personal infomation for the account.
    $personalinfo = DataExchange("l", $z_db_name, "SELECT * FROM z_personal WHERE ap_acc_fk=" . $useraccount['ac_id_pk'] . "");
    # Gather permissions for the account.
    $permissionset = DataExchange("l", $z_db_name, "SELECT * FROM z_permissions WHERE pr_package_fk=" . $useraccount['ac_package_fk'] . "");
} else {
    # Event for when user is not logged in!	
}
?>