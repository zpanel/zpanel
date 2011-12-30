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
$sql = "SELECT * FROM z_accounts WHERE ac_reseller_fk=" . $useraccount['ac_id_pk'] . " AND ac_deleted_ts IS NULL";
$listclients = DataExchange("r", $z_db_name, $sql);
$rowclients = mysql_fetch_assoc($listclients);
$totalclients = DataExchange("t", $z_db_name, $sql);

$sql = "SELECT * FROM z_packages WHERE pk_reseller_fk=" . $useraccount['ac_id_pk'] . " AND pk_deleted_ts IS NULL";
$listpackages = DataExchange("r", $z_db_name, $sql);
$rowpackages = mysql_fetch_assoc($listpackages);

echo $lang['106'] . "<br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'blank')) {
    echo "<br><div class=\"zannouce\">" . $lang['297'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'error')) {
    echo "<br><div class=\"zannouce\">" . $lang['302'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['105'] . "</div>";
}
echo "<br><h2>" . $lang['107'] . "</h2>";
if ($totalclients > 0) {
    ?>
    <form id="frmClients" name="frmClients" method="post" action="runner.php?load=obj_clients">
        <table class="zgrid">
            <tr>
                <th><?php echo $lang['109']; ?></th>
                <th><?php echo $lang['110']; ?></th>
                <th><?php echo $lang['111']; ?></th>
                <th><?php echo $lang['112']; ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            <?php
            do {
                # Get package infomation for the user...
                $sql = "SELECT pk_name_vc FROM z_packages WHERE pk_id_pk=" . $rowclients['ac_package_fk'] . "";
                $package = DataExchange("l", $z_db_name, $sql);
                ?>
                <tr>
                    <td><?php echo Cleaner('o', $rowclients['ac_user_vc']); ?></td>
                    <td><?php echo $package['pk_name_vc']; ?></td>
                    <td><?php echo FormatFileSize(GetQuotaUsages('diskspace', $rowclients['ac_id_pk'])); ?></td>
                    <td><?php echo FormatFileSize(GetQuotaUsages('bandwidth', $rowclients['ac_id_pk'])); ?></td>
                    <td><input type="submit" name="inEdit_<?php echo $rowclients['ac_id_pk']; ?>" id="inEdit_<?php echo $rowclients['ac_id_pk']; ?>" value="<?php echo $lang['85']; ?>" />

                        <?php if ($rowclients['ac_id_pk'] != 2) { ?>
                            <input type="submit" name="inDelete_<?php echo $rowclients['ac_id_pk']; ?>" id="inDelete_<?php echo $rowclients['ac_id_pk']; ?>" value="<?php echo $lang['84']; ?>" />
        <?php } ?>
                    </td>
                    <td>
                        <?php if($rowclients['ac_id_pk'] != 2) { ?>
                        <form action="runner.php?load=obj_shadow" method="post" name="frmShadow" id="frmShadow">
                        <input type="submit" name="inShadow_<?php echo $rowclients['ac_id_pk']; ?>" id="inShadow_<?php echo $rowclients['ac_id_pk']; ?>" value="<?php echo $lang['236']; ?>" />
                        </form>
                        <?php } ?>
                        </td>
                </tr>
    <?php } while ($rowclients = mysql_fetch_assoc($listclients)); ?>
        </table>
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="delete" />
    </form>
<?php
} else {
    echo $lang['113'];
}
if (!isset($_GET['edit'])) {
    echo "<br><h2>" . $lang['108'] . "</h2>";
    ?>
    <form id="frmClients" name="frmClients" method="post" action="runner.php?load=obj_clients">
        <table class="zform">
            <tr>
                <th><?php echo $lang['109']; ?>:</th>
                <td><input type="text" name="inUserName" id="inUserName" maxlength="10" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['116']; ?>:</th>
                <td><input type="text" name="inPassword" id="inPassword" value="<?php echo GenerateRandomPassword(9, 4); ?>" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['110']; ?>:</th>
                <td><select name="inPackage" id="inPackage">
                        <option value="" selected="selected">-- <?php echo $lang['114']; ?> --</option>
    <?php do { ?>
                            <option value="<?php echo $rowpackages['pk_id_pk']; ?>"><?php echo $rowpackages['pk_name_vc']; ?></option>
    <?php } while ($rowpackages = mysql_fetch_assoc($listpackages)); ?>
                    </select></td>
            </tr>
            <tr>
                <th><?php echo $lang['13']; ?></th>
                <td><input type="text" name="inFullName" id="inFullName" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['14']; ?></th>
                <td><input type="text" name="inEmailAddress" id="inEmailAddress" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['15']; ?></th>
                <td><textarea name="inAddress" id="inAddress" cols="45" rows="5"></textarea></td>
            </tr>
            <tr>
                <th><?php echo $lang['16']; ?></th>
                <td><input name="inPostCode" type="text" id="inPostCode" value="" size="20" maxlength="10" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['17']; ?></th>
                <td><input name="inPhone" type="text" id="inPhone" value="" size="20" maxlength="50" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['223']; ?></th>
                <td><input name="inSWE" type="checkbox" id="inSWE" value="1" checked="checked" /></td>
            </tr>
            <tr>
                <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                    <input type="hidden" name="inAction" value="new" />
                    <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['101']; ?>" /></th>
            </tr>
        </table>
    </form>
<?php
} else {
    # Client to edit..
    $sql = "SELECT * FROM z_accounts WHERE ac_id_pk=" . $_GET['edit'] . " AND ac_deleted_ts IS NULL AND ac_reseller_fk=" . $useraccount['ac_id_pk'] . "";
    $listclient = DataExchange("r", $z_db_name, $sql);
    $rowclient = mysql_fetch_assoc($listclient);
    # Get the client's personal data...
    $sql = "SELECT * FROM z_personal WHERE ap_acc_fk=" . $rowclient['ac_id_pk'] . "";
    $listpersonal = DataExchange("r", $z_db_name, $sql);
    $rowpersonal = mysql_fetch_assoc($listpersonal);
    ?>
    <br><h2><?php echo $lang['115']; ?></h2>
    <form id="frmClients" name="frmClients" method="post" action="runner.php?load=obj_clients">
        <table class="zform">
            <tr>
                <th><?php echo $lang['109']; ?>:</th>
                <td><input name="inUserName" type="text" disabled="disabled" maxlength="10" id="inUserName" value="<?php echo Cleaner('o', $rowclient['ac_user_vc']); ?>" readonly="readonly" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['110']; ?>:</th>
                <td>

                        <?php if ($rowclient['ac_id_pk'] != 2) { ?>
                        <select name="inPackage" id="inPackage">  
                        <?php do { ?>
                                <option value="<?php echo $rowpackages['pk_id_pk']; ?>"<?php if ($rowpackages['pk_id_pk'] == $rowclient['ac_package_fk']) {
                    echo " selected ";
                }; ?>><?php echo $rowpackages['pk_name_vc']; ?></option>
        <?php } while ($rowpackages = mysql_fetch_assoc($listpackages)); ?>
                        </select>
    <?php } else { ?>
                        <input type="text" disabled="disabled" maxlength="10" value="<?php echo $rowpackages['pk_name_vc']; ?>" readonly="readonly" />
                        <input type="hidden" name="inPackage" id="inPackage" value="<?php echo $rowpackages['pk_id_pk']; ?>" />
    <?php } ?>

                </td>
            </tr>
            <tr>
                <th><?php echo $lang['13']; ?></th>
                <td><input type="text" name="inFullName" id="inFullName" value="<?php echo Cleaner('o', $rowpersonal['ap_fullname_vc']); ?>" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['14']; ?></th>
                <td><input type="text" name="inEmailAddress" id="inEmailAddress" value="<?php echo Cleaner('o', $rowpersonal['ap_email_vc']); ?>" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['15']; ?></th>
                <td><textarea name="inAddress" id="inAddress" cols="45" rows="5"><?php echo Cleaner('o', $rowpersonal['ap_address_tx']); ?></textarea></td>
            </tr>
            <tr>
                <th><?php echo $lang['16']; ?></th>
                <td><input name="inPostCode" type="text" id="inPostCode" size="20" maxlength="10" value="<?php echo Cleaner('o', $rowpersonal['ap_postcode_vc']); ?>" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['17']; ?></th>
                <td><input name="inPhone" type="text" id="inPhone" size="20" maxlength="50" value="<?php echo Cleaner('o', $rowpersonal['ap_phone_vc']); ?>" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['183']; ?>:</th>
                <td><input name="inNewPassword" type="password" id="inNewPassword" size="20" maxlength="50" /> 
                </td>
            </tr>
            <tr>
                <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                    <input type="hidden" name="inClientID" value="<?php echo $rowclient['ac_id_pk']; ?>" />
                    <input type="hidden" name="inAction" value="edit" />
                    <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['101']; ?>" /></th>
            </tr>
        </table>
    </form>
<?php } ?>