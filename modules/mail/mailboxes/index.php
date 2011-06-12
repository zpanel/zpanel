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

$sql = "SELECT * FROM z_mailboxes WHERE mb_acc_fk=" . $useraccount['ac_id_pk'] . " AND mb_deleted_ts IS NULL";
$listmailboxes = DataExchange("r", $z_db_name, $sql);
$rowmailboxes = mysql_fetch_assoc($listmailboxes);
$totalmailboxes = DataExchange("t", $z_db_name, $sql);

$sql = "SELECT * FROM z_vhosts WHERE vh_acc_fk=" . $useraccount['ac_id_pk'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1";
$listexdomains = DataExchange("r", $z_db_name, $sql);
$rowexdomains = mysql_fetch_assoc($listexdomains);
$totalexdomains = DataExchange("t", $z_db_name, $sql);

echo $lang['184'] . "<br>";
if ((isset($_GET['r'])) && ($_GET['r'] == 'ok')) {
    echo "<br><div class=\"zannouce\">" . $lang['179'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'exists')) {
    echo "<br><div class=\"zannouce\">" . $lang['180'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'passrs')) {
    echo "<br><div class=\"zannouce\">" . $lang['195'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'hmcomerror')) {
    echo "<br><div class=\"zannouce\">" . $lang['280'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'hmautherror')) {
    echo "<br><div class=\"zannouce\">" . $lang['281'] . ": " . $_GET['mb'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'hmnameerror')) {
    echo "<br><div class=\"zannouce\">" . $lang['287'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'nodomain')) {
    echo "<br><div class=\"zannouce\">" . $lang['191'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'nopassword')) {
    echo "<br><div class=\"zannouce\">" . $lang['296'] . "</div>";
}
if ((isset($_GET['r'])) && ($_GET['r'] == 'notvalid')) {
    echo "<br><div class=\"zannouce\">" . $lang['421'] . "</div>";
}


if (isset($_GET['edit'])) {
# There has been an edit request...
# Platform is Windows, we use hMailServer
    if (ShowServerPlatform() == "Windows") { ###################################### WINDOWS
        $hmaildatabase = GetSystemOption('hmailserver_db');
        $sql = "SELECT * FROM hm_accounts WHERE accountaddress='" . Cleaner('o', $_GET['edit']) . "'";
        $listhmaccount = DataExchange("r", $hmaildatabase, $sql);
        $rowhmaccount = mysql_fetch_assoc($listhmaccount);
        if ($rowhmaccount['accountactive'] == '1') {
            $status = $lang['251'];
            $stchecked = "checked";
            $statuscolor = "green";
        } else {
            $status = $lang['253'];
            $stchecked = "";
            $statuscolor = "red";
        }
        if ($rowhmaccount['accountvacationmessageon'] == '1') {
            $archecked = "checked";
        } else {
            $archecked = "";
        }
        if ($rowhmaccount['accountenablesignature'] == '1') {
            $sigchecked = "checked";
        } else {
            $sigchecked = "";
        }
        if ($rowhmaccount['accountvacationexpires'] == '1') {
            $arexpchecked = "checked";
        } else {
            $arexpchecked = "";
        }
        if ($rowhmaccount['accountvacationexpiredate'] == "0000-00-00 00:00:00") {
            $rowhmaccount['accountvacationexpiredate'] = "";
        }
        $firstname = $rowhmaccount['accountpersonfirstname'];
        $lastname = $rowhmaccount['accountpersonlastname'];
        $timestamp = date('d-m-Y') . " 12:00:00";

        echo "<br><h2>" . $lang['248'] . ": " . Cleaner('o', $_GET['edit']) . "</h2>";
        echo $lang['284'] . ": " . $rowhmaccount['accountlastlogontime'] . "<br><br>";
    } else { ################### POSIX
        # Platform is POSIX, we use Postfix
        $postfixdatabase = GetSystemOption('hmailserver_db');
        $sql = "SELECT * FROM mailbox WHERE username='" . Cleaner('o', $_GET['edit']) . "'";
        $listhmaccount = DataExchange("r", $postfixdatabase, $sql);
        $rowhmaccount = mysql_fetch_assoc($listhmaccount);
        if ($rowhmaccount['active'] == '1') {
            $status = $lang['251'];
            $statuscolor = "green";
        } else {
            $status = $lang['253'];
            $statuscolor = "red";
        }
        $name = explode(" ", $rowhmaccount['name']);
        $firstname = $name[0];
        $lastname = $name[1];
        echo "<br><h2>" . $lang['248'] . ": " . Cleaner('o', $_GET['edit']) . "</h2>";
    } ###################################### ENDIF
#Status table
    ?>
    <form id="frmEditMailbox" name="frmEditMailbox" method="post" action="runner.php?load=obj_mail">
        <table class="zform">
            <tr>
                <th><?php echo $lang['249']; ?>:</th>
                <td><font color="<?php echo $statuscolor; ?>"><?php echo $status; ?></font></td>
            </tr>
            <tr>
                <th><input type="radio" name="inStatus" id="inStatus" value="1" <?php
				if (ShowServerPlatform() == "Windows") { ###################################### WINDOWS
					if ($rowhmaccount['accountactive'] == '1') echo "checked"; 
				} else { ################### POSIX
					if ($rowhmaccount['active'] == '1') echo "checked"; 
				} ###################################### ENDIF ?>/>
				<?php echo $lang['250']; ?><br>
				<input type="radio" name="inStatus" id="inStatus" value="0" <?php
				if (ShowServerPlatform() == "Windows") { ###################################### WINDOWS
					if ($rowhmaccount['accountactive'] == '0') echo "checked"; 
				} else { ################### POSIX
					if ($rowhmaccount['active'] == '0') echo "checked"; 
				} ###################################### ENDIF ?>/><?php echo $lang['252']; ?></th>
                <td></td>
            </tr>
            <tr>
                <th colspan="2" align="right">
                    <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['274']; ?>" />
                </th>
            </tr>
        </table>

        <?php
        #Personal Information table
        echo "<br><h2>" . $lang['255'] . "</h2>";
        echo $lang['267'];
        ?>
        <br><br>
        <table class="zform">
            <tr valign="top">
                <th><?php echo $lang['256']; ?>:</th>
                <td><input type="text" name="inPFname" id="inPFname" value="<?php echo $firstname; ?>"/></td>
            </tr>  
            <tr valign="top">
                <th><?php echo $lang['257']; ?>:</th>
                <td><input type="text" name="inPLname" id="inPLname" value="<?php echo $lastname; ?>"/></td>
            </tr>
            <tr>
                <th colspan="2" align="right">
                    <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['274']; ?>" />
                </th>
            </tr> 
        </table>
        <?php if (ShowServerPlatform() == "Windows") { ###################################### WINDOWS?>
            <?php
            #Signatures table
            echo "<br><h2>" . $lang['254'] . "</h2>";
            echo $lang['268'];
            ?>
            <br><br>
            <table class="zform">
                <tr>
                    <th><?php echo $lang['251']; ?>:</th>
                    <td><input type="checkbox" name="inSigEnable" id="inSigEnable" value="1" <?php echo $sigchecked; ?>/></td>
                </tr>
                <tr valign="top">
                    <th><?php echo "HTML"; ?>:</th>
                    <td><textarea cols="50" rows="10" type="text" name="inSigHTML" id="inSigHTML"/><?php echo $rowhmaccount['accountsignaturehtml']; ?></textarea></td>
                </tr>  
                <tr valign="top">
                    <th><?php echo "Plain Text"; ?>:</th>
                    <td><textarea cols="50" rows="10" type="text" name="inSigPlainTXT" id="inSigPlainTXT"/><?php echo $rowhmaccount['accountsignatureplaintext']; ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2" align="right">
                        <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['274']; ?>" />
                    </th>
                </tr>  
            </table> 

            <?php
            #Auto reply table
            echo "<br><h2>" . $lang['258'] . "</h2>";
            echo $lang['265'];
            ?>
            <br><br>
            <table class="zform">
                <tr>
                    <th><?php echo $lang['251']; ?>:</th>
                    <td><input type="checkbox" name="inAutoReplyEnabled" id="inAutoReplyEnabled" value="1" <?php echo $archecked; ?> /></td>
                </tr>
                <tr valign="top">
                    <th><?php echo $lang['259']; ?>:</th>
                    <td><input type="text" style="width:98%;" name="inAutoReplySubject" id="inAutoReplySubject" value="<?php echo $rowhmaccount['accountvacationsubject']; ?>"/></td>
                </tr>  
                <tr valign="top">
                    <th><?php echo $lang['260']; ?>:</th>
                    <td><textarea cols="50" rows="10" type="text" name="inAutoReplyTXT" id="inAutoReplyTXT"/><?php echo $rowhmaccount['accountvacationmessage']; ?></textarea></td>
                </tr>
                <tr>
                    <th><?php echo $lang['261']; ?>:</th>
                    <td><input type="checkbox" name="inAutoReplyExpire" id="" value="1" <?php echo $arexpchecked; ?>/> <input type="text" name="inAutoReplyExpireDate" id="inAutoReplyExpireDate" value="<?php echo $rowhmaccount['accountvacationexpiredate']; ?>" /> <a href="javascript:show_calendar('document.frmEditMailbox.inAutoReplyExpireDate', '<?php echo $timestamp; ?>');"><img src="modules/mail/mailboxes/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a></td>
                </tr>
                <tr>
                    <th colspan="2" align="right">
                        <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['274']; ?>" />
                    </th>
                </tr>   
            </table>
            <input type="hidden" name="inAction" value="EditMailbox" />
            <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
            <input type="hidden" name="inUsermailbox" value="<?php echo Cleaner('o', $_GET['edit']) ?>" />
        </form>

        <?php
        #External accounts table
        $hmaildatabase = GetSystemOption('hmailserver_db');
        $sql = "SELECT accountid FROM hm_accounts WHERE accountaddress='" . Cleaner('o', $_GET['edit']) . "'";
        $listaccountid = DataExchange("r", $hmaildatabase, $sql);
        $rowaccountid = mysql_fetch_assoc($listaccountid);
        $sql = "SELECT * FROM hm_fetchaccounts WHERE faaccountid='" . $rowaccountid['accountid'] . "' ORDER BY faaccountname ASC";
        $listfaaccount = DataExchange("r", $hmaildatabase, $sql);
        $rowfaaccount = mysql_fetch_assoc($listfaaccount);
        $faaccountid = $rowaccountid['accountid'];

        echo "<br><h2>" . $lang['262'] . "</h2>";
        ?>
        <?php echo $lang['266']; ?><br><br>
        <?php if (!empty($rowfaaccount['faid'])) { ?>
            <form id="frmEditMailboxExternalAccounts" name="frmEditMailboxExternalAccounts" method="post" action="runner.php?load=obj_mail">
                <table class="zform">
                    <tr>
                        <th><?php echo $lang['209']; ?></th><th><?php echo $lang['109']; ?></th><th><?php echo $lang['270']; ?></th><th><?php echo $lang['263']; ?></th><th><?php echo $lang['249']; ?></th><th colspan="2"></th>
                        <?php
                        do {
                            if ($rowfaaccount['faactive'] == '1') {
                                $fastatus = $lang['251'];
                                $fastatuscolor = "green";
                            } else {
                                $fastatus = $lang['253'];
                                $fastatuscolor = "red";
                            }
                            ?>
                        <tr>
                            <td><A NAME="link_<?php echo $rowfaaccount['faid']; ?>"><a href="#link_<?php echo $rowfaaccount['faid']; ?>" title="Show More Settings" onclick="toggle_visibility('ex_<?php echo $rowfaaccount['faid']; ?>');"><?php echo $rowfaaccount['faaccountname']; ?></a></A></td>
                            <td><?php echo $rowfaaccount['fausername']; ?></td>
                            <td><?php echo $rowfaaccount['faserveraddress']; ?></td>
                            <td><?php echo $rowfaaccount['faserverport']; ?></td>
                            <td><font color="<?php echo $fastatuscolor; ?>"><?php echo $fastatus; ?></font></td>
                            <td>
                                <?php if ($rowfaaccount['faactive'] == '1') { ?>
                                    <input type="submit" name="inDisable_<?php echo $rowfaaccount['faid']; ?>" id="inDisable_<?php echo $rowfaaccount['faid']; ?>" value="<?php echo $lang['252']; ?>">
                                <?php } else { ?>
                                    <input type="submit" name="inEnable_<?php echo $rowfaaccount['faid']; ?>" id="inEnable_<?php echo $rowfaaccount['faid']; ?>" value="<?php echo $lang['250']; ?>">
                                <?php } ?>
                            </td>
                            <td>
                                <input type="submit"  name="inDelete_<?php echo $rowfaaccount['faid']; ?>" id="inDelete_<?php echo $rowfaaccount['faid']; ?>" value="<?php echo $lang['84']; ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="7">
                                <div id="ex_<?php echo $rowfaaccount['faid']; ?>" style="display:none;">
                                    <?php echo $lang['277']; ?>: <b><?php echo $rowfaaccount['faminutes']; ?></b> <?php echo $lang['278']; ?><br />
                                    <?php
                                    if ($rowfaaccount['fadaystokeep'] == 0) {
                                        echo "" . $lang['273'] . "<br>";
                                    }
                                    ?>
                                    <?php
                                    if ($rowfaaccount['fadaystokeep'] > 0) {
                                        echo "" . $lang['272'] . " <b>" . $rowfaaccount['fadaystokeep'] . "</b> " . $lang['275'] . "<br>";
                                    }
                                    ?>
                                    <?php
                                    if ($rowfaaccount['fadaystokeep'] == -1) {
                                        echo "" . $lang['271'] . "<br>";
                                    }
                                    ?>
                                    <?php
                                    if ($rowfaaccount['fausessl'] == 1) {
                                        $usessl = $lang['285'];
                                    } else {
                                        $usessl = $lang['286'];
                                    }
                                    ?>
                <?php
                if ($rowfaaccount['faprocessmimerecipients'] == 1) {
                    $usemime = $lang['285'];
                } else {
                    $usemime = $lang['286'];
                }
                ?>
                <?php echo $lang['264']; ?>: <?php echo "<b>" . $usessl . "</b>"; ?><br />
                <?php echo $lang['288']; ?>: <?php echo "<b>" . $usemime . "</b>"; ?><br />
                                </div>
                            </td>
                        </tr>
            <?php } while ($rowfaaccount = mysql_fetch_assoc($listfaaccount)); ?>
                    </tr>
                </table>
                <input type="hidden" name="inFaAccountId" id="inFaAccountId" value="<?php echo $faaccountid; ?>" />
                <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                <input type="hidden" name="inAction" value="EditMailboxExternalAccounts" />
            </form>
        <?php
        } else {
            echo "<b>" . $lang['276'] . "</b>";
        }
        ?>
        <br><br>
        <form id="frmAddMailboxExternalAccounts" name="frmAddMailboxExternalAccounts" method="post" action="runner.php?load=obj_mail">	
            <table class="zform">
                <tr valign="top">
                    <th><?php echo $lang['209']; ?>:</th>
                    <td><input type="text" name="inExMessageAccount" id="inExMessageAccount" value="<?php echo $rowfaaccount['faaccountname']; ?>"/></td>
                </tr>  
                <tr valign="top">
                    <th><?php echo $lang['270']; ?>:</th>
                    <td><input type="text" name="inExMessageAddress" id="inExMessageAddress" value="<?php echo $rowfaaccount['faserveraddress']; ?>"/></td>
                </tr>
                <tr valign="top">
                    <th><?php echo $lang['263']; ?>:</th>
                    <td><input type="text" name="inExMessagePort" id="inExMessagePort" value="<?php echo $rowfaaccount['faserverport']; ?>"/></td>
                </tr>
                <tr valign="top">
                    <th><?php echo $lang['109']; ?>:</th>
                    <td><input type="text" name="inExMessageUser" id="inExMessageUser" value="<?php echo $rowfaaccount['fausername']; ?>"/></td>
                </tr>
                <tr valign="top">
                    <th><?php echo $lang['116']; ?>:</th>
                    <td><input type="password" name="inExMessagePass" id="inExMessagePass" value="<?php echo $rowfaaccount['fapassword']; ?>"/></td>
                </tr>
                <tr valign="top">
                    <th><?php echo $lang['277']; ?>:</th>
                    <td>15<input type="radio" name="inExMessagecheck" id="inExMessagecheck" value="15" />  
                		30<input type="radio" name="inExMessagecheck" id="inExMessagecheck" value="30" checked/> 
                		45<input type="radio" name="inExMessagecheck" id="inExMessagecheck" value="45" /> 
                		60<input type="radio" name="inExMessagecheck" id="inExMessagecheck" value="60" /> <?php echo $lang['278']; ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang['264']; ?>:</th>
                    <td><input type="checkbox" name="inExMessageSSL" id="inExMessageSSL" value="1"/></td>
                </tr>
                <tr>
                    <th><?php echo $lang['289']; ?>:</th>
                    <td><input type="radio" name="inExMessageOption" id="inExMessageOption" value="-1" /><?php echo $lang['271']; ?><br />
                        <input type="radio" name="inExMessageOption" id="inExMessageOption" value="skip" checked/><?php echo $lang['272']; ?> <input type="text" name="inExMessageOption2" id="inExMessageOption2" value="7" style="width:30px;" /> <?php echo $lang['275']; ?><br />
                        <input type="radio" name="inExMessageOption" id="inExMessageOption" value="0" /><?php echo $lang['273']; ?><br /><br />
                        <input type="checkbox" name="inExMessageMIME" id="inExMessageMIME" value="1" /><?php echo $lang['288']; ?></td>
                </tr>
                <td colspan="2"><?php echo $lang['279']; ?>:<br><b><?php echo Cleaner('o', $_GET['edit']) ?></b></td>
                </tr>
                </tr>
                <th><?php echo $lang['116']; ?>:</th>
                <td><input type="password" name="inExMessageAuth" id="inExMessageAuth" /></td>
                </tr>
                <tr>
                    <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                        <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['128']; ?>" />
                    </th>
                </tr>
            </table> 
            <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
            <input type="hidden" name="inUsermailbox" id="inUsermailbox" value="<?php echo Cleaner('o', $_GET['edit']) ?>" />
            <input type="hidden" name="inHMAccountID" id="inHMAccountID" value="<?php echo $faaccountid; ?>" />
            <input type="hidden" name="inAction" value="AddMailboxExternalAccounts" />
        </form>
    <?php } else {###################################### POSTFIX ?>
        <input type="hidden" name="inAction" value="EditMailbox" />
        <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
        <input type="hidden" name="inUsermailbox" value="<?php echo Cleaner('o', $_GET['edit']) ?>" />
        </form>
                <?php }###################################### ENDIF  ?>

                <?php
            } else {

#If not in edit mode, display the main mailbox page
                echo "<br><h2>" . $lang['176'] . "</h2>";

                if ($totalmailboxes > 0) {
                    ?>
        <form id="frmFilter" name="frmFilter" method="post" action="runner.php?load=obj_mail">
            <table id="none">
                <tr>
                            <?php
#find user account domains to generate filter list
                            $sql = "SELECT * FROM z_vhosts WHERE vh_acc_fk=" . $useraccount['ac_id_pk'] . " AND vh_deleted_ts IS NULL AND vh_type_in=1";
                            $listfilterdomains = DataExchange("r", $z_db_name, $sql);
                            $rowfilterdomains = mysql_fetch_assoc($listfilterdomains);
                            $totalfilterdomains = DataExchange("t", $z_db_name, $sql);
                            ?>
                    <td><b><?php echo $lang['290']; ?>:</b></td>
                    <td><select name="inFilter" id="inFilter"><option value=""><?php echo $lang['283']; ?></option>
        <?php
        do {
            if (strstr($rowfilterdomains['vh_name_vc'], $_GET['rfilter'])) {
                $fselected = "selected";
            } else {
                $fselected = "";
            }
            echo "<option value=\"" . $rowfilterdomains['vh_name_vc'] . "\" " . $fselected . ">@" . $rowfilterdomains['vh_name_vc'] . "</option>";
        } while ($rowfilterdomains = mysql_fetch_assoc($listfilterdomains));
        ?>
                        </select>
                    </td>
                    <td><input type="submit" value="<?php echo $lang['282']; ?>" /></td>
                </tr>
            </table>
            <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="filter_mailbox" />
        </form>
        <br>
        <form id="frmMailboxes" name="frmMailboxes" method="post" action="runner.php?load=obj_mail">
            <table class="zgrid">
                <tr>
                    <th><?php echo $lang['181']; ?></th>
                    <th><?php echo $lang['182']; ?></th>
                    <th><?php echo $lang['249']; ?></th>
                    <th></th>
                </tr>
                <?php
                do {
                    #get the status of individual mailboxes, enabled or disabled
                    if (ShowServerPlatform() == "Windows") { ###################################### WINDOWS
                        # Platform is Windows, we use hMailServer
                        $hmaildatabase = GetSystemOption('hmailserver_db');
                        $sql = "SELECT * FROM hm_accounts WHERE accountaddress='" . Cleaner('o', $rowmailboxes['mb_address_vc']) . "'";
                        $listhmaccount = DataExchange("r", $hmaildatabase, $sql);
                        $rowhmaccount = mysql_fetch_assoc($listhmaccount);
                        if ($rowhmaccount['accountactive'] == '1') {
                            $status = $lang['251'];
                            $statuscolor = "green";
                        } else {
                            $status = $lang['253'];
                            $statuscolor = "red";
                        }
                    } else { #################### POSIX
                        # Platform is POSIX, we use Postfix
                        $postfixdatabase = GetSystemOption('hmailserver_db');
                        $sql = "SELECT * FROM mailbox WHERE username='" . Cleaner('o', $rowmailboxes['mb_address_vc']) . "'";
                        $listhmaccount = DataExchange("r", $postfixdatabase, $sql);
                        $rowhmaccount = mysql_fetch_assoc($listhmaccount);
                        if ($rowhmaccount['active'] == '1') {
                            $status = $lang['251'];
                            $statuscolor = "green";
                        } else {
                            $status = $lang['253'];
                            $statuscolor = "red";
                        }
                    } ###################################### ENDIF
                    #a little update to filter domain results
                    if ((isset($_GET['rfilter'])) && ($_GET['rfilter'] != '')) {
                        $rfilter = $_GET['rfilter'];
                        $isfilter = strstr($rowmailboxes['mb_address_vc'], $rfilter);
                        if ($isfilter) {
                            ?>
                            <tr>
                                <td><?php echo Cleaner('o', $rowmailboxes['mb_address_vc']); ?></td>
                                <td><?php echo date(GetSystemOption('zpanel_df'), $rowmailboxes['mb_created_ts']); ?></td>
                                <td><font color="<?php echo $statuscolor; ?>"><?php echo $status; ?></font></td>
                                <td>
                                    <input type="submit" name="inEdit_<?php echo $rowmailboxes['mb_id_pk']; ?>" id="inEdit_<?php echo $rowmailboxes['mb_id_pk']; ?>" value="<?php echo $lang['85']; ?>" /> 
                                    <input type="submit" name="inReset_<?php echo $rowmailboxes['mb_id_pk']; ?>" id="inReset_<?php echo $rowmailboxes['mb_id_pk']; ?>" value="<?php echo $lang['183']; ?>" /><input type="submit" name="inDelete_<?php echo $rowmailboxes['mb_id_pk']; ?>" id="inDelete_<?php echo $rowmailboxes['mb_id_pk']; ?>" value="<?php echo $lang['84']; ?>" /></td>
                            </tr>	
                <?php } ?>	
                        <?php
                    }
                    #a little update to filter domain results
                    if ((!isset($_GET['rfilter'])) or ($_GET['rfilter'] == '')) {
                        ?>
                        <tr>
                            <td><?php echo Cleaner('o', $rowmailboxes['mb_address_vc']); ?></td>
                            <td><?php echo date(GetSystemOption('zpanel_df'), $rowmailboxes['mb_created_ts']); ?></td>
                            <td><font color="<?php echo $statuscolor; ?>"><?php echo $status; ?></font></td>
                            <td>
                                <input type="submit" name="inEdit_<?php echo $rowmailboxes['mb_id_pk']; ?>" id="inEdit_<?php echo $rowmailboxes['mb_id_pk']; ?>" value="<?php echo $lang['85']; ?>" /> 
                                <input type="submit" name="inReset_<?php echo $rowmailboxes['mb_id_pk']; ?>" id="inReset_<?php echo $rowmailboxes['mb_id_pk']; ?>" value="<?php echo $lang['183']; ?>" /><input type="submit" name="inDelete_<?php echo $rowmailboxes['mb_id_pk']; ?>" id="inDelete_<?php echo $rowmailboxes['mb_id_pk']; ?>" value="<?php echo $lang['84']; ?>" /></td>
                        </tr>	
            <?php }
        } while ($rowmailboxes = mysql_fetch_assoc($listmailboxes)); ?>
            </table>
            <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" /><input type="hidden" name="inAction" value="delete_mailbox" />
        </form>
        <?php
    } else {
        echo $lang['178'];
    }

    if (($quotainfo['qt_mailboxes_in'] > GetQuotaUsages('mailboxes', $useraccount['ac_id_pk'])) && (!isset($_GET['reset']))) {
        echo "<br><h2>" . $lang['177'] . "</h2>";
        ?>
                            <?php if (GetQuotaUsages('domains', $useraccount['ac_id_pk']) > 0) { ?>
            <form id="frmNewMailbox" name="frmNewMailbox" method="post" action="runner.php?load=obj_mail">
                <table class="zform">
                    <tr>
                        <th><?php echo $lang['14']; ?></th>
                        <td><input name="inAddress" type="text" id="inAddress" />
                            <select name="inDomain" id="inDomain">
                                <option value="" selected="selected">-- <?php echo $lang['29']; ?> --</option>
            <?php
            do {
                echo "<option value=\"" . $rowexdomains['vh_name_vc'] . "\">@" . $rowexdomains['vh_name_vc'] . "</option>";
            } while ($rowexdomains = mysql_fetch_assoc($listexdomains));
            ?>
                            </select></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['116']; ?>:</th>
                        <td><input name="inPassword" type="password" id="inPassword"/></td>
                    </tr>
                    <tr>
                        <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                            <input type="hidden" name="inAction" value="NewMailbox" />
                            <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['128']; ?>" /></th>
                    </tr>
                </table>
            </form><?php
        } else {
            echo $lang['232'];
        }
    }

    if (isset($_GET['reset'])) {
# There has been a password reset request...
        echo "<br><h2>" . $lang['194'] . "</h2>";
        echo $lang['193'];
        echo "<br><br>";
        ?>
        <form id="frmResetPassword" name="frmResetPassword" method="post" action="runner.php?load=obj_mail">
            <table class="zform">
                <tr>
                    <th><?php echo $lang['14']; ?></th>
                    <td><?php echo Cleaner('o', $_GET['reset']); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang['116']; ?>:</th>
                    <td><input name="inPassword" type="password" id="inPassword"/></td>
                </tr>
                <tr>
                    <th colspan="2" align="right"><input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
                        <input type="hidden" name="inMailbox" value="<?php echo $_GET['reset']; ?>" />
                        <input type="hidden" name="inAction" value="ResetPassword" />
                        <input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['274']; ?>" /></th>
                </tr>
            </table>
        </form>
    <?php }
} ?>

<script language="JavaScript" src="modules/mail/mailboxes/ts_picker.js"></script>
<script type="text/javascript">
    <!--
    function toggle_visibility(id) {
        var e = document.getElementById(id);
        if(e.style.display == 'none')
            e.style.display = 'block';
        else
            e.style.display = 'none';
    }
    //-->
</script>