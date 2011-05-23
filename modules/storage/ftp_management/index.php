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

$sql = "SELECT * FROM z_ftpaccounts WHERE ft_acc_fk=" . $useraccount['ac_id_pk'] . " AND ft_deleted_ts IS NULL";
$listftpaccounts = DataExchange("r", $z_db_name, $sql);
$rowftpaccounts = mysql_fetch_assoc($listftpaccounts);
$totalftpaccounts = DataExchange("t", $z_db_name, $sql);

echo $lang['208'] . "<br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['212'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'exists')) {
    echo "<br><div class=\"zannouce\">" . $lang['211'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'blank')) {
    echo "<br><div class=\"zannouce\">" . $lang['295'] . "</div>";
}
echo "<br><h2>" . $lang['205'] . "</h2>";
if ($totalftpaccounts > 0) {
    ?><form id="frmFTPAccounts" name="frmFTPAccounts" method="post" action="runner.php?load=obj_ftpaccounts">
        <table class="zgrid">
            <tr>
                <th><?php echo $lang['209']; ?></th>
                <th><?php echo $lang['162']; ?></th>
                <th><?php echo $lang['210']; ?></th>
                <th></th>
            </tr>
            <?php do { ?>
                <tr>
                    <td><?php echo Cleaner('o', $rowftpaccounts['ft_user_vc']); ?></td>
                    <td><?php echo Cleaner('o', $rowftpaccounts['ft_directory_vc']); ?></td>
                    <td><?php echo Cleaner('o', $rowftpaccounts['ft_access_vc']); ?></td>
                    <td><input type="submit" name="inReset_<?php echo $rowftpaccounts['ft_id_pk']; ?>" id="inReset_<?php echo $rowftpaccounts['ft_id_pk']; ?>" value="<?php echo $lang['183']; ?>" /><input type="submit" name="inDelete_<?php echo $rowftpaccounts['ft_id_pk']; ?>" id="inDelete_<?php echo $rowftpaccounts['ft_id_pk']; ?>" value="<?php echo $lang['84']; ?>" /></td>
                </tr>
            <?php } while ($rowftpaccounts = mysql_fetch_assoc($listftpaccounts)); ?>
        </table>
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="delete" />
    </form>
    <?php
} else {
    echo $lang['207'];
}
if (($quotainfo['qt_ftpaccounts_in'] > GetQuotaUsages('ftpaccounts', $useraccount['ac_id_pk'])) && (!isset($_GET['reset']))) {
    echo "<br><h2>" . $lang['206'] . "</h2>";
    ?>
    <form id="frmNewFTPAccount" name="frmNewFTPAccount" method="post" action="runner.php?load=obj_ftpaccounts">
        <table class="zform">
            <tr>
                <th><?php echo $lang['109']; ?>:</th>
                <td><input name="inUsername" type="text" id="inUsername" size="30" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['116']; ?>:</th>
                <td><input name="inPassword" type="password" id="inPassword" size="30" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['213']; ?>:</th>
                <td><select name="inAccess" size="1">
                        <option value="RO" selected="selected">Read-only</option>
                        <option value="WO">Write-only</option>
                        <option value="RW">Full access</option>
                    </select></td>
            </tr>
            <tr>
                <th><?php echo $lang['162']; ?>:</th>
                <td><input name="inAutoHome" type="checkbox" id="inAutoHome" value="1" checked="checked" />
    <?php echo $lang['164']; ?></td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td><?php echo $lang['165']; ?>: 
                    <select name="inDestination" id="inDestination">
                        <option value="">/ (Default)</option>
                        <?php
                        $handle = @opendir(GetSystemOption('hosted_dir') . $useraccount['ac_user_vc']);
                        $chkdir = GetSystemOption('hosted_dir') . $useraccount['ac_user_vc'] . "/";
                        if (!$handle) {
                            # Log an error as the folder cannot be opened...
                            TriggerLog($useraccount['ac_id_pk'], $b = "Was unable to read the folders in (" . $chkdir . "), please ensure this folder exists.");
                        } else {
                            while ($file = readdir($handle)) {
                                if ($file != "." && $file != "..") {
                                    if (is_dir($chkdir . $file)) {
                                        echo "<option value=" . $file . ">/" . $file . "</option>\n";
                                    }
                                }
                            }
                            closedir($handle);
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                    <input type="hidden" name="inAction" value="NewFTPAccount" />
                    <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['128']; ?>" /></th>
            </tr>
        </table>
    </form><?php } ?>
<?php if (isset($_GET['reset'])) {
    echo "<br><h2>" . $lang['183'] . "</h2>"; ?>
    <form id="frmResetPassword" name="frmResetPassword" method="post" action="runner.php?load=obj_ftpaccounts">
        <table class="zform">
            <tr>
                <th><?php echo $lang['109']; ?>:</th>
                <td><?php echo Cleaner('o', $_GET['reset']); ?></td>
            </tr>
            <tr>
                <th><?php echo $lang['24']; ?></th>
                <td><input name="inPassword" type="password" id="inPassword" size="30" /></td>
            </tr>
            <tr>
                <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                    <input type="hidden" name="inAccount" value="<?php echo $_GET['reset']; ?>" />
                    <input type="hidden" name="inAction" value="reset" />
                    <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['183']; ?>" /></th>
            </tr>
        </table>
    </form>
<?php } ?>