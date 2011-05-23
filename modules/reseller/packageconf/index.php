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
$sql = "SELECT * FROM z_packages WHERE pk_reseller_fk=" . $useraccount['ac_id_pk'] . " AND pk_deleted_ts IS NULL";
$listpackages = DataExchange("r", $z_db_name, $sql);
$rowpackages = mysql_fetch_assoc($listpackages);
$totalpackages = DataExchange("t", $z_db_name, $sql);

echo $lang['82'] . "<br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['83'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'blank')) {
    echo "<br><div class=\"zannouce\">" . $lang['299'] . "</div>";
}
?>
<br><h2><?php echo $lang['104']; ?></h2>
<?php if ($totalpackages > 0) { ?>
    <form id="frmPackages" name="frmPackages" method="post" action="runner.php?load=obj_packages">
        <table class="zgrid">
            <tr>
                <th scope="row"><?php echo $lang['77']; ?></th>
                <th><?php echo $lang['86']; ?></th>
                <th><?php echo $lang['87']; ?></th>
                <th>&nbsp;</th>
            </tr>
            <?php
            do {
                # Lets get the total number of clients using the packages...
                $sql = "SELECT * FROM z_accounts WHERE ac_package_fk=" . $rowpackages['pk_id_pk'] . "";
                $totalclients = DataExchange("t", $z_db_name, $sql);
                ?>
                <tr>
                    <td scope="row"><?php echo Cleaner('o', $rowpackages['pk_name_vc']); ?></td>
                    <td><?php echo date(GetSystemOption('zpanel_df'), $rowpackages['pk_created_ts']); ?></td>
                    <td><?php echo $totalclients; ?></td>
                    <td><input type="submit" name="inEdit_<?php echo $rowpackages['pk_id_pk']; ?>" id="inEdit_<?php echo $rowpackages['pk_id_pk']; ?>" value="<?php echo $lang['85']; ?>" />

        <?php if ($rowpackages['pk_id_pk'] != 1) { ?>
                            <input type="submit" name="inDelete_<?php echo $rowpackages['pk_id_pk']; ?>" id="inDelete_<?php echo $rowpackages['pk_id_pk']; ?>" value="<?php echo $lang['84']; ?>" />

        <?php } ?>
                    </td>
                </tr>
    <?php } while ($rowpackages = mysql_fetch_assoc($listpackages)); ?>
        </table>
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="delete" />
    </form>
<?php
} else {
    echo $lang['231'];
}
?>
<?php if (!isset($_GET['edit'])) { ?>
    <br><h2><?php echo $lang['102']; ?></h2>
    <form id="frmCreatePackage" name="frmCreatePackage" method="post" action="runner.php?load=obj_packages">
        <table class="zform">
            <tr>
                <th><?php echo $lang['77']; ?>:</th>
                <td><input type="text" name="inPackageName" id="inPackageName" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['88']; ?>:</th>
                <td><input type="checkbox" name="inEnablePHP" id="inEnablePHP" value="1" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['89']; ?>:</th>
                <td><input type="checkbox" name="inEnableCGI" id="inEnableCGI" value="1" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['90']; ?>:</th>
                <td><input name="inNoDomains" type="text" id="inNoDomains" value="0" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['91']; ?>:</th>
                <td><input name="inNoSubDomains" type="text" id="inNoSubDomains" value="0" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['92']; ?>:</th>
                <td><input name="inNoParkedDomains" type="text" id="inNoParkedDomains" value="0" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['93']; ?>:</th>
                <td><input name="inNoMailboxes" type="text" id="inNoMailboxes" value="0" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['94']; ?>:</th>
                <td><input name="inNoFowarders" type="text" id="inNoFowarders" value="0" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['95']; ?>:</th>
                <td><input name="inNoDistLists" type="text" id="inNoDistLists" value="0" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['96']; ?>:</th>
                <td><input name="inNoFTPAccounts" type="text" id="inNoFTPAccounts" value="0" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['97']; ?>:</th>
                <td><input name="inNoMySQL" type="text" id="inNoMySQL" value="0" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['98']; ?>:</th>
                <td><input name="inDiskQuota" type="text" id="inDiskQuota" value="0" size="10" maxlength="10" />
    <?php echo $lang['100']; ?></td>
            </tr>
            <tr>
                <th><?php echo $lang['99']; ?>:</th>
                <td><input name="inBandQuota" type="text" id="inBandQuota" value="0" size="10" maxlength="10" />
    <?php echo $lang['100']; ?></td>
            </tr>
            <tr>
                <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="new" /><input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['101']; ?>" /></th>
            </tr>
        </table>
    </form>
<?php
} else {
    # Package is to edit...
    $sql = "SELECT * FROM z_packages WHERE pk_id_pk=" . $_GET['edit'] . " AND pk_deleted_ts IS NULL AND pk_reseller_fk=" . $useraccount['ac_id_pk'] . "";
    $listpackage = DataExchange("r", $z_db_name, $sql);
    $rowpackage = mysql_fetch_assoc($listpackage);
    # Get the list of quotas for the packages...
    $sql = "SELECT * FROM z_quotas WHERE qt_package_fk=" . $_GET['edit'] . "";
    $listquotas = DataExchange("r", $z_db_name, $sql);
    $rowquotas = mysql_fetch_assoc($listquotas);
    ?>
    <br><h2><?php echo $lang['103']; ?></h2>
    <form id="frmEditPackage" name="frmEditPackage" method="post" action="runner.php?load=obj_packages">
        <table class="zform">
            <tr>
                <th><?php echo $lang['77']; ?>:</th>
                <td>
                    <?php if ($rowpackage['pk_id_pk'] != 1) { ?>
                        <input type="text" name="inPackageName" id="inPackageName" value="<?php echo Cleaner('o', $rowpackage['pk_name_vc']) ?>" />
    <?php } else { ?>
                        <input type="text" value="<?php echo Cleaner('o', $rowpackage['pk_name_vc']) ?>" readonly="readonly" disabled="disabled" />
                        <input type="hidden" name="inPackageName" id="inPackageName" value="<?php echo Cleaner('o', $rowpackage['pk_name_vc']) ?>" />
    <?php } ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang['88']; ?>:</th>
                <td><input type="checkbox" name="inEnablePHP" id="inEnablePHP" value="1"<?php if (GetCheckboxValue($rowpackage['pk_enablephp_in']) == 1) {
        echo " checked";
    }; ?>/></td>
            </tr>
            <tr>
                <th><?php echo $lang['89']; ?>:</th>
                <td><input type="checkbox" name="inEnableCGI" id="inEnableCGI" value="1"<?php if (GetCheckboxValue($rowpackage['pk_enablecgi_in']) == 1) {
        echo " checked";
    }; ?> /></td>
            </tr>
            <tr>
                <th><?php echo $lang['90']; ?>:</th>
                <td><input name="inNoDomains" type="text" id="inNoDomains" value="<?php echo Cleaner('o', $rowquotas['qt_domains_in']); ?>" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['91']; ?>:</th>
                <td><input name="inNoSubDomains" type="text" id="inNoSubDomains" value="<?php echo Cleaner('o', $rowquotas['qt_subdomains_in']); ?>" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['92']; ?>:</th>
                <td><input name="inNoParkedDomains" type="text" id="inNoParkedDomains" value="<?php echo Cleaner('o', $rowquotas['qt_parkeddomains_in']); ?>" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['93']; ?>:</th>
                <td><input name="inNoMailboxes" type="text" id="inNoMailboxes" value="<?php echo Cleaner('o', $rowquotas['qt_mailboxes_in']); ?>" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['94']; ?>:</th>
                <td><input name="inNoFowarders" type="text" id="inNoFowarders" value="<?php echo Cleaner('o', $rowquotas['qt_fowarders_in']); ?>" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['95']; ?>:</th>
                <td><input name="inNoDistLists" type="text" id="inNoDistLists" value="<?php echo Cleaner('o', $rowquotas['qt_distlists_in']); ?>" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['96']; ?>:</th>
                <td><input name="inNoFTPAccounts" type="text" id="inNoFTPAccounts" value="<?php echo Cleaner('o', $rowquotas['qt_ftpaccounts_in']); ?>" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['97']; ?>:</th>
                <td><input name="inNoMySQL" type="text" id="inNoMySQL" value="<?php echo Cleaner('o', $rowquotas['qt_mysql_in']); ?>" size="5" maxlength="3" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['98']; ?>:</th>
                <td><input name="inDiskQuota" type="text" id="inDiskQuota" value="<?php echo Cleaner('o', ($rowquotas['qt_diskspace_bi'] / 1024000)); ?>" size="10" maxlength="10" />
    <?php echo $lang['100']; ?></td>
            </tr>
            <tr>
                <th><?php echo $lang['99']; ?>:</th>
                <td><input name="inBandQuota" type="text" id="inBandQuota" value="<?php echo Cleaner('o', ($rowquotas['qt_bandwidth_bi'] / 1024000)); ?>" size="10" maxlength="10" />
    <?php echo $lang['100']; ?></td>
            </tr>
            <tr>
                <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inPackageID" value="<?php echo $rowpackage['pk_id_pk']; ?>" /><input type="hidden" name="inAction" value="edit" /><input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['101']; ?>" /></th>
            </tr>
        </table>
    </form>
<?php } ?>