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

class LeopartsfilterYear extends ObjectModel
{

    /** @var string Name */
    public $id_leopartsfilter_make;
    public $id_leopartsfilter_model;
    public $id_leopartsfilter_year;
    public $name;
    public $date_add;
    public $position;
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leopartsfilter_year',
        'primary' => 'id_leopartsfilter_year',
        'multilang' => true,
        'fields' => array(
            'id_leopartsfilter_make' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_leopartsfilter_model' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
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

    public function getList($active = 1, $id_lang = null, $id_shop = null, $id_make = null, $id_model = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS b.*, a.* FROM ' . _DB_PREFIX_ . 'leopartsfilter_year a
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_year_lang b ON (b.`id_leopartsfilter_year` = a.`id_leopartsfilter_year` AND b.`id_lang` = ' . $id_lang . ')
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
        $sql .= ' ORDER BY b.name ASC';
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }

    public function existName($val, $id_model, $id_year, $id_lang = '', $id_shop = '')
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS year_l.*, year.* FROM ' . _DB_PREFIX_ . 'leopartsfilter_year year
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_year_lang year_l ON (year.`id_leopartsfilter_year` = year_l.`id_leopartsfilter_year` AND year_l.`id_lang` = ' . $id_lang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_year_shop year_s ON (year.`id_leopartsfilter_year` = year_s.`id_leopartsfilter_year` AND year_s.`id_shop` = ' . $id_shop . ')

                WHERE year_l.name ="' . $val . '" and year_l.id_leopartsfilter_year != ' . (int) $id_year . ' and id_leopartsfilter_model = ' . (int) $id_model;
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }
    public function getDropdown($id, $selected = 1, $id_lang = null, $id_shop = null)
    {
        // validate module
        unset($id);
        unset($selected);

        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT a.id_leopartsfilter_year, b.name FROM ' . _DB_PREFIX_ . 'leopartsfilter_year a';
        $sql .= ' LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_year_lang b on a.id_leopartsfilter_year = b.id_leopartsfilter_year AND b.id_lang = ' . (int) $id_lang;
        $sql .= ' JOIN ' . _DB_PREFIX_ . 'leopartsfilter_year_shop c on a.id_leopartsfilter_year = c.id_leopartsfilter_year AND c.id_shop = ' . (int) $id_shop;

        $res = Db::getInstance()->executeS($sql);
        $mod_config = LeopartsfilterConfig::getInstance();

        $default = array(
            array(
                'id_leopartsfilter_year' => '',
                'name' => $mod_config->getConfig('PS_MMY_YEAR_DEFAULT_TEXT', '', $id_lang),
            )
        );
        $result = array_merge($default, $res);
        return $result;
    }
}
