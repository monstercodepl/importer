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

class LeopartsfilterMake extends ObjectModel
{

    /** @var string Name */
    public $id_leopartsfilter_make;
    public $name;
    public $date_add;
    public $position;
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leopartsfilter_make',
        'primary' => 'id_leopartsfilter_make',
        'multilang' => true,
        'fields' => array(
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

    /**
     * Create list make for Heper Form
     */
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

        $sql = 'SELECT a.id_leopartsfilter_make, b.name FROM ' . _DB_PREFIX_ . 'leopartsfilter_make a';
        $sql .= ' LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_make_lang b on a.id_leopartsfilter_make = b.id_leopartsfilter_make AND b.id_lang = ' . (int) $id_lang;
        $sql .= ' JOIN ' . _DB_PREFIX_ . 'leopartsfilter_make_shop c on a.id_leopartsfilter_make = c.id_leopartsfilter_make AND c.id_shop = ' . (int) $id_shop;

        $res = Db::getInstance()->executeS($sql);
        $mod_config = LeopartsfilterConfig::getInstance();

        $default = array(
            array(
                'id_leopartsfilter_make' => '',
                'name' => $mod_config->getConfig('PS_MMY_MAKE_DEFAULT_TEXT', '', $id_lang),
            )
        );
        $result = array_merge($default, $res);
        return $result;
    }

    public function getList($active = null, $id_lang = null, $id_shop = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS b.*, a.* FROM ' . _DB_PREFIX_ . 'leopartsfilter_make a
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_make_lang b ON (b.`id_leopartsfilter_make` = a.`id_leopartsfilter_make` AND b.`id_lang` = ' . $id_lang . ')
                WHERE 1=1 ';
        if ($active != null) {
            $sql .= ' AND active = ' . (int) $active;
        }
        $sql .= ' ORDER BY b.name ASC';
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }

    public function existName($value, $id_make, $id_lang = '', $id_shop = '')
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS make_l.*, make.* FROM ' . _DB_PREFIX_ . 'leopartsfilter_make make
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_make_lang make_l ON (make.`id_leopartsfilter_make` = make_l.`id_leopartsfilter_make` AND make_l.`id_lang` = ' . $id_lang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_make_shop make_s ON (make.`id_leopartsfilter_make` = make_s.`id_leopartsfilter_make` AND make_s.`id_shop` = ' . $id_shop . ')

                WHERE make_l.name ="' . $value . '" and make_l.id_leopartsfilter_make != ' . (int) $id_make;
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }
}
