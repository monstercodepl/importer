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
 
class PHPExcel_Reader_Excel5_Style_Border
{
    protected static $map = array(
        0x00 => PHPExcel_Style_Border::BORDER_NONE,
        0x01 => PHPExcel_Style_Border::BORDER_THIN,
        0x02 => PHPExcel_Style_Border::BORDER_MEDIUM,
        0x03 => PHPExcel_Style_Border::BORDER_DASHED,
        0x04 => PHPExcel_Style_Border::BORDER_DOTTED,
        0x05 => PHPExcel_Style_Border::BORDER_THICK,
        0x06 => PHPExcel_Style_Border::BORDER_DOUBLE,
        0x07 => PHPExcel_Style_Border::BORDER_HAIR,
        0x08 => PHPExcel_Style_Border::BORDER_MEDIUMDASHED,
        0x09 => PHPExcel_Style_Border::BORDER_DASHDOT,
        0x0A => PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT,
        0x0B => PHPExcel_Style_Border::BORDER_DASHDOTDOT,
        0x0C => PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT,
        0x0D => PHPExcel_Style_Border::BORDER_SLANTDASHDOT,
    );

    /**
     * Map border style
     * OpenOffice documentation: 2.5.11
     *
     * @param int $index
     * @return string
     */
    public static function lookup($index)
    {
        if (isset(self::$map[$index])) {
            return self::$map[$index];
        }
        return PHPExcel_Style_Border::BORDER_NONE;
    }
}