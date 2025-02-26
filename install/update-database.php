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
 *    @author leotheme <leotheme@gmail.com>
 *    @copyright 2007-2020 Leotheme
 *    @license http://leotheme.com - prestashop template provider
 */

$res = true;

$res &= (bool) Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leopartsfilter_device` (
        `id_leopartsfilter_device` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `position` int(11) DEFAULT NULL,
        `active` tinyint(1) DEFAULT NULL,
        `date_add` date DEFAULT NULL,
        `id_leopartsfilter_make` int(11) UNSIGNED NOT NULL,
        `id_leopartsfilter_model` int(11) UNSIGNED NOT NULL,
        `id_leopartsfilter_year` int(11) NOT NULL, 
    PRIMARY KEY (`id_leopartsfilter_device`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
');

$res &= (bool) Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leopartsfilter_device_lang` (
        `id_leopartsfilter_device` int(11) UNSIGNED NOT NULL,
        `name` varchar(255) DEFAULT NULL,
        `id_lang` int(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_leopartsfilter_device`, `id_lang`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
');

$res &= (bool) Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leopartsfilter_device_shop` (
        `id_leopartsfilter_device` int(11) UNSIGNED NOT NULL,
        `id_shop` int(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_leopartsfilter_device`, `id_shop`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
');

$res &= (bool) Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leopartsfilter_level5` (
        `id_leopartsfilter_level5` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `position` int(11) DEFAULT NULL,
        `active` tinyint(1) DEFAULT NULL,
        `date_add` date DEFAULT NULL,
        `id_leopartsfilter_make` int(11) UNSIGNED NOT NULL,
        `id_leopartsfilter_model` int(11) UNSIGNED NOT NULL,
        `id_leopartsfilter_year` int(11) NOT NULL, 
        `id_leopartsfilter_device` int(11) NOT NULL,
    PRIMARY KEY (`id_leopartsfilter_level5`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
');

$res &= (bool) Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leopartsfilter_level5_lang` (
        `id_leopartsfilter_level5` int(11) UNSIGNED NOT NULL,
        `name` varchar(255) DEFAULT NULL,
        `id_lang` int(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_leopartsfilter_level5`, `id_lang`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
');

$res &= (bool) Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leopartsfilter_level5_shop` (
        `id_leopartsfilter_level5` int(11) UNSIGNED NOT NULL,
        `id_shop` int(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_leopartsfilter_level5`, `id_shop`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
');


$res &= (bool) Db::getInstance()->execute('
    CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'leopartsfilter_import` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `id_product` int(11) NOT NULL,
        `product_name` varchar(255) NOT NULL,
        `level1` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `level2` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `level3` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `level4` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `level5` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `status` int(11) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
');

$res &= (bool) Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'leopartsfilter_product` ADD `id_leopartsfilter_device` INT NOT NULL AFTER `id_leopartsfilter_year`; ');
$res &= (bool) Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'leopartsfilter_product` ADD `id_leopartsfilter_level5` INT NOT NULL AFTER `id_leopartsfilter_device`; ');
$res &= (bool) Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'leopartsfilter_import` ADD `level5` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `level4`;');
$res &= (bool) Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'leopartsfilter_product` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_product`, `id_leopartsfilter_make`, `id_leopartsfilter_model`, `id_leopartsfilter_year`, `id_leopartsfilter_device`, `id_leopartsfilter_level5`) USING BTREE; 
');
