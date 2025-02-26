<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @author     PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 * @package    PHPExcel_Writer_OpenDocument
 * @version    ##VERSION##, ##DATE##
 */
 
class PHPExcel_Reader_Excel5_Color
{
    /**
     * Read color
     *
     * @param int $color Indexed color
     * @param array $palette Color palette
     * @return array RGB color value, example: array('rgb' => 'FF0000')
     */
    public static function map($color, $palette, $version)
    {
        if ($color <= 0x07 || $color >= 0x40) {
            // special built-in color
            return PHPExcel_Reader_Excel5_Color_BuiltIn::lookup($color);
        } elseif (isset($palette) && isset($palette[$color - 8])) {
            // palette color, color index 0x08 maps to pallete index 0
            return $palette[$color - 8];
        } else {
            // default color table
            if ($version == PHPExcel_Reader_Excel5::XLS_BIFF8) {
                return PHPExcel_Reader_Excel5_Color_BIFF8::lookup($color);
            } else {
                // BIFF5
                return PHPExcel_Reader_Excel5_Color_BIFF5::lookup($color);
            }
        }

        return $color;
    }
}