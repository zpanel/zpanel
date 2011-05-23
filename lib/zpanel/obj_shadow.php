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

# Lets get database ID's for all accounts.
if ($permissionset['pr_admin_in'] == 1) {
    $sql = "SELECT * FROM z_accounts WHERE ac_deleted_ts IS NULL";
} else {
    $sql = "SELECT * FROM z_accounts WHERE ac_reseller_fk=" . $useraccount['ac_id_pk'] . " AND ac_deleted_ts IS NULL";
}
$listclients = DataExchange("r", $z_db_name, $sql);
$rowclients = mysql_fetch_assoc($listclients);
$totalRows_clients = DataExchange("t", $z_db_name, $sql);
if ($totalRows_clients > 0) {
    # We now need to check which client we are going to shadow...
    do {
        # See if this client has been selected to be shadowed...
        if (isset($_POST['inShadow_' . $rowclients['ac_id_pk']])) {

            # Call the API!
            zapi_shadow_user($rowclients['ac_user_vc'], $rowclients['ac_id_pk'], $useraccount['ac_user_vc']);
        }
    } while ($rowclients = mysql_fetch_assoc($listclients));
}
header("location: " . GetNormalModuleURL($returnurl) . "");
exit;
?>