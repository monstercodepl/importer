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

class LeopartsfilterProduct extends ObjectModel
{

    /** @var string Name */
    public $id_leopartsfilter_make;
    public $id_leopartsfilter_model;
    public $id_leopartsfilter_year;
    public $id_leopartsfilter_device;
    public $id_leopartsfilter_level5;
    public $id_product;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leopartsfilter_product',
        'primary' => 'id_product',
        'multilang' => false,
        'fields' => array(
            'id_leopartsfilter_make' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_leopartsfilter_model' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_leopartsfilter_year' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_leopartsfilter_device' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_leopartsfilter_level5' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
        ),
    );

    public static function getList($id_product = null, $id_make = null, $id_model = null, $id_year = null, $id_device = null, $id_level5 = null, $s = null)
    {
        
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'leopartsfilter_product` WHERE 1=1 ';

        if ($id_product != null && (int) $id_product) {
            $sql .= ' AND id_product=' . (int) id_product;
        }
        
        if ($s) {
            $sql .= ' AND (';
            $sql .= ' id_leopartsfilter_make IN (Select id_leopartsfilter_make From '. _DB_PREFIX_ .'leopartsfilter_make_lang WHERE id_lang='. Context::getContext()->language->id .' and name like "%'.$s.'%" )';
            $sql .= ' OR id_leopartsfilter_model IN (Select id_leopartsfilter_model From '. _DB_PREFIX_ .'leopartsfilter_model_lang WHERE id_lang='. Context::getContext()->language->id .' and name like "%'.$s.'%" )';
            $sql .= ' OR id_leopartsfilter_year IN (Select id_leopartsfilter_year From '. _DB_PREFIX_ .'leopartsfilter_year_lang WHERE id_lang='. Context::getContext()->language->id .' and name like "%'.$s.'%" )';
            $sql .= ' OR id_leopartsfilter_device IN (Select id_leopartsfilter_device From '. _DB_PREFIX_ .'leopartsfilter_device_lang WHERE id_lang='. Context::getContext()->language->id .' and name like "%'.$s.'%" )';
            $sql.= ')';
        } else {
            if ($id_make != null && (int) $id_make) {
                $sql .= ' AND id_leopartsfilter_make=' . (int) $id_make;
            }
            if ($id_model != null && (int) $id_model) {
                $sql .= ' AND id_leopartsfilter_model=' . (int) $id_model;
            }
            if ($id_year != null && (int) $id_year) {
                $sql .= ' AND id_leopartsfilter_year=' . (int) $id_year;
            }
            if ($id_device != null && (int) $id_device) {
                $sql .= ' AND id_leopartsfilter_device=' . (int) $id_device;
            }
            if ($id_level5 != null && (int) $id_level5) {
                $sql .= ' AND id_leopartsfilter_level5=' . (int) $id_level5;
            }
        }
        
        $sql .= ' GROUP BY id_product';
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }
}
