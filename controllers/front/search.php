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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/product.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/make.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/model.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/year.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/device.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/level5.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/config.php');

class LeopartsfilterSearchModuleFrontController extends ModuleFrontController
{
    public $php_self;
    protected $template_path = '';
    public $mod_product;
    public function __construct()
    {
        $this->mod_product = new LeopartsfilterProduct();
        $this->mod_config = LeopartsfilterConfig::getInstance();
        parent::__construct();
        $this->getDataLevel();
    }

    public function initContent()
    {
        $id_make = Tools::getValue('make');
        $id_model = Tools::getValue('model');
        $id_year = Tools::getValue('year');
        $id_device = Tools::getValue('device');
        $id_level5 = Tools::getValue('level5');
        $action = Tools::getValue('action');
        $s = Tools::getValue('s');
        # Default value
        $listing = array();
        $products = array();
        $results = $this->mod_product->getList(null, $id_make, $id_model, $id_year, $id_device, $id_level5, $s);
        $where = '';
        $id_products = '';
        foreach ($results as $row) {
            $id_products .= ', ' . $row['id_product'];
        }
        $show_all = count($results);
        $id_products = ltrim($id_products, ',');

        if (!empty($id_products) && !$s) {
            $where = 'WHERE  p.id_product IN  (' . pSQL($id_products) . ')';
            if ($s!= null) {
                $where .= ' OR pl.name like "%'.$s.'%" ';
            }
            $all_products = $this->getProducts($where, (int) Context::getContext()->language->id, 1, $show_all, null, null);
            $all_products = $this->loadProductDetail($all_products);
            $pagination = $this->getPagination($all_products);

            $page = (int) Tools::getValue('page') ? (int) Tools::getValue('page') : 1;
            $limit = Configuration::get('PS_PRODUCTS_PER_PAGE', 12);
            $products = $this->getProducts($where, (int) Context::getContext()->language->id, $page, $limit, null, null);
            $products = $this->loadProductDetail($products);
            
            $listing = array(
                'label' => '',
                'products' => $products,
                'sort_orders' => array(),
                'sort_selected' => '',
                'rendered_facets' => '',
                'rendered_active_filters' => '',
                'js_enabled' => false,
                'pagination' => $pagination,
                'total' => count($all_products)
            );
        } else {
            if ($s!= null) {
                $where = 'WHERE pl.name like "%'.$s.'%" ';
                if (!empty($id_products)) {
                    $where .= ' OR p.id_product IN  (' . pSQL($id_products) . ') ';
                }
                $all_products = $this->getProducts($where, (int) Context::getContext()->language->id, 1, $show_all, null, null);
                $all_products = $this->loadProductDetail($all_products);
                $pagination = $this->getPagination($all_products);

                $page = (int) Tools::getValue('page') ? (int) Tools::getValue('page') : 1;
                $limit = Configuration::get('PS_PRODUCTS_PER_PAGE', 12);
                $products = $this->getProducts($where, (int) Context::getContext()->language->id, $page, $limit, null, null);
                $products = $this->loadProductDetail($products);
                $listing = array(
                    'label' => '',
                    'products' => $products,
                    'sort_orders' => array(),
                    'sort_selected' => '',
                    'rendered_facets' => '',
                    'rendered_active_filters' => '',
                    'js_enabled' => false,
                    'pagination' => $pagination,
                );
            }
        }
// echo '<pre>';
// print_r($pagination);
// die();
        $vars = array(
            'top_text' => $this->mod_config->getConfig('PS_MMY_TOP_TEXT'),
            'products' => $products,
            'search_products' => $products,
            'listing' => $listing,
            'orderby' => Tools::getValue('order'),
            'total_products' => count($products),
            'id_make' => $id_make,
            'id_model' => $id_model,
            'id_year' => $id_year,
            'id_device' => $id_device,
            'id_level5' => $id_level5,
            'order' => Tools::getValue('order')
        );

        $cookie = $this->context->cookie;

        $cookieExists = isset($cookie->savedMotorcycles);

        $this->mod_make = new LeopartsfilterMake();
        $this->mod_model = new LeopartsfilterModel();
        $this->mod_year = new LeopartsfilterYear();
        $this->mod_device = new LeopartsfilterDevice();

        $results = $this->mod_make->getList($active = 1);
        foreach ($results as $row) {
            $make_list[] = array(
                'mid' => $row['id_leopartsfilter_make'],
                'name' => $row['name']
            );
        }

        $makeKey = array_search($id_make, array_column($make_list, 'mid'));

        $results = $this->mod_model->getList($active = 1);
        foreach ($results as $row) {
            $model_list[] = array(
                'mid' => $row['id_leopartsfilter_model'],
                'name' => $row['name']
            );
        }

        $modelKey = array_search($id_model, array_column($model_list, 'mid'));

        $results = $this->mod_year->getList($active = 1);
        foreach ($results as $row) {
            $year_list[] = array(
                'mid' => $row['id_leopartsfilter_year'],
                'name' => $row['name']
            );
        }

        $yearKey = array_search($id_year, array_column($year_list, 'mid'));

        $results = $this->mod_device->getList($active = 1);
        foreach ($results as $row) {
            $device_list[] = array(
                'mid' => $row['id_leopartsfilter_device'],
                'name' => $row['name']
            );
        }

        $deviceKey = array_search($id_device, array_column($device_list, 'mid'));

        $motorcycle = array( 
            'id_make' => $id_make,
            'name_make' => $make_list[$makeKey]['name'],
            'id_model' => $id_model,
            'name_model' => $model_list[$modelKey]['name'],
            'id_year' => $id_year,
            'name_year' => $year_list[$yearKey]['name'],
            'id_device' => $id_device,
            'name_device' => $device_list[$deviceKey]['name']
        );


        if($cookieExists) {
            $cookieMotorcycles = $cookie->savedMotorcycles;
            $savedMotorcycles = unserialize($cookieMotorcycles);

            $savedMotorcycles[] = $motorcycle;

            $arr = array_intersect_key($savedMotorcycles, array_unique(array_map(function ($el) {
                return $el['id_device'];
            }, $savedMotorcycles)));

            $cookie->savedMotorcycles = serialize($arr);
        }
        if(!$cookieExists) {
            $savedMotorcycles = array($motorcycle);

            $serializedSavedMotorcycles = serialize($savedMotorcycles);
            $cookie->savedMotorcycles = $serializedSavedMotorcycles;
        }

        $this->context->smarty->assign($vars);

        if ($action == 'ajaxsearch') {
            $this->setTemplate('module:leopartsfilter/views/templates/front/search.tpl');
        } else {
            $this->setTemplate('module:leopartsfilter/views/templates/front/view-search.tpl');
        }
        
        parent::initContent();
    }

    public function getProducts($where, $id_lang, $page, $limit, $order_by = null, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, $check_access = true, Context $context = null)
    {
        # validate module
        unset($check_access);
        if (!$context) {
            $context = Context::getContext();
        }
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }
        if ($page < 1) {
            $page = 1;
        }

        $id_supplier = (int) Tools::getValue('id_supplier');

        /* Return only the number of products */
        if ($get_total) {
            $sql = 'SELECT COUNT(cp.`id_product`) AS total
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`
                ' . $where . '
                ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') .
                    ($active ? ' AND product_shop.`active` = 1' : '') .
                    ($id_supplier ? 'AND p.id_supplier = ' . (int) $id_supplier : '');
            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }
        $sql = 'SELECT DISTINCT p.id_product, p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`id_product_attribute`, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
                pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image`,
                il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
                DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
                INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . '
                    DAY)) > 0 AS new, product_shop.price AS orderprice
            FROM `' . _DB_PREFIX_ . 'category_product` cp
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p
                ON p.`id_product` = cp.`id_product`
            ' . Shop::addSqlAssociation('product', 'p') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
            ON (p.`id_product` = pa.`id_product`)
            ' . Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1') . '
            ' . Product::sqlStock('p', 'product_attribute_shop', false, $context->shop) . '
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                ON (product_shop.`id_category_default` = cl.`id_category`
                AND cl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i
                ON (i.`id_product` = p.`id_product`)' .
                Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
                ON (image_shop.`id_image` = il.`id_image`
                AND il.`id_lang` = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
                ON m.`id_manufacturer` = p.`id_manufacturer`
            ' . $where . '
            AND  product_shop.`id_shop` = ' . (int) $context->shop->id . '
            AND (pa.id_product_attribute IS NULL OR product_attribute_shop.id_shop=' . (int) $context->shop->id . ')
            AND (i.id_image IS NULL OR image_shop.id_shop=' . (int) $context->shop->id . ')
                ' . ($active ? ' AND product_shop.`active` = 1' : '')
                . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                . ($id_supplier ? ' AND p.id_supplier = ' . (int) $id_supplier : '');
        
            $orderby = 'pl.name';
            if (Tools::getValue('order') == 'product.position.asc') {
                $orderby = 'cp.position ASC';
            }
            if (Tools::getValue('order') == 'product.name.asc') {
                $orderby = 'pl.name ASC';
            }
            if (Tools::getValue('order') == 'product.name.desc') {
                $orderby = 'pl.name DESC';
            }
            if (Tools::getValue('order') == 'product.price.asc') {
                $orderby = 'product_shop.price ASC';
            }
            if (Tools::getValue('order') == 'product.price.desc') {
                $orderby = 'product_shop.price DESC';
            }
            $sql .= ' ORDER BY ' . $orderby;

            $limit_from = ((int) $page - 1) * $limit;
            $sql .= ' LIMIT ' . $limit_from.','.$limit;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        // if ($order_by == 'orderprice') {
        //     Tools::orderbyPrice($result, $order_way);
        // }
        if (!$result) {
            return array();
        }
        return Product::getProductsProperties($id_lang, $result);
    }

    public function loadProductDetail($products)
    {
        #1.7
        $assembler = new ProductAssembler(Context::getContext());
        $presenterFactory = new ProductPresenterFactory(Context::getContext());
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(new ImageRetriever(Context::getContext()->link), Context::getContext()->link, new PriceFormatter(), new ProductColorsRetriever(), Context::getContext()->getTranslator());
        $products_for_template = array();
        foreach ($products as $rawProduct) {
            $products_for_template[] = $presenter->present($presentationSettings, $assembler->assembleProduct($rawProduct), Context::getContext()->language);
        }
        return $products_for_template;
    }

    public function getDataLevel()
    {
        $id_make = Tools::getValue('make');
        $id_model = Tools::getValue('model');
        $id_year = Tools::getValue('year');
        $id_device = Tools::getValue('device');
        $id_level5 = Tools::getValue('level5');

        $action = Tools::getValue('action');
        if ($action == 'adminajax') {
            $mod_config = LeopartsfilterConfig::getInstance();
            $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');

            if (Tools::getIsset('modelid') && Tools::getIsset('makeid') && Tools::getIsset('yearid') && Tools::getIsset('deviceid')) {
                $mod_level5 = new LeopartsfilterLevel5();
                $results = $mod_level5->getList($active = 1, null, null, Tools::getValue('makeid'), Tools::getValue('modelid'), Tools::getValue('yearid'), Tools::getValue('deviceid'));
                $arr = array(0 => array
                    (
                        'id_leopartsfilter_level5' => '',
                        'name' => $mod_config->getConfig('PS_MMY_LEVEL5_DEFAULT_TEXT', '', $id_lang)
                    ));
                foreach ($results as $key => $value) {
                    $arr[] = $value;
                }
                echo json_encode($arr);
                die();
            }

            if (Tools::getIsset('modelid') && Tools::getIsset('makeid') && Tools::getIsset('yearid')) {
                $mod_device = new LeopartsfilterDevice();
                $results = $mod_device->getList($active = 1, null, null, Tools::getValue('makeid'), Tools::getValue('modelid'), Tools::getValue('yearid'));
                $arr = array(0 => array
                    (
                        'id_leopartsfilter_device' => '',
                        'name' => $mod_config->getConfig('PS_MMY_DEVICE_DEFAULT_TEXT', '', $id_lang)
                    ));
                foreach ($results as $key => $value) {
                    $arr[] = $value;
                }
                echo json_encode($arr);
                die();
            }

            if (Tools::getValue('modelid') && Tools::getValue('modelid') != '' && Tools::getValue('makeid') && Tools::getValue('makeid') != '') {
                $mod_year = new LeopartsfilterYear();
                $results = $mod_year->getList($active = 1, null, null, Tools::getValue('makeid'), Tools::getValue('modelid'));
                $arr = array(0 => array
                    (
                        'id_leopartsfilter_year' => '',
                        'name' => $mod_config->getConfig('PS_MMY_YEAR_DEFAULT_TEXT', '', $id_lang)
                    ));
                foreach ($results as $key => $value) {
                    $arr[] = $value;
                }
                echo json_encode($arr);
                die();
            }

            if (Tools::getValue('makeid') && Tools::getValue('makeid') != '') {
                $active = 1;
                if (Tools::getValue('active') && (Tools::getValue('active') == 'all')) {
                    $active = null;
                }
                $mod_model = new LeopartsfilterModel();
                $results = $mod_model->getList($active, null, null, Tools::getValue('makeid'));
                $arr = array(0 => array
                    (
                        'id_leopartsfilter_model' => '',
                        'name' => $mod_config->getConfig('PS_MMY_MODEL_DEFAULT_TEXT', '', $id_lang)
                    ));
                foreach ($results as $key => $value) {
                    $arr[] = $value;
                }
                echo json_encode($arr);
                die();
            }
            echo '-99';
        }
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($configuration['tpl'], $this->getCacheId('leopartsfilter'))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration['data']));
        }

        return $this->fetch($configuration['tpl'], $this->getCacheId('leopartsfilter'));
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        return $configuration;
    }

    public function getPagination($results)
    {
        $url = Tools::getHttpHost(true).__PS_BASE_URI__ . 'index.php?fc=module&module=leopartsfilter&controller=search';
        if ((int) Tools::getValue('make')) {
            $url .= '&make='. (int) Tools::getValue('make');
        }
        if ((int) Tools::getValue('model')) {
            $url .= '&model='. (int) Tools::getValue('model');
        }
        if ((int) Tools::getValue('year')) {
            $url .= '&year='. (int) Tools::getValue('year');
        }
        if ((int) Tools::getValue('make')) {
            $url .= '&device='. (int) Tools::getValue('device');
        }
        if ((int) Tools::getValue('lv5')) {
            $url .= '&lv5='. (int) Tools::getValue('lv5');
        }
        if (Tools::getValue('order')){
            $url .= '&order='.Tools::getValue('order');
        }
        $resultsperpage = Configuration::get('PS_PRODUCTS_PER_PAGE', 12);
        $pages = array();
        $numpage = floor(count($results)/12);
        if (count($results) % $resultsperpage > 0) {
            $numpage++;
        }
        $c_page = (int) Tools::getValue('page');

        if ($c_page > 1 && $numpage > 0) {
            $page = (int)Tools::getValue('page') - 1;
            if ($page < 1) {
                $page = 1;
            }
            $arr = Array
                (
                    'type' => 'previous',
                    'page' => 1,
                    'clickable' => 1,
                    'current' => '',
                    'url' => $url . '&page='.$page
                );
            $pages[] = $arr;
        }
        

        for ($i=1; $i<=$numpage ; $i++) {
            $current = '';
            if ($c_page == $i || ($i == 1 && !$c_page)) {
                $current = 1;
            }
            $arr = Array
                (
                    'type' => 'page',
                    'page' => $i,
                    'clickable' => 1,
                    'current' => $current,
                    'url' => $url . '&page=' . $i
                );
            $pages[] = $arr;
        }
        if ($c_page < $numpage && $numpage > 0) {
            $page = (int)Tools::getValue('page') + 1;
            $arr = Array
                (
                    'type' => 'next',
                    'page' => $numpage,
                    'clickable' => 1,
                    'current' => '',
                    'url' => $url . '&page=' . $page
                );
            $pages[] = $arr;
        }

        $getpage = (int) Tools::getValue('page') ? (int) Tools::getValue('page') : 1;
        $totalItems = count($results);
        $itemsShownFrom = ($resultsperpage * ($getpage - 1)) + 1;
        $itemsShownTo = $resultsperpage * $getpage;

        return [
            'total_items' => $totalItems,
            'items_shown_from' => $itemsShownFrom,
            'items_shown_to' => ($itemsShownTo <= $totalItems) ? $itemsShownTo : $totalItems,
            'current_page' => (int) Tools::getValue('page') ? (int) Tools::getValue('page') : 1,
            'pages_count' => $numpage,
            'pages' => $pages,
            // Compare to 3 because there are the next and previous links
            'should_be_displayed' => (count($pages) > 1),
        ];
    }
}
