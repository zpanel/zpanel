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
$path = GetSystemOption('zpanel_root') . 'modules';
$coldisplay = GetSystemOption('module_icons_pr');
$dir = opendir($path);
$dirFiles = array();
while ($file = readdir($dir)) {
    if ($file != "." && $file != "..") {
        $dirFiles[] = $file;
    }
}
closedir($dir);
$dirFilesSorted = '';
$dirFilesSorted = sort($dirFiles, SORT_REGULAR);

foreach ($dirFiles as $file) {
    $colcount = 1;
    if (is_dir($path . '/' . $file)) {
        if (file_exists($path . '/' . $file . '/catinfo.zp.php')) {
            require_once($path . '/' . $file . '/catinfo.zp.php');
            if (isset($thiscat['title'])) {
                if (CheckModuleCatForPerms($thiscat['level_required'], $permissionset) == 1) {
                    echo "<table class=\"zmodule\">\n<tr>\n<th align=\"left\"><a name=\"" . $file . "\"></a>" . $thiscat['title'] . "<a href=\"#\" class=\"zmodule\" id=\"zmodule_" . $file . "_a\"></a></th>\n</tr>\n";
                    echo "<tr>\n<td align=\"left\">\n<div class=\"zmodule_" . $file . "\" id=\"zmodule_" . $file . "\">\n<table class=\"zmodulecontent\" align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tr>\n<td>\n";
                    $modfilearray = scandir($path . '/' . $file);
                    foreach ($modfilearray as $modfile) {
                        if (is_dir($path . '/' . $file . '/' . $modfile)) {
                            if (file_exists($path . '/' . $file . '/' . $modfile . '/modinfo.zp.php')) {
                                require_once($path . '/' . $file . '/' . $modfile . '/modinfo.zp.php');
                                if ((isset($thismod['title'])) && (isset($thismod['icon']))) {
                                    if ($colcount == 1) {
                                        echo "</td>\n</tr>\n<tr>\n<td align=\"left\">\n<table align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tr>\n";
                                    }
                                    $cleanname = str_replace(" ", "<br />", $thismod['title']);
                                    #Icon overrides
                                    $getmodicon = explode('.', $thismod['icon']);
                                    if (file_exists($path . '/' . $file . '/' . $modfile . '/' . $getmodicon[0] . '_override.' . $getmodicon[1])) {
                                        $iconpath = 'modules/' . $file . '/' . $modfile . '/';
                                        $modicon = $getmodicon[0] . '_override.' . $getmodicon[1];
                                    } elseif (file_exists(GetSystemOption('zpanel_root') . 'templates/' . GetSystemOption('zpanel_template') . '/icons/' . $modfile . '/' . $thismod['icon'])) {
                                        $iconpath = 'templates/' . GetSystemOption('zpanel_template') . '/icons/' . $modfile . '/';
                                        $modicon = $thismod['icon'];
                                    } else {
                                        $iconpath = 'modules/' . $file . '/' . $modfile . '/';
                                        $modicon = $thismod['icon'];
                                    }
                                    echo "<td style=\"text-align:center;\" align=\"left\"><a href=\"?c=$file&p=$modfile\" title=\"" . $thismod['title'] . "\"><img src=\"" . $iconpath . $modicon . "\" border=\"0\" /></a><br /><a href=\"./?c=$file&p=$modfile\">" . $cleanname . "</a></td>\n";

                                    if ($colcount == $coldisplay) {
                                        $colcount = 1;
                                        $tableopen = 0;
                                        echo "  </tr>\n</table>\n";
                                    } else {
                                        $colcount = $colcount + 1;
                                        $tableopen = 1;
                                    }
                                }
                            }
                        }
                    }
                    if ($tableopen) {
                        echo "</tr>\n</table>\n</div>\n";
                    }echo "</tr>\n</table>\n</div>\n</td>\n</tr>\n</table>\n<br>\n";
                }
            }
        }
    }
}
?>


