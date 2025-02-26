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

require_once(_PS_MODULE_DIR_ . 'leopartsfilter/lib/PHPExcel.php');

require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/config.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/import.php');

class AdminLeopartsfilterImportController extends ModuleAdminController
{
    public $count_field;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
        $this->meta_title = $this->l('LEO PARTS FILTER IMPORT DATA');
        $this->count_field = (int) LeopartsfilterConfig::getInstance()->getConfig('PS_ALLOW_SEARCH_FORM', false, false);
    }
    
    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Administration');
        $this->toolbar_title[] = $this->l('LEO PARTS FILTER IMPORT DATA');
    }

    public function initToolbarFlags()
    {
        $this->getLanguages();
    //    $this->initTabModuleList();
        $this->initPageHeaderToolbar();

        $this->context->smarty->assign(array(
            'maintenance_mode' => !(bool) Configuration::get('PS_SHOP_ENABLE'),
            'debug_mode' => (bool) _PS_MODE_DEV_,
            'lite_display' => $this->lite_display,
            'url_post' => self::$currentIndex . '&token=' . $this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'title' => $this->page_header_toolbar_title,
           'toolbar_btn' => $this->page_header_toolbar_btn,
           'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
        ));
    }

    
    public function initContent()
    {
        $lang = Language::getLanguages();
        $id_shop = (int)Context::getContext()->shop->id;
        $id_lang = (int)Context::getContext()->language->id;

        if (Tools::getValue('id') && Tools::getValue('action')=='insert') {
            $this->updateLevel1Data(Tools::getValue('id'));
            die();
        }
        if (Tools::getValue('id') && Tools::getValue('action')=='delete') {
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'leopartsfilter_import WHERE id IN ('.Tools::getValue('id').')';
            echo Db::getInstance()->execute($sql);
            die();
        }
        $file_import = $this->uploadDocument('file_import');

        if ($file_import) {
            $file = _PS_MODULE_DIR_ . $this->module->name . '/file_upload/'.$file_import;
            $objPHPExcel = new PHPExcel();
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray();

            if (isset($sheetData[1]) && count($sheetData[1]) < (count($lang) * $this->count_field) + 2) {
                $this->context->smarty->assign(array(
                    'removedata' => Tools::getValue('removedata') ? Tools::getValue('removedata') : '',
                    'status' => 0,
                ));
            } else {
                if (Tools::getValue('removedata') == 1) {
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_device`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_device_lang`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_device_shop`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_make`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_make_lang`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_make_shop`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_model`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_model_lang`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_model_shop`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_year`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_year_lang`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_year_shop`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_device_shop`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_level5`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_level5_lang`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_level5_lang`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_import`');
                    Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'leopartsfilter_product`');
                }
                
                $arr_import = array();

                for ($i=1; $i<count($sheetData); $i++) {
                    if (!$sheetData[$i][0]) {
                        $sheetData[$i][0] = 0;
                    }
                    if (!$sheetData[$i][1]) {
                        $sheetData[$i][1] = "";
                    }
                    $level1 = array();
                    $level2 = array();
                    $level3 = array();
                    $level4 = array();
                    $level5 = array();
                    $po = 2;
                    for ($j=1; $j<=count($lang); $j++) {
                        $level1[$lang[$j-1]['id_lang']] = $sheetData[$i][$j + 1];
                        $po++;
                    }
                    for ($j=1; $j<=count($lang) && $this->count_field >= 2; $j++) {
                        $level2[$lang[$j-1]['id_lang']] = $sheetData[$i][$j + count($lang) + 1];
                        $po++;
                    }
                    for ($j=1; $j<=count($lang) && $this->count_field >= 3; $j++) {
                        $level3[$lang[$j-1]['id_lang']] = $sheetData[$i][$j + count($lang) * 2 + 1];
                        $po++;
                    }
                    for ($j=1; $j<=count($lang) && $this->count_field >= 4; $j++) {
                        $level4[$lang[$j-1]['id_lang']] = $sheetData[$i][$j + count($lang) * 3 + 1];
                        $po++;
                    }
                    for ($j=1; $j<=count($lang) && $this->count_field >= 5; $j++) {
                        $level5[$lang[$j-1]['id_lang']] = $sheetData[$i][$j + count($lang) * 4 + 1];
                        $po++;
                    }
                    if (isset($sheetData[$i][1])) {
                        $product_name = $sheetData[$i][1];
                    } else {
                        $product_name = '';
                    }

                    if (isset($sheetData[$i][$po])) {
                        $status = $sheetData[$i][$po];
                    } else {
                        $status = 0;
                    }

                    if (isset($sheetData[$i][$po])) {
                        $inport_id = $sheetData[$i][$po];
                    } else {
                        $inport_id = 0;  
                    }

                    if (!count($level1)) {
                        for ($j=1; $j<=count($lang); $j++) {
                            $level1[$lang[$j-1]['id_lang']] = '';
                        }
                    }
                    if (!count($level2)) {
                        for ($j=1; $j<=count($lang); $j++) {
                            $level2[$lang[$j-1]['id_lang']] = '';
                        }
                    }
                    if (!count($level3)) {
                        for ($j=1; $j<=count($lang); $j++) {
                            $level3[$lang[$j-1]['id_lang']] = '';
                        }
                    }
                    if (!count($level4)) {
                        for ($j=1; $j<=count($lang); $j++) {
                            $level4[$lang[$j-1]['id_lang']] = '';
                        }
                    }
                    if (!count($level5)) {
                     for ($j=1; $j<=count($lang); $j++) {
                            $level5[$lang[$j-1]['id_lang']] = '';
                        }
                    }

                    if ($inport_id && $status == 1) {

                    } else {
                        if ($inport_id && $status == 3) {
                        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'leopartsfilter_import WHERE id=' . (int)$inport_id;
                            Db::getInstance()->execute($sql);
                        } else {
                            if ($inport_id && $status == 2) {
                                $sql = 'UPDATE ' . _DB_PREFIX_ . 'leopartsfilter_import SET id_product= '.$sheetData[$i][0].', product_name="'.pSQL($product_name).'", level1="'.pSQL(json_encode($level1)).'", level2="'.pSQL(json_encode($level2)).'", level3="'.pSQL(json_encode($level3)).'", level4="'.pSQL(json_encode($level4)).'", level5="'.pSQL(json_encode($level5)).'", status = 0 WHERE id=' . (int)$inport_id;
                                Db::getInstance()->execute($sql);
                            } else {
                                $arr_import[] = '('.$sheetData[$i][0].',"'.pSQL($product_name).'","'.pSQL(json_encode($level1)).'","'.pSQL(json_encode($level2)).'","'.pSQL(json_encode($level3)).'","'.pSQL(json_encode($level4)).'","'.pSQL(json_encode($level5)).'", 0)';
                            }
                        }
                    }
                    
                }

                if (count($arr_import)) {
                    foreach ($arr_import as $key => $value) {
                        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_import (id_product, product_name, level1, level2, level3, level4, level5, status) VALUE ' . $value;
                        Db::getInstance()->execute($sql);
                    } 
                }
                $this->context->smarty->assign(array(
                    'removedata' => Tools::getValue('removedata') ? Tools::getValue('removedata') : '',
                    'status' => 1,
                ));
                Tools::redirect(Tools::getHttpHost(true) . $_SERVER['REQUEST_URI']);
            }
        }
        $query = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_import WHERE 1 ';
        if (Tools::getValue('lv1')) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_make_lang WHERE id_leopartsfilter_make='.(int)Tools::getValue('lv1') . ' AND id_lang=' . (int)$id_lang;
            $leopartsfilter_make = Db::getInstance()->getRow($sql);
            if ($leopartsfilter_make) {
                $query .= ' AND level1 LIKE "%{\"'.(int)Tools::getValue('lv1').'\":\"'.$leopartsfilter_make['name'].'\"}%"';
            }
        }
        if (Tools::getValue('lv2')) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_model_lang WHERE id_leopartsfilter_model='.(int)Tools::getValue('lv2') . ' AND id_lang=' . (int)$id_lang;
            $leopartsfilter_model = Db::getInstance()->getRow($sql);
            if ($leopartsfilter_model) {
                $query .= ' AND level2 LIKE "%{\"'.(int)Tools::getValue('lv2').'\":\"'.$leopartsfilter_model['name'].'\"}%"';
            }
        }

        if (Tools::getValue('lv3')) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_year_lang WHERE id_leopartsfilter_year='.(int)Tools::getValue('lv3') . ' AND id_lang=' . (int)$id_lang;
            $leopartsfilter_year = Db::getInstance()->getRow($sql);
            if ($leopartsfilter_year) {
                $query .= ' AND level3 LIKE "%{\"'.(int)Tools::getValue('lv3').'\":\"'.$leopartsfilter_year['name'].'\"}%"';
            }
        }

        if (Tools::getValue('lv4')) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_device_lang WHERE id_leopartsfilter_device='.(int)Tools::getValue('lv4') . ' AND id_lang=' . (int)$id_lang;
            $leopartsfilter_device = Db::getInstance()->getRow($sql);
            if ($leopartsfilter_device) {
                $query .= ' AND level4 LIKE "%{\"'.(int)Tools::getValue('lv4').'\":\"'.$leopartsfilter_device['name'].'\"}%"';
            }
        }

        if (Tools::getValue('lv5')) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_level5_lang WHERE id_leopartsfilter_level5='.(int)Tools::getValue('lv5') . ' AND id_lang=' . (int)$id_lang;
            $leopartsfilter_level5 = Db::getInstance()->getRow($sql);
            if ($leopartsfilter_level5) {
                $query .= ' AND level5 LIKE "%{\"'.(int)Tools::getValue('lv5').'\":\"'.$leopartsfilter_level5['name'].'\"}%"';
            }
        }

        if (Tools::getValue('rowstatus') != '') {
            $query .= ' AND status = ' . (int)Tools::getValue('rowstatus');
        }

        if (Tools::getValue('limit')) {
            if (Tools::getValue('limit') == -1) {

            } else {
                if (Tools::getValue('page')) {
                    $page = Tools::getValue('page') - 1;
                    if ($page == 0) {
                        $page = 1;
                    }
                    $start = Tools::getValue('limit') * $page;
                    $query .= ' LIMIT ' . $start . ',' . Tools::getValue('limit');
                } else {
                    $query .= ' LIMIT ' . Tools::getValue('limit');
                }
            } 
        } else {
            $query .= ' LIMIT 200';
        }

        $results = Db::getInstance()->executeS($query);
        for ($i=0; $i<count($results); $i++) {
            $results[$i]['level1'] = json_decode($results[$i]['level1']);
            if ($this->count_field >=2) {
                $results[$i]['level2'] = json_decode($results[$i]['level2']);
            }
            if ($this->count_field >=3) {
                $results[$i]['level3'] = json_decode($results[$i]['level3']);
            }
            if ($this->count_field >=4) {
                $results[$i]['level4'] = json_decode($results[$i]['level4']);
            }
            if ($this->count_field >=5) {
                $results[$i]['level5'] = json_decode($results[$i]['level5']);
            } else {
                unset($results[$i]['level5']);
            }
        }

        if (Tools::getValue('limit') == -1) {
            $pagination = '';
        } else {
            $pagination = $this->getPagination();
        }
        
        
        $sql = 'SELECT a.*, b.name FROM '._DB_PREFIX_.'leopartsfilter_make a ';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_make_lang b ON (b.`id_leopartsfilter_make` = a.`id_leopartsfilter_make` AND b.`id_lang` ='.$id_lang.')';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_make_shop s ON (a.id_leopartsfilter_make = s.id_leopartsfilter_make AND id_shop = '.$id_shop.')';
        $leopartsfilter_make = Db::getInstance()->executeS($sql);

        $leopartsfilter_model = '';
        if (Tools::getValue('lv1')) {
            $sql = 'SELECT a.*, b.name FROM '._DB_PREFIX_.'leopartsfilter_model a ';
            $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_model_lang b ON (b.`id_leopartsfilter_model` = a.`id_leopartsfilter_model` AND b.`id_lang` ='.$id_lang.')';
            $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_model_shop s ON (a.id_leopartsfilter_model = s.id_leopartsfilter_model AND id_shop = '.$id_shop.')';
            $sql .= ' WHERE a.id_leopartsfilter_make=' .(int)Tools::getValue('lv1');
            $leopartsfilter_model = Db::getInstance()->executeS($sql);
        }

        $leopartsfilter_year = '';
        if (Tools::getValue('lv2')) {
            $sql = 'SELECT a.*, b.name FROM '._DB_PREFIX_.'leopartsfilter_year a ';
            $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_year_lang b ON (b.`id_leopartsfilter_year` = a.`id_leopartsfilter_year` AND b.`id_lang` ='.$id_lang.')';
            $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_year_shop s ON (a.id_leopartsfilter_year = s.id_leopartsfilter_year AND id_shop = '.$id_shop.')';
            $sql .= ' WHERE a.id_leopartsfilter_make=' .(int)Tools::getValue('lv1') . ' AND a.id_leopartsfilter_model='.(int)Tools::getValue('lv2');
            $leopartsfilter_year = Db::getInstance()->executeS($sql);
        }

        $leopartsfilter_device = '';
        if (Tools::getValue('lv3')) {
            $sql = 'SELECT a.*, b.name FROM '._DB_PREFIX_.'leopartsfilter_device a ';
            $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_device_lang b ON (b.`id_leopartsfilter_device` = a.`id_leopartsfilter_device` AND b.`id_lang` ='.$id_lang.')';
            $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_device_shop s ON (a.id_leopartsfilter_device = s.id_leopartsfilter_device AND id_shop = '.$id_shop.')';
            $sql .= ' WHERE a.id_leopartsfilter_make=' .(int)Tools::getValue('lv1') . ' AND a.id_leopartsfilter_model='.(int)Tools::getValue('lv2') . ' AND a.id_leopartsfilter_year='.(int)Tools::getValue('lv3');
            $leopartsfilter_device = Db::getInstance()->executeS($sql);
        }

        $leopartsfilter_level5 = '';
        if (Tools::getValue('lv4')) {
            $sql = 'SELECT a.*, b.name FROM '._DB_PREFIX_.'leopartsfilter_level5 a ';
            $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_level5_lang b ON (b.`id_leopartsfilter_level5` = a.`id_leopartsfilter_level5` AND b.`id_lang` ='.$id_lang.')';
            $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_level5_shop s ON (a.id_leopartsfilter_level5 = s.id_leopartsfilter_level5 AND id_shop = '.$id_shop.')';
            $sql .= ' WHERE a.id_leopartsfilter_make=' .(int)Tools::getValue('lv1') . ' AND a.id_leopartsfilter_model='.(int)Tools::getValue('lv2') . ' AND a.id_leopartsfilter_year='.(int)Tools::getValue('lv3') . ' AND a.id_leopartsfilter_device='.(int)Tools::getValue('lv4');
            $leopartsfilter_level5 = Db::getInstance()->executeS($sql);
        }

        $this->context->smarty->assign(array(
            'lang' => count($lang),
            'lv1' => (int)Tools::getValue('lv1'),
            'lv2' => (int)Tools::getValue('lv2'),
            'lv3' => (int)Tools::getValue('lv3'),
            'lv4' => (int)Tools::getValue('lv4'),
            'lv5' => (int)Tools::getValue('lv5'),
            'rowstatus' => Tools::getValue('rowstatus') == false ? -1 : Tools::getValue('rowstatus'),
            'leopartsfilter_make' => $leopartsfilter_make,
            'leopartsfilter_model' => $leopartsfilter_model,
            'leopartsfilter_year' => $leopartsfilter_year,
            'leopartsfilter_device' => $leopartsfilter_device,
            'leopartsfilter_level5' => $leopartsfilter_level5,
            'pagination' => $pagination,
            'data' => $results,
            'level' => $this->count_field,
            'token' => Tools::getAdminTokenLite('AdminLeopartsfilterImport'),
            'url' => 'index.php?controller=AdminLeopartsfilterImport&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterImport'),
            'export_url' => 'index.php?controller=AdminLeopartsfilterExport&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterExport').'&lv1='.(int)Tools::getValue('lv1').'&lv2='.(int)Tools::getValue('lv2').'&lv3='.(int)Tools::getValue('lv3').'&lv4='.(int)Tools::getValue('lv4').'&lv5='.(int)Tools::getValue('lv5').'&rowstatus='.(int)Tools::getValue('rowstatus').'&action=showfile',
            'edit_link' => 'index.php?controller=AdminLeopartsfilterImportEdit&updateleopartsfilter_import=&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterImportEdit')
        ));

        return parent::initContent();
    }


    public function updateLevel1Data($id)
    {
        $lang = Language::getLanguages(false);
        $query = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_import WHERE id=' . $id;
        $arr = Db::getInstance()->executeS($query);
        for ($i=0; $i<count($arr); $i++) {
            $arr[$i]['level1'] = (array)json_decode($arr[$i]['level1']);
            $arr[$i]['level2'] = (array)json_decode($arr[$i]['level2']);
            $arr[$i]['level3'] = (array)json_decode($arr[$i]['level3']);
            $arr[$i]['level4'] = (array)json_decode($arr[$i]['level4']);
            $arr[$i]['level5'] = (array)json_decode($arr[$i]['level5']);
        }
        $level1_name = '';
        foreach ($arr[0]['level1'] as $key => $value) {
            if ($level1_name == '') {
                $level1_name = $value;
            }
        }
        $id_lv1 = 0;
        $query = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_make WHERE id_leopartsfilter_make IN (SELECT id_leopartsfilter_make from '._DB_PREFIX_.'leopartsfilter_make_lang WHERE name = "'. trim($level1_name) .'")';
        $results = Db::getInstance()->executeS($query);
        if ($results == false) {
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_make (position,active,date_add) VALUE (0,1,"'.date("Y-m-d").'")';
            Db::getInstance()->execute($sql);
            $id_lv1 = (int)Db::getInstance()->Insert_ID();
            $shop = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_make_shop (id_leopartsfilter_make,id_shop) VALUE ('.$id_lv1.','.(int)Context::getContext()->shop->id.')';
            Db::getInstance()->execute($shop);
            $arr_lang = array();
            for ($i=0; $i<count($lang); $i++) {
                $id_lang = $lang[$i]['id_lang'];
                foreach ($arr[0]['level1'] as $key => $value) {
                    if ($id_lang == $key || !isset($arr[0]['level1'][$id_lang])) {
                        $arr_lang[] = '('.$id_lv1.',"'. pSQL($value) .'",'.$id_lang.')';
                    }
                }
            }
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_make_lang (id_leopartsfilter_make,name,id_lang) VALUE ' . implode(',', $arr_lang);
            Db::getInstance()->execute($sql);
        } else {
            $id_lv1 = $results[0]['id_leopartsfilter_make'];
        }
        $id_lv2 = 0;
        if ($this->count_field >= 2) {
            $level2_name = '';
            foreach ($arr[0]['level2'] as $key => $value) {
                if ($level2_name == '') {
                    $level2_name = $value;
                }
            }
            $query = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_model WHERE id_leopartsfilter_model IN (SELECT id_leopartsfilter_model from '._DB_PREFIX_.'leopartsfilter_model_lang WHERE name = "'. trim($level2_name) .'") AND id_leopartsfilter_make = ' . $id_lv1;
            $results = Db::getInstance()->executeS($query);

            if ($results == false) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_model (position,active,date_add,id_leopartsfilter_make) VALUE (0,1,"'.date("Y-m-d").'",'.$id_lv1.')';
                Db::getInstance()->execute($sql);
                $id_lv2 = (int)Db::getInstance()->Insert_ID();
                $shop = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_model_shop (id_leopartsfilter_model,id_shop) VALUE ('.$id_lv2.','.(int)Context::getContext()->shop->id.')';
                Db::getInstance()->execute($shop);
                $arr_lang = array();
                for ($i=0; $i<count($lang); $i++) {
                    $id_lang = $lang[$i]['id_lang'];
                    foreach ($arr[0]['level2'] as $key => $value) {
                        if ($id_lang == $key || !isset($arr[0]['level2'][$id_lang])) {
                            $arr_lang[] = '('.$id_lv2.',"'. pSQL($value) .'",'.$id_lang.')';
                        }
                    }
                }
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_model_lang (id_leopartsfilter_model,name,id_lang) VALUE ' . implode(',', $arr_lang);
                Db::getInstance()->execute($sql);
            } else {
                $id_lv2 = $results[0]['id_leopartsfilter_model'];
            }
        }
        $id_lv3 = 0;
        if ($this->count_field >= 3) {
            $level3_name = '';
            foreach ($arr[0]['level3'] as $key => $value) {
                if ($level3_name == '') {
                    $level3_name = $value;
                }
            }
            $query = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_year WHERE id_leopartsfilter_year IN (SELECT id_leopartsfilter_year from '._DB_PREFIX_.'leopartsfilter_year_lang WHERE name = "'. trim($level3_name) .'") AND id_leopartsfilter_model = ' . $id_lv2 . ' AND id_leopartsfilter_make = ' . $id_lv1;
            $results = Db::getInstance()->executeS($query);
            if ($results == false) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_year (position,active,date_add,id_leopartsfilter_make,id_leopartsfilter_model) VALUE (0,1,"'.date("Y-m-d").'",'.$id_lv1.','.$id_lv2.')';
                Db::getInstance()->execute($sql);
                $id_lv3 = (int)Db::getInstance()->Insert_ID();
                $shop = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_year_shop (id_leopartsfilter_year,id_shop) VALUE ('.$id_lv3.','.(int)Context::getContext()->shop->id.')';
                Db::getInstance()->execute($shop);

                $arr_lang = array();
                for ($i=0; $i<count($lang); $i++) {
                    $id_lang = $lang[$i]['id_lang'];
                    foreach ($arr[0]['level3'] as $key => $value) {
                        if ($id_lang == $key || !isset($arr[0]['level3'][$id_lang])) {
                            $arr_lang[] = '('.$id_lv3.',"'. pSQL($value) .'",'.$id_lang.')';
                        }
                    }
                }
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_year_lang (id_leopartsfilter_year,name,id_lang) VALUE ' . implode(',', $arr_lang);
                Db::getInstance()->execute($sql);
            } else {
                $id_lv3 = $results[0]['id_leopartsfilter_year'];
            }
        }
        $id_lv4 = 0;
        if ($this->count_field >= 4) {
            $level4_name = '';
            foreach ($arr[0]['level4'] as $key => $value) {
                if ($level4_name == '') {
                    $level4_name = $value;
                }
            }
            $query = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_device WHERE id_leopartsfilter_device IN (SELECT id_leopartsfilter_device from '._DB_PREFIX_.'leopartsfilter_device_lang WHERE name = "'. trim($level4_name) .'") AND id_leopartsfilter_model = ' . $id_lv2 . ' AND id_leopartsfilter_make = ' . $id_lv1 . ' AND id_leopartsfilter_year = ' . $id_lv3;
            $results = Db::getInstance()->executeS($query);

            if ($results == false) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_device (position,active,date_add,id_leopartsfilter_make,id_leopartsfilter_model,id_leopartsfilter_year) VALUE (0,1,"'.date("Y-m-d").'",'.$id_lv1.','.$id_lv2.','.$id_lv3.')';

                Db::getInstance()->execute($sql);
                $id_lv4 = (int)Db::getInstance()->Insert_ID();
                $shop = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_device_shop (id_leopartsfilter_device,id_shop) VALUE ('.$id_lv4.','.(int)Context::getContext()->shop->id.')';
                Db::getInstance()->execute($shop);

                $arr_lang = array();
                for ($i=0; $i<count($lang); $i++) {
                    $id_lang = $lang[$i]['id_lang'];
                    foreach ($arr[0]['level4'] as $key => $value) {
                        if ($id_lang == $key || !isset($arr[0]['level4'][$id_lang])) {
                            $arr_lang[] = '('.$id_lv4.',"'. pSQL($value) .'",'.$id_lang.')';
                        }
                    }
                }
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_device_lang (id_leopartsfilter_device,name,id_lang) VALUE ' . implode(',', $arr_lang);
                Db::getInstance()->execute($sql);
            } else {
                $id_lv4 = $results[0]['id_leopartsfilter_device'];
            }
        }


        $id_lv5 = 0;
        if ($this->count_field >= 5) {
            $level5_name = '';
            foreach ($arr[0]['level5'] as $key => $value) {
                if ($level5_name == '') {
                    $level5_name = $value;
                }
            }
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_level5 WHERE id_leopartsfilter_level5 IN (SELECT id_leopartsfilter_level5 from '._DB_PREFIX_.'leopartsfilter_level5_lang WHERE name = "'. trim($level5_name) .'") AND id_leopartsfilter_model = ' . $id_lv2 . ' AND id_leopartsfilter_make = ' . $id_lv1 . ' AND id_leopartsfilter_year = ' . $id_lv3 . ' AND id_leopartsfilter_device = ' . $id_lv4;
            $results = Db::getInstance()->executeS($sql);
            if ($results == false) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_level5 (position,active,date_add,id_leopartsfilter_make,id_leopartsfilter_model,id_leopartsfilter_year,id_leopartsfilter_device) VALUE (0,1,"'.date("Y-m-d").'",'.$id_lv1.','.$id_lv2.','.$id_lv3.','.$id_lv4.')';
                Db::getInstance()->execute($sql);
                $id_lv5 = (int)Db::getInstance()->Insert_ID();
                $shop = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_level5_shop (id_leopartsfilter_level5,id_shop) VALUE ('.$id_lv5.','.(int)Context::getContext()->shop->id.')';
                Db::getInstance()->execute($shop);

                $arr_lang = array();
                for ($i=0; $i<count($lang); $i++) {
                    $id_lang = $lang[$i]['id_lang'];
                    foreach ($arr[0]['level5'] as $key => $value) {
                        if ($id_lang == $key || !isset($arr[0]['level5'][$id_lang])) {
                            $arr_lang[] = '('.$id_lv5.',"'. pSQL($value) .'",'.$id_lang.')';
                        }
                    }
                }
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_level5_lang (id_leopartsfilter_level5,name,id_lang) VALUE ' . implode(',', $arr_lang);
                Db::getInstance()->execute($sql);
            } else {
                $id_lv5 = $results[0]['id_leopartsfilter_level5'];
            }
        }

        if ($arr[0]['id_product']) {
            $query = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_product WHERE id_product='.$arr[0]['id_product'].' AND id_leopartsfilter_make='.$id_lv1.' AND id_leopartsfilter_model=' . $id_lv2 . ' AND id_leopartsfilter_year='.$id_lv3.' AND id_leopartsfilter_device=' . $id_lv4.' AND id_leopartsfilter_level5=' . $id_lv5;
            $results = Db::getInstance()->executeS($query);
            if (!count($results)) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'leopartsfilter_product (id_product,id_leopartsfilter_make,id_leopartsfilter_model,id_leopartsfilter_year,id_leopartsfilter_device,id_leopartsfilter_level5) VALUE ('.$arr[0]['id_product'].','.$id_lv1.','.$id_lv2.','.$id_lv3.','.$id_lv4.','.$id_lv5.')';
                Db::getInstance()->execute($sql);
            }
        }
        
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'leopartsfilter_import SET status = 1 WHERE id=' . (int)$id;
        Db::getInstance()->execute($sql);
    }

    public function uploadDocument($file)
    {
        if (isset($_FILES[$file]) && $_FILES[$file]) {
            if ($_FILES[$file]['error'] > 0) {
                return '';
            } else {
                $allowed = array('xls');
                $filename = $_FILES[$file]['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array(Tools::strtolower($ext), $allowed)) {
                    return '';
                } else {
                    $dir = _PS_MODULE_DIR_ . $this->module->name . '/file_upload/';
                    $filename = strtotime(date('Y-m-d H:i:s')) . '_' . $_FILES[$file]['name'];
                    move_uploaded_file($_FILES[$file]['tmp_name'], $dir.$filename);
                    return $filename;
                }
            }
        }
    }

    public function getPagination()
    {
        $id_shop = (int)Context::getContext()->shop->id;
        $id_lang = (int)Context::getContext()->language->id;

        $pagination = array();

        $query = 'SELECT count(*) as total FROM '._DB_PREFIX_.'leopartsfilter_import WHERE 1 ';

        if (Tools::getValue('lv1')) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_make_lang WHERE id_leopartsfilter_make='.(int)Tools::getValue('lv1') . ' AND id_lang=' . (int)$id_lang;
            $leopartsfilter_make = Db::getInstance()->getRow($sql);
            if ($leopartsfilter_make) {
                $query .= ' AND level1 LIKE "%{\"'.(int)Tools::getValue('lv1').'\":\"'.$leopartsfilter_make['name'].'\"}%"';
            }
        }
        if (Tools::getValue('lv2')) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_model_lang WHERE id_leopartsfilter_model='.(int)Tools::getValue('lv2') . ' AND id_lang=' . (int)$id_lang;
            $leopartsfilter_model = Db::getInstance()->getRow($sql);
            if ($leopartsfilter_model) {
                $query .= ' AND level2 LIKE "%{\"'.(int)Tools::getValue('lv2').'\":\"'.$leopartsfilter_model['name'].'\"}%"';
            }
        }

        if (Tools::getValue('lv3')) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_year_lang WHERE id_leopartsfilter_year='.(int)Tools::getValue('lv3') . ' AND id_lang=' . (int)$id_lang;
            $leopartsfilter_year = Db::getInstance()->getRow($sql);
            if ($leopartsfilter_year) {
                $query .= ' AND level3 LIKE "%{\"'.(int)Tools::getValue('lv3').'\":\"'.$leopartsfilter_year['name'].'\"}%"';
            }
        }

        if (Tools::getValue('lv4')) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_device_lang WHERE id_leopartsfilter_device='.(int)Tools::getValue('lv4') . ' AND id_lang=' . (int)$id_lang;
            $leopartsfilter_device = Db::getInstance()->getRow($sql);
            if ($leopartsfilter_device) {
                $query .= ' AND level4 LIKE "%{\"'.(int)Tools::getValue('lv4').'\":\"'.$leopartsfilter_device['name'].'\"}%"';
            }
        }

        if (Tools::getValue('lv5')) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_level5_lang WHERE id_leopartsfilter_level5='.(int)Tools::getValue('lv5') . ' AND id_lang=' . (int)$id_lang;
            $leopartsfilter_level5 = Db::getInstance()->getRow($sql);
            if ($leopartsfilter_level5) {
                $query .= ' AND level5 LIKE "%{\"'.(int)Tools::getValue('lv5').'\":\"'.$leopartsfilter_level5['name'].'\"}%"';
            }
        }

        if (Tools::getValue('rowstatus') != '') {
            $query .= ' AND status = ' . (int)Tools::getValue('rowstatus');
        }

        $results_total = Db::getInstance()->executeS($query);

        if (count($results_total) == 0) {
            return '';
        }

        $total = $results_total[0]['total'];
        $limit = Tools::getValue('limit') ? Tools::getValue('limit') : 200;
        if ($total <= $limit) {
            $pagination['listpage'] = array(1);
            $pagination['totalpage'] = 1;
        } else {
            if ($total%$limit == 0) {
                $totalpage = ($total - ($total%$limit))/$limit;
            } else {
                $totalpage = ($total - ($total%$limit))/$limit + 1;
            }
            
            

            $pagination['listpage'] = array();
            $page_start = 1;
            if (Tools::getValue('page') > 4) {
                $page_start = Tools::getValue('page') - 3;
            }
            if ($page_start < 1) {
                $page_start = 1;
            }

            $end_page = $page_start + 6;

            for ($i=$page_start; $i <= $totalpage && $i <=$end_page; $i++) {
                $pagination['listpage'][] = $i;
            }

            $pagination['totalpage'] = $totalpage;
        }
        
        $pagination['page'] = Tools::getValue('page') ? Tools::getValue('page') : 1;
        $pagination['total_product'] = $total;
        
        $pagination['limit'] = $limit;
        
        $pagination['tpl'] = _PS_MODULE_DIR_ . 'leopartsfilter/views/templates/admin/pagination.tpl';

        return $pagination;
    }
}
