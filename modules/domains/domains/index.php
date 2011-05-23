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

$sql = "SELECT * FROM z_vhosts WHERE vh_acc_fk=" . $useraccount['ac_id_pk'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1";
$listdomains = DataExchange("r", $z_db_name, $sql);
$rowdomains = mysql_fetch_assoc($listdomains);
$totaldomains = DataExchange("t", $z_db_name, $sql);

echo $lang['155'] . "<br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['156'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'alreadyexists')) {
    echo "<br><div class=\"zannouce\">" . $lang['157'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'error')) {
    echo "<br><div class=\"zannouce\">" . $lang['291'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'blank')) {
    echo "<br><div class=\"zannouce\">" . $lang['292'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'badname')) {
    echo "<br><div class=\"zannouce\">" . $lang['293'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'nosub')) {
    echo "<br><div class=\"zannouce\">" . $lang['294'] . "</div>";
}
echo "<br><h2>" . $lang['158'] . "</h2>";
if ($totaldomains > 0) {
    ?><form id="frmDomains" name="frmDomains" method="post" action="runner.php?load=obj_vhosts">
        <table class="zgrid">
            <tr>
                <th><?php echo $lang['161']; ?></th>
                <th><?php echo $lang['162']; ?></th>
                <th><?php echo $lang['163']; ?></th>
                <th></th>
            </tr>
            <?php do { ?>
                <tr>
                    <td><?php echo Cleaner('o', $rowdomains['vh_name_vc']); ?></td>
                    <td><?php echo Cleaner('o', $rowdomains['vh_directory_vc']); ?></td>
                    <td><?php
        if ($rowdomains['vh_active_in'] == 1) {
            echo "<font color=\"green\">Live</font>";
        } else {
            echo "<font color=\"orange\">Pending</font>";
        }
                ?></td>
                    <td><input type="submit" name="inDelete_<?php echo $rowdomains['vh_id_pk']; ?>" id="inDelete_<?php echo $rowdomains['vh_id_pk']; ?>" value="<?php echo $lang['84']; ?>" /></td>
                </tr>
    <?php } while ($rowdomains = mysql_fetch_assoc($listdomains)); ?>
        </table>
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="delete" />
    </form>
    <?php
} else {
    echo $lang['160'];
}
if ($quotainfo['qt_domains_in'] > GetQuotaUsages('domains', $useraccount['ac_id_pk'])) {
    echo "<br><h2>" . $lang['159'] . "</h2>";
    ?>
    <form id="frmNewDomain" name="frmNewDomain" method="post" action="runner.php?load=obj_vhosts">
        <table class="zform">
            <tr>
                <th><?php echo $lang['161']; ?>:</th>
                <td><input name="inDomain" type="text" id="inDomain" size="30" /></td>
            </tr>
            <tr>
                <th><?php echo $lang['162']; ?>:</th>
                <td><input name="inAutoHome" type="checkbox" id="inAutoHome" value="1" CHECKED />
    <?php echo $lang['164']; ?></td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td><?php echo $lang['165']; ?>: 
                    <select name="inDestination" id="inDestination">
                        <option value="">/ (root)</option>
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
                    <input type="hidden" name="inAction" value="NewDomain" />
                    <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['128']; ?>" /></th>
            </tr>
        </table>
    </form><?php } ?>