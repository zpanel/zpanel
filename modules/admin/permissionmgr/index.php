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
include('conf/zcnf.php');
include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
include('inc/zAccountDetails.php');
# Now we have to get a list of the packages and display current permissions for each package.
$sql = "SELECT * FROM z_permissions";
$listpermissions = DataExchange("r", $z_db_name, $sql);
$rowpermissions = mysql_fetch_assoc($listpermissions);

echo $lang['76'];
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><br><div class=\"zannouce\">" . $lang['81'] . "</div>";
}
echo "<br><br>";
?>
<form id="frmPermissions" name="frmPermissions" method="post" action="runner.php?load=obj_permissions">
    <table class="zgrid">
        <tr>
            <th><?php echo $lang['77']; ?></th>
            <th><?php echo $lang['78']; ?></th>
        <?php if ($_SESSION['zUserID'] == 2) {
            echo "<th>" . $lang['79'] . "</th>";
        } ?>
        </tr>
        <?php
        do {
            # Get the package name...
            $sql = "SELECT * FROM z_packages WHERE pk_id_pk = " . $rowpermissions['pr_package_fk'] . " AND pk_deleted_ts IS NULL";
            $package = DataExchange("l", $z_db_name, $sql);
            if ($package['pk_reseller_fk'] == $useraccount['ac_id_pk']) {
                ?>
                <tr>
                    <td><?php echo $package['pk_name_vc']; ?></td>
                    <td><input name="inIsReseller_<?php echo $rowpermissions['pr_id_pk']; ?>" type="checkbox" id="inIsReseller_<?php echo $rowpermissions['pr_id_pk']; ?>" value="1"<?php if (GetCheckboxValue($rowpermissions['pr_reseller_in']) == 1) {
                    echo " checked";
                }; ?> /></td>

                        <?php if ($_SESSION['zUserID'] == 2) { ?>
                        <td>
                        <?php if ($rowpermissions['pr_id_pk'] != 1) { ?>
                                <input name="inIsAdmin_<?php echo $rowpermissions['pr_id_pk']; ?>" type="checkbox" id="inIsAdmin_<?php echo $rowpermissions['pr_id_pk']; ?>" value="1"<?php if (GetCheckboxValue($rowpermissions['pr_admin_in']) == 1) {
                    echo " checked";
                }; ?> />
            <?php } else { ?>
                                <input name="inIsAdmin_<?php echo $rowpermissions['pr_id_pk']; ?>" type="hidden" id="inIsAdmin_<?php echo $rowpermissions['pr_id_pk']; ?>" value="1" />
            <?php } ?>
                        </td>
        <?php } ?>

                </tr>
    <?php };
} while ($rowpermissions = mysql_fetch_assoc($listpermissions)); ?>
        <tr>
            <td colspan="4" align="right" scope="row"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['18']; ?>" /></td>
        </tr>
    </table>
</form>
