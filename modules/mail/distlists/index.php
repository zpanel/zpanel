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

$sql = "SELECT * FROM z_distlists WHERE dl_acc_fk=" . $useraccount['ac_id_pk'] . " AND dl_deleted_ts IS NULL";
$listdistlists = DataExchange("r", $z_db_name, $sql);
$rowdistlists = mysql_fetch_assoc($listdistlists);
$totaldistlists = DataExchange("t", $z_db_name, $sql);

$sql = "SELECT * FROM z_vhosts WHERE vh_acc_fk=" . $useraccount['ac_id_pk'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1";
$listexdomains = DataExchange("r", $z_db_name, $sql);
$rowexdomains = mysql_fetch_assoc($listexdomains);
$totalexdomains = DataExchange("t", $z_db_name, $sql);

echo $lang['204'] . "<br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['190'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'nodomain')) {
    echo "<br><div class=\"zannouce\">" . $lang['191'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'exists')) {
    echo "<br><div class=\"zannouce\">" . $lang['180'] . "</div>";
}
echo "<br><h2>" . $lang['201'] . "</h2>";
if ($totaldistlists > 0) {
    ?><form id="frmDistLists" name="frmDistLists" method="post" action="runner.php?load=obj_mail">
        <table class="zgrid">
            <tr>
                <th><?php echo $lang['181']; ?></th>
                <th></th>
            </tr>
            <?php do { ?>
                <tr>
                    <td><?php echo Cleaner('o', $rowdistlists['dl_address_vc']); ?></td>
                    <td><input type="submit" name="inEdit_<?php echo $rowdistlists['dl_id_pk']; ?>" id="inEdit_<?php echo $rowdistlists['dl_id_pk']; ?>" value="<?php echo $lang['85']; ?>" /><input type="submit" name="inDelete_<?php echo $rowdistlists['dl_id_pk']; ?>" id="inDelete_<?php echo $rowdistlists['dl_id_pk']; ?>" value="<?php echo $lang['84']; ?>" /></td>
                </tr>
            <?php } while ($rowdistlists = mysql_fetch_assoc($listdistlists)); ?>
        </table>
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="delete_distlist" />
    </form>
    <?php
} else {
    echo $lang['203'];
}
if ($quotainfo['qt_distlists_in'] > GetQuotaUsages('distlists', $useraccount['ac_id_pk'])) {
    echo "<br><h2>" . $lang['202'] . "</h2>";
    ?>
    <?php if (GetQuotaUsages('domains', $useraccount['ac_id_pk']) > 0) { ?>
        <form id="frmNewDistList" name="frmNewDistList" method="post" action="runner.php?load=obj_mail">
            <table class="zform">
                <tr>
                    <th><?php echo $lang['14']; ?></th>
                    <td><input name="inAddress" type="text" id="inAddress" />
                        <select name="inDomain" id="inDomain">
                            <option value="">-- <?php echo $lang['29']; ?> --</option>
                            <?php
                            do {
                                echo "<option value=\"" . $rowexdomains['vh_name_vc'] . "\">@" . $rowexdomains['vh_name_vc'] . "</option>";
                            } while ($rowexdomains = mysql_fetch_assoc($listexdomains));
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                        <input type="hidden" name="inAction" value="NewDistList" />
                        <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['128']; ?>" /></th>
                </tr>
            </table>
        </form><?php
                } else {
                    echo $lang['234'];
                }
            }
# Check the see if the user wants to edit the dist list in queston...
            if (isset($_GET['edit'])) {
                # Get a list of the dist list users....
                $sql = "SELECT * FROM z_distlists WHERE dl_acc_fk=" . $useraccount['ac_id_pk'] . " AND dl_address_vc='" . Cleaner('i', $_GET['edit']) . "' AND dl_deleted_ts IS NULL";
                $listdistlist = DataExchange("r", $z_db_name, $sql);
                $rowdistlist = mysql_fetch_assoc($listdistlist);
                $totaldistlist = DataExchange("t", $z_db_name, $sql);

                $sql = "SELECT * FROM z_distlistusers WHERE du_distlist_fk=" . $rowdistlist['dl_id_pk'] . " AND du_deleted_ts IS NULL";
                $listdistlistusers = DataExchange("r", $z_db_name, $sql);
                $rowdistlistusers = mysql_fetch_assoc($listdistlistusers);
                $totaldistlistusers = DataExchange("t", $z_db_name, $sql);

                echo "<br><h2>Edit distrubution list</h2>";
                echo "<form id=\"frmNewDistListUser\" name=\"frmNewDistListUser\" method=\"post\" action=\"runner.php?load=obj_mail\">
	<table class=\"zform\">
	<tr>
    <th colspan=\"3\">" . $rowdistlist['dl_address_vc'] . "</th>
  	</tr>
	  <tr>
    <th>&nbsp;</th>
    <td>&nbsp;</td>
	<td>&nbsp;</td>
  </tr>";
                if ($rowdistlistusers > 0) {
                    do {
                        echo "<tr>
    <th>Email Address:</th><td>" . $rowdistlistusers['du_address_vc'] . "</td>
    <td><input type=\"submit\" name=\"inDelete_" . $rowdistlistusers['du_id_pk'] . "\" id=\"inDelete_" . $rowforwarders['du_id_pk'] . "\" value=\"" . $lang['84'] . "\" /></td>
  </tr>";
                    } while ($rowdistlistusers = mysql_fetch_assoc($listdistlistusers));
                }
                echo "
  <tr>
    <th>&nbsp;</th>
    <td>&nbsp;</td>
	<td>&nbsp;</td>
  </tr>
  <tr>
    <th>Add new address:</th>
    <td><input type=\"text\" name=\"inDistListAddress\" id=\"inDistListAddress\" /></td><td>&nbsp;</td>
  </tr>
  <tr>
    <th colspan=\"3\" align=\"right\"><input type=\"hidden\" name=\"inReturn\" value=\"" . GetFullURL() . "\" />
          <input type=\"hidden\" name=\"inAction\" value=\"edit_distlists\" />
		  <input type=\"hidden\" name=\"inDLID\" value=\"" . $rowdistlist['dl_id_pk'] . "\" />
          <input type=\"submit\" name=\"inSubmit\" id=\"inSubmit\" value=\"" . $lang['128'] . "\" /></th>
  </tr>
</table>";
            }?>