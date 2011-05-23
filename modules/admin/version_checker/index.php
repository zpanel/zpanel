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
## START Configuration Step 1 - Edit line below to 'zp_version.txt' location
$verchck = eval(@file_get_contents('http://zpanel.co.uk/version/zp_version.txt'));
// NOTE: On or about line #102 also needs to be configured
## END Configuration Step 1
?>

<Script Language="JavaScript">
    function load() {
        var load = window.open('modules/admin/version_checker/copyright.php','','scrollbars=no,menubar=no,height=300,width=400,resizable=no,toolbar=no,location=no,status=no');
    }
</Script>
<style type="text/css">
    .version{
        padding:5px;
    }
    .version th{
        padding:5px;
        font-weight:bold;
    }
    .version td{
        padding:5px;
    }
    .title{
        font-weight:bold;
        font-size:18px;
    }
    .blue{
        font-weight:bold;
        color:#1587CA;
    }
    .green{
        font-weight:bold;
        color:green;
    }
    .red{
        font-weight:bold;
        color:red;
    }
    .legendhead{
        font-weight:bold;
        font-size:14px;
    }
</style>
<?php
include('lang/' . GetPrefdLang($personalinfo['ap_language_vc']) . '.php');
$Version_Num = GetSystemOption('zpanel_version');
$Version_Num = strtoupper($Version_Num);
$ZPanelversion = strtoupper($ZPanelversion);

echo '<div align="center">';
echo '<span class="title">' . $lang['349'] . '</span><br><br>';
echo '<span class="version"><b>' . $lang['350'] . '</b> ' . $ZPanelversion . '</span><br>';
echo '<span class="version"><b>' . $lang['351'] . '</b> ' . $Version_Num . '</span><br>';

if ($ZPanelversion < $Version_Num) {
    echo '<span class="blue">' . $lang['357'] . '</span><br>
			  <a href="http://cp.mach-hosting.com/?c=admin&p=bugreport"><span class="green">(' . $lang['365'] . ')</span></a>';
} elseif ($ZPanelversion == $Version_Num) {
    echo '<span class="green">' . $lang['361'] . '</span><br>';
} else {
    echo '<span class="red">' . $message . '</span><br>
			  <a href="' . $download . '" target="_BLANK"><span class="blue">' . $lang['352'] . '</span></a><br>';
}
echo '</div><br>';
echo '<fieldset><br><legend class="legendhead"><b>' . $lang['353'] . '</b></legend>
		  	  <table class="version" border="0" cellspacing="0" cellpadding="0">';
if (empty($Announcements)) {
    echo '<div align="center">';
    echo '<b>' . $lang['354'] . '</b>';
} else {
    echo '<b>&nbsp;&nbsp;' . $Announcements . '</b></font>';
}
echo '</div>';
echo '</table><br></fieldset><br>';
echo '<fieldset><br><legend class="legendhead"><b>' . $lang['355'] . '</b></legend>';
if (empty($devs)) {
    echo '<div align="center">';
    echo '<b>' . $lang['356'] . '</b>';
    echo '</div>';
} else {
    echo '<div align="center">';
    echo '<table class="version" border="0" cellspacing="0" cellpadding="0" width="100%" align="center"><tr>';
    echo '<th>' . $lang['358'] . '</th><th>' . $lang['359'] . '</th><th>' . $lang['360'] . '</th></tr>';
    for ($i = 0; $i < sizeof($devs); $i++) {
        $dev = explode('|', $devs[$i]);
        $DevName = $dev[0];
        $DevForum = $dev[1];
        $DevJob = $dev[2];
        echo '<tr valign="top"><td nowrap="nowrap"><b>' . $DevName . '</b></td><td nowrap="nowrap">' . $DevForum . '</td><td>' . $DevJob . '</td></tr>';
    }
    echo '</table>';
    echo '</div><br></fieldset>';
}
?>
<!--This function will check if jquery is loaded, and if not then load as needed-->
<script type="text/javascript">
    if (typeof jQuery == 'undefined') { 
        var head = document.getElementsByTagName("head")[0];
        script = document.createElement('script');
        script.id = 'jQuery';
        script.type = 'text/javascript';
        script.src = 'modules/admin/version_checker/jquery.js';
        head.appendChild(script);
    }
</script>
<script type="text/javascript"> 
    $(function(){ 
        $("fieldset.trigger legend a").click(function(event){ 
            event.preventDefault(); 
            $(this).parent().parent().children("div").slideToggle(); 
        }); 
 
        $("fieldset.trigger div a").click(function(event) { 
            event.preventDefault(); 
            $(this).parent().slideUp(); 
        }); 
    }); 
</script> 
<br>
<span class="legendhead"><?php echo $lang['362']; ?></span>
<br>
<br>
<fieldset class="trigger"> 
    <legend><a href="#"> <?php echo $lang['363']; ?> / <?php echo $lang['364']; ?></a></legend> 
    <div style="display: none;"> 
        <br>
        <!-- START Configuration Step 2 - Edit line below to 'changelog.txt' location -->
        <iframe id="1" name="portal" src="http://zpanel.co.uk/version/changelog.txt" width="100%" scrolling="auto" height="500" frameborder="0"></iframe>
    </div>
</fieldset> 
<!-- START Configuration Step 2 -->
<a href="javascript:load();">Zpanel Version Checker v2.4 &copy 2011</a>
