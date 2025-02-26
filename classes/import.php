<?php
/**
 * 2007-2020 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Parts Filter for searching cars
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2020 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

class LeopartsfilterImport
{
    public $params;
    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            # validate module
            $instance = new LeopartsfilterImport();
        }
        return $instance;
    }
}
