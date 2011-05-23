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

if (!isset($_POST['inGenerate'])) {
    echo "" . $lang['37'] . "";
    echo "<br><br>";
    ?>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="frmWizard" id="frmWizard">
        <table class="zform">
            <tr>
                <th><strong><?php echo $lang['39']; ?></strong></th>
                <td><input name="inDescription" type="text" id="inDescription" size="30" maxlength="200"></td>
                <td><em><?php echo $lang['44']; ?></em></td>
            </tr>
            <tr>
                <th><strong><?php echo $lang['40']; ?></strong></th>
                <td><input name="inKeywords" type="text" id="inKeywords" size="30" maxlength="100"></td>
                <td><em><?php echo $lang['45']; ?></em></td>
            </tr>
            <tr>
                <th><strong><?php echo $lang['41']; ?></strong></th>
                <td><input name="inAuthor" type="text" id="inAuthor" size="30" maxlength="30"></td>
                <td><em><?php echo $lang['46']; ?></em></td>
            </tr>
            <tr>
                <th><strong><?php echo $lang['42']; ?></strong></th>
                <td><input name="inCopyright" type="text" id="inCopyright" size="30" maxlength="60"></td>
                <td><em><?php echo $lang['47']; ?></em></td>
            </tr>
            <tr>
                <th valign="top"><strong><?php echo $lang['43']; ?></strong></th>
                <td><table id="none" border="0">
                        <tr>
                            <td><input name="inBots_1" type="checkbox" id="inBots_1" value="NOINDEX"></td>
                            <td>NOINDEX</td>
                        </tr>
                        <tr>
                            <td><input name="inBots_2" type="checkbox" id="inBots_2" value="NOFOLLOW"></td>
                            <td>NOFOLLOW</td>
                        </tr>
                        <tr>
                            <td><input name="inBots_3" type="checkbox" id="inBots_3" value="NOIMAGEINDEX"></td>
                            <td>NOIMAGEINDEX</td>
                        </tr>
                        <tr>
                            <td><input name="inBots_4" type="checkbox" id="inBots_4" value="NOIMAGECLICK"></td>
                            <td>NOIMAGECLICK</td>
                        </tr>
                    </table></td>
                <td valign="top"><em><?php echo $lang['48']; ?></em></td>
            </tr>
            <tr>
                <td align="right"><input name="inGenerate" type="submit" id="inGenerate" value="<?php echo $lang['30']; ?>"></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </form>
<?php
} else {
    $isfirst = 'true';
    $robots_content = "";

    if ($_POST['inBots_1'] <> "") {
        if ($isfirst == 'true') {
            $robots_content = $_POST['inBots_1'];
            $isfirst = 'false';
        } else {
            $robots_content = $robots_content . ", " . $_POST['inBots_1'] . "";
        }
    }

    if ($_POST['inBots_2'] <> "") {
        if ($isfirst == 'true') {
            $robots_content = $_POST['inBots_2'];
            $isfirst = 'false';
        } else {
            $robots_content = $robots_content . ", " . $_POST['inBots_2'] . "";
        }
    }
    if ($_POST['inBots_3'] <> "") {
        if ($isfirst == 'true') {
            $robots_content = $_POST['inBots_3'];
            $isfirst = 'false';
        } else {
            $robots_content = $robots_content . ", " . $_POST['inBots_3'] . "";
        }
    }

    if ($_POST['inBots_4'] <> "") {
        if ($isfirst == 'true') {
            $robots_content = $_POST['inBots_4'];
            $isfirst = 'false';
        } else {
            $robots_content = $robots_content . ", " . $_POST['inBots_4'] . "";
        }
    }
    ?>

    <p><?php echo $lang['38']; ?></p><br><br>
    <textarea name="inGeneratedTags" cols="60" rows="8" wrap="OFF" id="inGeneratedTags">
        <?php
        if ($_POST['inDescription'] <> "")
            echo "<META NAME=\"Description\" CONTENT=\"" . stripslashes($_POST['inDescription']) . "\">\n";
        if ($_POST['inKeywords'] <> "")
            echo "<META NAME=\"Keywords\" CONTENT=\"" . $_POST['inKeywords'] . "\">\n";
        if ($_POST['inAuthor'] <> "")
            echo "<META NAME=\"Author\" CONTENT=\"" . $_POST['inAuthor'] . "\">\n";
        if ($_POST['inCopyright'] <> "")
            echo "<META NAME=\"Copyright\" CONTENT=\"" . $_POST['inCopyright'] . "\">\n";
        if ($robots_content <> "")
            echo "<META NAME=\"Robots\" CONTENT=\"" . $robots_content . "\">\n";
        ?>
    </textarea>  
<?php } ?>