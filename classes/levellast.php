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

class LeopartsfilterLevellast extends ObjectModel
{

    /** @var string Name */
    public $id_leopartsfilter_make;
    public $id_leopartsfilter_model;
    public $id_leopartsfilter_device;
    public $id_leopartsfilter_year;
    public $id_leopartsfilter_level5;
    public $name;
    public $date_add;
    public $position;
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leopartsfilter_level5',
        'primary' => 'id_leopartsfilter_level5',
        'multilang' => true,
        'fields' => array(
            'id_leopartsfilter_make' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_leopartsfilter_model' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_leopartsfilter_year' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_leopartsfilter_device' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'position' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        # Insert multi_shop
        $this->def['table'] = self::$definition['table'];
        ShopCore::addTableAssociation($this->def['table'], array('type' => 'shop'));

        parent::__construct($id, $id_lang, $id_shop);
    }

    public function getList($active = 1, $id_lang = null, $id_shop = null, $id_make = null, $id_model = null, $id_year = null, $id_device = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS b.*, a.* FROM ' . _DB_PREFIX_ . 'leopartsfilter_level5 a
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_level5_lang b ON (b.`id_leopartsfilter_level5` = a.`id_leopartsfilter_level5` AND b.`id_lang` = ' . $id_lang . ')
                WHERE 1=1 ';
        if ($active != null) {
            $sql .= ' AND active = ' . (int) $active;
        }
        if ($id_make) {
            $sql .= ' AND a.id_leopartsfilter_make = ' . (int) $id_make;
        }
        if ($id_model) {
            $sql .= ' AND a.id_leopartsfilter_model = ' . (int) $id_model;
        }
        if ($id_year) {
            $sql .= ' AND a.id_leopartsfilter_year = ' . (int) $id_year;
        }
        if ($id_device) {
            $sql .= ' AND a.id_leopartsfilter_device = ' . (int) $id_device;
        }
        $sql .= ' ORDER BY b.name ASC';
        
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }

    public function existName($val, $id_model, $id_levellast, $id_lang = '', $id_shop = '')
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS levellast_l.*, levellast.* FROM ' . _DB_PREFIX_ . 'leopartsfilter_level5 levellast
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_level5_lang levellast_l ON (levellast.`id_leopartsfilter_level5` = levellast_l.`id_leopartsfilter_level5` AND levellast_l.`id_lang` = ' . $id_lang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_level5_shop levellast_s ON (levellast.`id_leopartsfilter_level5` = levellast_s.`id_leopartsfilter_level5` AND levellast_s.`id_shop` = ' . $id_shop . ')

                WHERE levellast_l.name ="' . $val . '" and levellast_l.id_leopartsfilter_level5 != ' . (int) $id_levellast;
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }
}
