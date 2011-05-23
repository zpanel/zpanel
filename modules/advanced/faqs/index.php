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

#Get a list of the FAQ's from the database
$sql = "SELECT * FROM z_faqs WHERE fq_queston_tx IS NOT NULL";
$listfaqs = DataExchange("r", $z_db_name, $sql);
$rowfaqs = mysql_fetch_assoc($listfaqs);

echo $lang['50'];
echo "<br><br>";
?>
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
<blockquote>
    <table>
        <?php do { ?>
            <tr>
                <td><img src="modules/advanced/faqs/item.png" width="16" height="16"></td>
                <td><a href="#" onclick="toggle_visibility('<?php echo $rowfaqs['fq_id_pk']; ?>');"><strong><?php echo Cleaner('o', $rowfaqs['fq_queston_tx']); ?></strong></a>
                    <div id="<?php echo $rowfaqs['fq_id_pk']; ?>" style="display:none;"><?php echo Cleaner('o', $rowfaqs['fq_answer_tx']); ?><br><br></div></td>
            </tr>
        <?php } while ($rowfaqs = mysql_fetch_assoc($listfaqs)); ?>	
    </table>	
</blockquote>