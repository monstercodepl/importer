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

class AdminLeopartsfilterExportController extends ModuleAdminController
{
    public $count_field;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
        $this->meta_title = $this->l('LEO PARTS FILTER EXPORT DATA');
        $this->count_field = (int) LeopartsfilterConfig::getInstance()->getConfig('PS_ALLOW_SEARCH_FORM', false, false);
    }
    
    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Administration');
        $this->toolbar_title[] = $this->l('LEO PARTS FILTER EXPORT DATA');
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
        $file_name = '';
        if (Tools::getValue('act') == 'export'){
           $file_name = $this->exportData(); 
        }
        $file_delete = $this->deleteFileExport();
        
        $lang = Language::getLanguages();
        $id_shop = (int)Context::getContext()->shop->id;
        $id_lang = (int)Context::getContext()->language->id;

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
            'baee_url' => Tools::getHttpHost(true).__PS_BASE_URI__.'/modules/leopartsfilter/export_file/',
            'file_name' => $file_name,
            'export_files' => $this->getAllFileExport(),
            'file_delete' => $file_delete,
            'url_export' => 'index.php?controller=AdminLeopartsfilterImport&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterImport'),
            'url' => 'index.php?controller=AdminLeopartsfilterExport&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterExport'),
            'leopartsfilter_make' => $leopartsfilter_make,
            'leopartsfilter_model' => $leopartsfilter_model,
            'leopartsfilter_year' => $leopartsfilter_year,
            'leopartsfilter_device' => $leopartsfilter_device,
            'leopartsfilter_level5' => $leopartsfilter_level5,
            'level' => (int) LeopartsfilterConfig::getInstance()->getConfig('PS_ALLOW_SEARCH_FORM', false, false),
            'lang' => count($lang),
            'lv1' => (int)Tools::getValue('lv1'),
            'lv2' => (int)Tools::getValue('lv2'),
            'lv3' => (int)Tools::getValue('lv3'),
            'lv4' => (int)Tools::getValue('lv4'),
            'lv5' => (int)Tools::getValue('lv5'),
            'token' => Tools::getAdminTokenLite('AdminLeopartsfilterExport'),
        ));

        return parent::initContent();
    }

    public function getAllFileExport()
    {
        $path = _PS_MODULE_DIR_ . 'leopartsfilter/export_file';
        $files = array_diff(scandir($path), array('.', '..'));
        $arr_file = array();

        $i=0;
        foreach ($files as $key => $value) {
            if (strpos($value, '.xls') != false) {
                $create = date("Y-m-d H:i:s", str_replace('_leopartsfilter.xls', '', $value));
                $arr_file[$i]['name'] = $value;
                $arr_file[$i]['create'] = $create;
                $arr_file[$i]['url'] = Tools::getHttpHost(true).__PS_BASE_URI__.'modules/leopartsfilter/export_file/' . $value;
                $i++;
            }
        }
        $j = 1;
        $arr = array();
        for ($i=count($arr_file)-1; $i>=0; $i--) {
            $arr[$j] = $arr_file[$i];
            $j++;
        }
        return $arr;
    }

    public function deleteFileExport()
    {
        if (Tools::getValue('filename') && Tools::getValue('action') == 'deletefile') {
            if (file_exists(_PS_MODULE_DIR_ . 'leopartsfilter/export_file/' . Tools::getValue('filename'))) {
                $status = unlink(_PS_MODULE_DIR_ . 'leopartsfilter/export_file/' . Tools::getValue('filename'));
                return Tools::getValue('filename');
            }
        }
        return '';
    }

    public function exportData()
    {
        $lang = Language::getLanguages();
        $id_shop = (int)Context::getContext()->shop->id;
        $id_lang = (int)Context::getContext()->language->id;

        $query = 'SELECT * FROM '._DB_PREFIX_.'leopartsfilter_product WHERE 1 ';

        if (Tools::getValue('lv1')) {
            $query .= ' AND id_leopartsfilter_make = ' . (int)Tools::getValue('lv1');
        }
        if (Tools::getValue('lv2')) {
            $query .= ' AND id_leopartsfilter_model = ' . (int)Tools::getValue('lv2');
        }
        if (Tools::getValue('lv3')) {
            $query .= ' AND id_leopartsfilter_year = ' . (int)Tools::getValue('lv3');
        }
        if (Tools::getValue('lv4')) {
            $query .= ' AND id_leopartsfilter_device = ' . (int)Tools::getValue('lv4');
        }
        if (Tools::getValue('lv5')) {
            $query .= ' AND id_leopartsfilter_level5 = ' . (int)Tools::getValue('lv5');
        }
        $results = Db::getInstance()->executeS($query);
        
        if (!$results) {
            return;
        }

        $arr_product = array();
        foreach ($results as $key => $value) {
            if (!in_array($value['id_product'], $arr_product)) {
                $arr_product[] = $value['id_product'];
            }
        }

        $sql = 'SELECT id_product, name FROM '._DB_PREFIX_.'product_lang WHERE id_lang = ' . $id_lang . ' AND id_product IN ('.implode(",", $arr_product).')';
        $products = Db::getInstance()->executeS($sql);

        $id_shop = (int)Context::getContext()->shop->id;
        $id_lang = (int)Context::getContext()->language->id;
        $sql = 'SELECT b.* FROM '._DB_PREFIX_.'leopartsfilter_make a ';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_make_lang b ON (b.`id_leopartsfilter_make` = a.`id_leopartsfilter_make`)';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_make_shop s ON (a.id_leopartsfilter_make = s.id_leopartsfilter_make AND id_shop = '.$id_shop.')';
        $leopartsfilter_make = Db::getInstance()->executeS($sql);
 
        $sql = 'SELECT b.* FROM '._DB_PREFIX_.'leopartsfilter_model a ';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_model_lang b ON (b.`id_leopartsfilter_model` = a.`id_leopartsfilter_model`)';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_model_shop s ON (a.id_leopartsfilter_model = s.id_leopartsfilter_model AND id_shop = '.$id_shop.')';
        $leopartsfilter_model = Db::getInstance()->executeS($sql);

        $sql = 'SELECT b.* FROM '._DB_PREFIX_.'leopartsfilter_year a ';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_year_lang b ON (b.`id_leopartsfilter_year` = a.`id_leopartsfilter_year`)';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_year_shop s ON (a.id_leopartsfilter_year = s.id_leopartsfilter_year AND id_shop = '.$id_shop.')';
        $leopartsfilter_year = Db::getInstance()->executeS($sql);

        $sql = 'SELECT a.*, b.name FROM '._DB_PREFIX_.'leopartsfilter_device a ';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_device_lang b ON (b.`id_leopartsfilter_device` = a.`id_leopartsfilter_device`)';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_device_shop s ON (a.id_leopartsfilter_device = s.id_leopartsfilter_device AND id_shop = '.$id_shop.')';

        $leopartsfilter_device = Db::getInstance()->executeS($sql);

        $sql = 'SELECT a.*, b.name FROM '._DB_PREFIX_.'leopartsfilter_level5 a ';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_level5_lang b ON (b.`id_leopartsfilter_level5` = a.`id_leopartsfilter_level5`)';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'leopartsfilter_level5_shop s ON (a.id_leopartsfilter_level5 = s.id_leopartsfilter_level5 AND id_shop = '.$id_shop.')';
        $leopartsfilter_level5 = Db::getInstance()->executeS($sql);
        
        foreach ($results as $key => $value) {
            foreach ($products as $p) {
                if ($value['id_product'] == $p['id_product']) {
                    $results[$key]['product_name'] = $p['name'];
                }
            }
            $arr = array();
            foreach ($leopartsfilter_make as  $value_1) {
                if ($value['id_leopartsfilter_make'] == $value_1['id_leopartsfilter_make']) {
                    $arr['level1'][] = $value_1['name'];
                }
            }
            foreach ($leopartsfilter_model as $value_2) {
                if ($value['id_leopartsfilter_model'] == $value_2['id_leopartsfilter_model']) {
                    $arr['level2'][] = $value_2['name'];
                }
            }
            foreach ($leopartsfilter_year as $value_2) {
                if ($value['id_leopartsfilter_year'] == $value_2['id_leopartsfilter_year']) {
                    $arr['level3'][] = $value_2['name'];
                }
            }
            foreach ($leopartsfilter_device as $value_2) {
                if ($value['id_leopartsfilter_device'] == $value_2['id_leopartsfilter_device']) {
                    $arr['level4'][] = $value_2['name'];
                }
            }
            foreach ($leopartsfilter_level5 as $value_2) {
                if ($value['id_leopartsfilter_level5'] == $value_2['id_leopartsfilter_level5']) {
                    $arr['level5'][] = $value_2['name'];
                }
            }
            $results[$key]['content'] = $arr;
        }

        if (!$results) {
            return;
        }
        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                                     ->setLastModifiedBy("Maarten Balliauw")
                                     ->setTitle("PHPExcel Test Document")
                                     ->setSubject("PHPExcel Test Document")
                                     ->setDescription("Test document for PHPExcel, generated using PHP classes.")
                                     ->setKeywords("office PHPExcel php")
                                     ->setCategory("Test result file");
                                                        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Id Product')
                    ->setCellValue('B1', 'Product name');
        $char_code = 66;
        for ($i=0; $i < count($lang); $i++) {
            $char_code ++;
            $column = chr($char_code) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, 'Level 1');
        }
        for ($i=0; $i < count($lang); $i++) {
            $char_code ++;
            $column = chr($char_code) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, 'Level 2');
        }
        for ($i=0; $i < count($lang); $i++) {
            $char_code ++;
            $column = chr($char_code) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, 'Level 3');
        }
        for ($i=0; $i < count($lang); $i++) {
            $char_code ++;
            $column = chr($char_code) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, 'Level 4');
        }
        for ($i=0; $i < count($lang); $i++) {
            $char_code ++;
            $column = chr($char_code) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, 'Level 5');
        }

        $row = 1;
        foreach ($results as $key => $value) {
            $row ++;
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$row, $value['id_product'])
                    ->setCellValue('B'.$row, $value['product_name']);

            $char_code = 66;
            foreach ($value['content'] as $val_content) {
                foreach ($val_content as $val) {
                    $char_code ++;
                    $column = chr($char_code).$row;
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column, (string)$val);
                }
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle('leopartsfilter Export');
        $objPHPExcel->setActiveSheetIndex(0);
 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $file_name = time() . '_leopartsfilter.xls';
        $file = _PS_MODULE_DIR_ . 'leopartsfilter/export_file/' . $file_name;
        $objWriter->save($file);

        return $file_name;
    }
    
}
