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

class LeopartsfilterImportEdit extends ObjectModel
{
    public $id;
    public $id_product;
    public $product_name;
    public $level1;
    public $level2;
    public $level3;
    public $level4;
    public $level5;
    public $status;

    public static $definition = array(
        'table' => 'leopartsfilter_import',
        'primary' => 'id',
        'multilang' => false,
        'multishop' => false,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_STRING),
            'product_name' => array('type' => self::TYPE_STRING),
            'level1' =>     array('type' => self::TYPE_STRING),
            'level2' =>     array('type' => self::TYPE_STRING),
            'level3' =>     array('type' => self::TYPE_STRING),
            'level4' =>     array('type' => self::TYPE_STRING),
            'level5' =>     array('type' => self::TYPE_STRING),
            'status' =>     array('type' => self::TYPE_STRING)
        ),
    );


    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        # Insert multi_shop
        $this->def['table'] = self::$definition['table'];
       // ShopCore::addTableAssociation($this->def['table'], array('type' => 'shop'));
        parent::__construct($id, $id_shop, null);
    }
}
