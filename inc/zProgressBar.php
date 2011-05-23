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
header("Content-type: image/png");
$width = 120;
if ($_GET['total'] > 0) {
    if ($_GET['used'] < $_GET['total']) {
        $per = ($_GET['used'] / $_GET['total']) * 100;
    } else {
        $per = 100;
        $unlim = false;
    }
} else {
    $per = 0;
    $unlim = true;
}
$percent = round($per, 0);
#echo $percent;
#die();
$im = @imagecreate($width, 14)
        or die("Cannot Initialize new GD image stream");
# 0 - 74% colour is green!
$r = 0;
$g = 128;
$b = 0;
# Between 75%-89% colour is orange!
if ($percent > 74) {
    $r = 255;
    $g = 128;
    $b = 0;
}
# 90 - 99% colour is red!
if ($percent > 89) {
    $r = 128;
    $g = 0;
    $b = 0;
}
$text_color = imagecolorallocate($im, 255, 255, 255);
$fill = ($percent / 100) * $width;
$offset = ($width / 2) - ($width * .02);
imagefilledrectangle($im, 0, 0, $width, 14, imagecolorallocate($im, 200, 200, 200));
imagefilledrectangle($im, 0, 0, $fill, 14, imagecolorallocate($im, $r, $g, $b));
if ($unlim == false) {
    imagestring($im, 2, $offset, 0, "$percent%", $text_color);
} else {
    imagestring($im, 2, $offset, 0, "U/L", $text_color);
}
imagepng($im);
imagedestroy($im);
?> 