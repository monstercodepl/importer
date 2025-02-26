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

class LeopartsfilterModel extends ObjectModel
{

    /** @var string Name */
    public $id_leopartsfilter_make;
    public $id_leopartsfilter_model;
    public $name;
    public $date_add;
    public $position;
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'leopartsfilter_model',
        'primary' => 'id_leopartsfilter_model',
        'multilang' => true,
        'fields' => array(
            'id_leopartsfilter_make' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
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

        $sql = 'SELECT a.id_leopartsfilter_model, b.name FROM ' . _DB_PREFIX_ . 'leopartsfilter_model a';
        $sql .= ' LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_model_lang b on a.id_leopartsfilter_model = b.id_leopartsfilter_model AND b.id_lang = ' . (int) $id_lang;
        $sql .= ' JOIN ' . _DB_PREFIX_ . 'leopartsfilter_model_shop c on a.id_leopartsfilter_model = c.id_leopartsfilter_model AND c.id_shop = ' . (int) $id_shop;

        $res = Db::getInstance()->executeS($sql);
        $mod_config = LeopartsfilterConfig::getInstance();

        $default = array(
            array(
                'id_leopartsfilter_model' => '',
                'name' => $mod_config->getConfig('PS_MMY_MODEL_DEFAULT_TEXT', '', $id_lang),
            )
        );
        $result = array_merge($default, $res);
        return $result;
    }

    public function getList($active = null, $id_lang = null, $id_shop = null, $id_make = null)
{
    if (!$id_lang) {
        $id_lang = Context::getContext()->language->id;
    }
    if (!$id_shop) {
        $id_shop = Context::getContext()->shop->id;
    }

    $sql = 'SELECT SQL_CALC_FOUND_ROWS b.*, a.* FROM ' . _DB_PREFIX_ . 'leopartsfilter_model a
            LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_model_lang b ON (b.`id_leopartsfilter_model` = a.`id_leopartsfilter_model` 
                AND b.`id_lang` = ' . (int) $id_lang . ')
            WHERE 1=1 ';
    
    if ($active !== null) {
        $sql .= ' AND a.active = ' . (int) $active;
    }
    if ($id_make) {
        $sql .= ' AND a.id_leopartsfilter_make = ' . (int) $id_make;
    }

    // Wymuszenie sortowania numerycznego po nazwie w kolejności od najnowszego do najstarszego
    $sql .= ' ORDER BY CAST(b.name AS UNSIGNED) DESC';

    $results = Db::getInstance()->ExecuteS($sql);
    return $results;
}

    

    public function existName($val, $id_make, $id_model, $id_lang = '', $id_shop = '')
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS model_l.*, model.* FROM ' . _DB_PREFIX_ . 'leopartsfilter_model model
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_model_lang model_l ON (model.`id_leopartsfilter_model` = model_l.`id_leopartsfilter_model` AND model_l.`id_lang` = ' . $id_lang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_model_shop model_s ON (model.`id_leopartsfilter_model` = model_s.`id_leopartsfilter_model` AND model_s.`id_shop` = ' . $id_shop . ')

                WHERE model_l.name ="' . $val . '" and model_l.id_leopartsfilter_model != ' . (int) $id_model . ' and id_leopartsfilter_make = ' . (int) $id_make;
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }
}
