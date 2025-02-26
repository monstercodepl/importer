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

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/make.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/model.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/year.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/device.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/levellast.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/config.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/import.php');

class LeoPartsFilter extends Module
{

    public $mod_config;
    public $mod_make;
    public $mod_model;
    public $mod_year;
    public $mod_device;
    public $mod_level5;
    public $import;
    private $_html = '';

    public function __construct()
    {
        $this->name = 'leopartsfilter';
        $this->tab = 'front_office_features';
        $this->version = '3.2.15';
        $this->author = 'LeoTheme';
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Leo Parts Filter for searching cars');
        $this->description = $this->l('Find Car Parts Based On Make Model Year');
        $this->ps_versions_compliancy = array('min' => '1.5.6.1', 'max' => _PS_VERSION_);

        $this->mod_make = new LeopartsfilterMake();
        $this->mod_model = new LeopartsfilterModel();
        $this->mod_year = new LeopartsfilterYear();
        $this->mod_device = new LeopartsfilterDevice();
        $this->mod_level5 = new LeopartsfilterLevellast();
        $this->mod_import = new LeopartsfilterImport();
        $this->mod_config = LeopartsfilterConfig::getInstance();
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $res = true;
        $id_parent = Tab::getIdFromClassName('AdminParentModulesSf');
        $class = 'AdminLeopartsfilterConfiguration';
        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = $id_parent;
        $langs = Language::getLanguages(false);
        foreach ($langs as $l) {
            $tab1->name[$l['id_lang']] = $this->l('Leo Partsfilter Configuration');
        }
        $tab1->add(true, false);

        $this->installModuleTab('Make Management', 'make', '-1');
        $this->installModuleTab('Model Management', 'model', '-1');
        $this->installModuleTab('Year Management', 'year', '-1');
        $this->installModuleTab('Device Management', 'device', '-1');
        $this->installModuleTab('Level 5 Management', 'levellast', '-1');
        $this->installModuleTab('Import', 'import', '-1');
        $this->installModuleTab('Export', 'export', '-1');
        $this->installModuleTab('Leoparts filter Update', 'update', '-1');
        $this->installModuleTab('Leoparts Import Update', 'importEdit', '-1');

        if ($this->_installDataSample() != true) {
            // ONLY FOR INSTALL MODULE, NOT INSTAIL THEME
            $this->installConfig();
        }

        require_once(_PS_MODULE_DIR_ . 'leopartsfilter/install/database.php');

        return $res && $this->registerHook('header') && $this->registerHook('actionProductUpdate') && $this->registerHook('displayAdminProductsExtra') && $this->registerHook('displayLeftColumn') && $this->registerHook('displayTop') && $this->registerHook('displayBackOfficeHeader') && $this->registerHook('actionAdminControllerSetMedia') && $this->registerHook('displayFilter');
    }

    private function installModuleTab($title, $class_sfx = '', $parent = '')
    {
        $class = 'Admin' . Tools::ucfirst($this->name) . Tools::ucfirst($class_sfx);
        @copy(_PS_MODULE_DIR_ . $this->name . '/logo.gif', _PS_IMG_DIR_ . 't/' . $class . '.gif');
        if ($parent == '') {
            # validate module
            $position = Tab::getCurrentTabId();
        } else {
            # validate module
            $position = Tab::getIdFromClassName($parent);
        }

        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = (int) $position;
        $langs = Language::getLanguages(false);
        foreach ($langs as $l) {
            # validate module
            $tab1->name[$l['id_lang']] = $title;
        }
        if ($parent == -1) {
            $tab1->id_parent = -1;
            $tab1->add();
        } else {
            $tab1->add(true, false);
        }
    }

    private function uninstallModuleTab($class_sfx = '')
    {
        $tabClass = 'Admin' . Tools::ucfirst($this->name) . Tools::ucfirst($class_sfx);

        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();
            return true;
        }
        return false;
    }

    private function _installDataSample()
    {
        if (file_exists(_PS_MODULE_DIR_.'leoelements/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'leoelements/libs/LeoDataSample.php');
        }elseif (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php')) {
            require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');
        }else{
            return false;
        }

        $sample = new Datasample(1);
        return $sample->processImport($this->name);
    }

    public function installConfig()
    {

        $config = array();
        $default_config_langs = $this->mod_config->getDefaultValue(true);
        $default_config = $this->mod_config->getDefaultValue(false);

        foreach ($default_config_langs as $key => $value) {
            foreach (Language::getIDs(false) as $id_lang) {
                $config[$key . '_' . (int) $id_lang] = $value;
            }
        }
        foreach ($default_config as $key => $value) {
            $config[$key . '_' . (int) $id_lang] = $value;
        }

        LeopartsfilterConfig::updateConfigValue('cfg_leopartsfilter', $config);

        return true;
    }

    public function uninstall()
    {
        $res = true;
        # Uninstall Module Tab
        $res &= $this->uninstallModuleTab('make');
        $res &= $this->uninstallModuleTab('model');
        $res &= $this->uninstallModuleTab('year');
        $res &= $this->uninstallModuleTab('device');
        $res &= $this->uninstallModuleTab('level5');
        $res &= $this->uninstallModuleTab('import');
        $res &= $this->uninstallModuleTab('export');
        $res &= $this->uninstallModuleTab('management');

        # Delete Tables
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' .
            _DB_PREFIX_ . 'leopartsfilter_make`, `' .
            _DB_PREFIX_ . 'leopartsfilter_make_lang`, `' .
            _DB_PREFIX_ . 'leopartsfilter_make_shop`, `' .
            _DB_PREFIX_ . 'leopartsfilter_model`, `' .
            _DB_PREFIX_ . 'leopartsfilter_model_lang`, `' .
            _DB_PREFIX_ . 'leopartsfilter_model_shop`, `' .
            _DB_PREFIX_ . 'leopartsfilter_year`, `' .
            _DB_PREFIX_ . 'leopartsfilter_year_lang`, `' .
            _DB_PREFIX_ . 'leopartsfilter_year_shop`, `' .
            _DB_PREFIX_ . 'leopartsfilter_device`, `' .
            _DB_PREFIX_ . 'leopartsfilter_device_lang`, `' .
            _DB_PREFIX_ . 'leopartsfilter_device_shop`, `' .
            _DB_PREFIX_ . 'leopartsfilter_level5`, `' .
            _DB_PREFIX_ . 'leopartsfilter_level5_lang`, `' .
            _DB_PREFIX_ . 'leopartsfilter_level5_shop`, `' .
            _DB_PREFIX_ . 'leopartsfilter_import`, `' .
            _DB_PREFIX_ . 'leopartsfilter_product`;
        ');

        $res &= Configuration::deleteByName('cfg_leopartsfilter');
        
        if (!parent::uninstall() || $res) {
            return false;
        }

        return true;
    }

    public function checkModule()
    {
        if (Tools::getValue('controller') == 'AdminLeopartsfilterImport') {
            return true;
        }
        if (Tools::getValue('controller') == 'AdminLeopartsfilterExport') {
            return true;
        }
        if (Tools::getValue('controller') == 'AdminLeopartsfilterMake') {
            return true;
        }
        if (Tools::getValue('controller') == 'AdminLeopartsfilterModel') {
            return true;
        }
        if (Tools::getValue('controller') == 'AdminLeopartsfilterYear') {
            return true;
        }
        if (Tools::getValue('controller') == 'AdminLeopartsfilterDevice') {
            return true;
        }
        if (Tools::getValue('controller') == 'AdminLeopartsfilterLevellast') {
            return true;
        }
        if (Tools::getValue('configure') == 'leopartsfilter') {
            return true;
        }

        return false;
    }


    public function hookDisplayBackOfficeHeader()
    {
        if ($this->checkModule()) {
            $this->context->controller->addCSS(($this->_path) . 'views/css/admin.css', 'all');
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $this->context->controller->addJS(__PS_BASE_URI__ . 'modules/leopartsfilter/views/js/admin/setting.js');
    }

    public function postProcess()
    {
        if (count($this->errors) > 0) {
            return;
        }
        /*
         * @todo
         * tao lai bang database 6 bang
         * @xoa du lieu chi 1 shop
         * xoa tab o product
         *
         *
         * @speed add product, search product
         */
        if (Tools::isSubmit('SubmitCarFilterSettings')) {
            $this->postConfig();
            $this->_html .= $this->displayConfirmation($this->l('Your configurations have been saved successfully.'));
        }
    }
    public function updateModule()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'module` WHERE name = "leopartsfilter"';
        $results = Db::getInstance()->getRow($sql);
        if ($results && count($results)) {
            if ($results['version'] != $this->version) {
                $this->installModuleTab('Export', 'export', '-1');
                $this->installModuleTab('Leoparts filter Update', 'update', '-1');
                $this->installModuleTab('Leoparts Import Update', 'importEdit', '-1');
                $this->installModuleTab('Device Management', 'device', '-1');
                $this->installModuleTab('Levellast Management', 'levellast', '-1');
                $this->installModuleTab('Leoparts filter import data', 'import', '-1');
                $this->installModuleTab('Leoparts filter Update', 'update', '-1');
                $sql = 'UPDATE `'._DB_PREFIX_.'module` SET `version` = "'.$this->version.'" WHERE name = "leopartsfilter"';
                $results = Db::getInstance()->execute($sql);
            }
        }


        $sql = 'SELECT * FROM `'._DB_PREFIX_.'tab` WHERE class_name = "AdminLeopartsfilterExport"';
        $results = Db::getInstance()->ExecuteS($sql);
        if (!$results) {
            $this->installModuleTab('Export', 'export', '-1');
            $this->installModuleTab('Leoparts Import Update', 'importEdit', '-1');
        }
    }
    
    public function getContent()
    {
        $this->updateModule();
        $this->errors = array();
        if (!$this->access('configure')) {
            $this->errors[] = $this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error');
            $this->_html .= $this->displayError($this->trans('You do not have permission to configure this.', array(), 'Admin.Notifications.Error'));
        }
        $this->postProcess();
        // Show Left Menu is active
        Media::addJsDef(array('js_leopartsfilter_controller' => 'module_configuration'));
        return $this->_html . $this->renderForm();
    }

    public function postConfig()
    {
        if (Tools::isSubmit('SubmitCarFilterSettings')) {
            $keys = LeopartsfilterConfig::getConfigKey(false);
            $post = array();
            foreach ($keys as $key) {
                # validate module
                $post[$key] = Tools::getValue($key);
            }
            $multi_lang_keys = LeopartsfilterConfig::getConfigKey(true);
            foreach ($multi_lang_keys as $multi_lang_key) {
                foreach (Language::getIDs(false) as $id_lang) {
                    $post[$multi_lang_key . '_' . (int) $id_lang] = Tools::getValue($multi_lang_key . '_' . (int) $id_lang);
                }
            }
            $sql = 'select * from ' . _DB_PREFIX_ . 'meta where page = "module-leopartsfilter-search"';
            $results = Db::getInstance()->ExecuteS($sql);
            if (!count($results)) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'meta (page,configurable) VALUES ("module-leopartsfilter-search",1)';
                $results = Db::getInstance()->execute($sql);
                $id_meta = Db::getInstance()->Insert_ID();
                $id_shop = (int)Context::getContext()->shop->id;
                foreach (Language::getIDs(false) as $id_lang) {
                    $title = $post['PS_MMY_FILTER_URL_' . (int) $id_lang];
                    $url_rewrite = Tools::strtolower(str_replace(" ", "-", $title));
                    $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'meta_lang (id_meta,id_shop,id_lang,title,url_rewrite) VALUES ('. $id_meta .','.$id_shop.','.$id_lang.',"'.$title.'","'.$url_rewrite.'")';
                    $results = Db::getInstance()->execute($sql);
                }
            } else {
                foreach (Language::getIDs(false) as $id_lang) {
                    $title = $post['PS_MMY_FILTER_URL_' . (int) $id_lang];
                    $url_rewrite = Tools::strtolower(str_replace(" ", "-", $title));
                    $sql = 'UPDATE '._DB_PREFIX_.'meta_lang SET title="'.$title.'", url_rewrite="'.$url_rewrite.'" WHERE id_lang='.$id_lang.' AND id_meta IN (SELECT id_meta FROM '._DB_PREFIX_.'meta WHERE page = "module-leopartsfilter-search")';
                    $results = Db::getInstance()->execute($sql);
                }
            }
            $this->_clearCache('module:leopartsfilter/views/templates/front/filterbox.tpl');
            LeopartsfilterConfig::updateConfigValue('cfg_leopartsfilter', $post);
        }
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'cw_link',
                        'name' => 'ControllerList',
                        'default' => '',
                        'options' => array(
                            array(
                                'title' => $this->l('Level 1 Management'),
                                'target' => '_blank',
                                'link' => 'index.php?controller=AdminLeopartsfilterMake&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterMake'),
                            ),
                            array(
                                'title' => $this->l('Level 2 Management'),
                                'target' => '_blank',
                                'link' => 'index.php?controller=AdminLeopartsfilterModel&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterModel'),
                            ),
                            array(
                                'title' => $this->l('Level 3 Management'),
                                'target' => '_blank',
                                'link' => 'index.php?controller=AdminLeopartsfilterYear&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterYear'),
                            ),
                            array(
                                'title' => $this->l('Level 4 Management'),
                                'target' => '_blank',
                                'link' => 'index.php?controller=AdminLeopartsfilterDevice&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterDevice'),
                            ),
                            array(
                                'title' => $this->l('Level 5 Management'),
                                'target' => '_blank',
                                'link' => 'index.php?controller=AdminLeopartsfilterLevellast&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterLevellast'),
                            ),
                            array(
                                'title' => $this->l('Import data'),
                                'target' => '_blank',
                                'link' => 'index.php?controller=AdminLeopartsfilterImport&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterImport'),
                            ),
                            array(
                                'title' => $this->l('Export data'),
                                'target' => '_blank',
                                'link' => 'index.php?controller=AdminLeopartsfilterExport&token=' . Tools::getAdminTokenLite('AdminLeopartsfilterExport'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Search Page Top Text :'),
                        'name' => 'PS_MMY_TOP_TEXT',
                        'lang' => true,
                        'default' => '',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Filter Box Header Text :'),
                        'name' => 'PS_MMY_BOX_HEADER',
                        'lang' => true,
                        'default' => '',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Leve 1 Dropdown Value :'),
                        'name' => 'PS_MMY_MAKE_DEFAULT_TEXT',
                        'lang' => true,
                        'hint' => $this->l('The default value, show at frontend in drop down box.'),
                        'default' => '',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Leve 2 Dropdown Value :'),
                        'name' => 'PS_MMY_MODEL_DEFAULT_TEXT',
                        'lang' => true,
                        'default' => '',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Leve 3 Dropdown Value :'),
                        'name' => 'PS_MMY_YEAR_DEFAULT_TEXT',
                        'lang' => true,
                        'default' => '',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Leve 4 Dropdown Value :'),
                        'name' => 'PS_MMY_DEVICE_DEFAULT_TEXT',
                        'lang' => true,
                        'default' => '',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Leve 5 Dropdown Value :'),
                        'name' => 'PS_MMY_LEVEL5_DEFAULT_TEXT',
                        'lang' => true,
                        'default' => '',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Filter Button Text :'),
                        'name' => 'PS_MMY_FILTER_BUTTON_TEXT',
                        'lang' => true,
                        'default' => '',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allow Search Button'),
                        'name' => 'PS_ALLOW_SEARCH_FORM_BUTTON',
                        'options' => array('query' => array(
                                array('id' => '0', 'name' => $this->l('No')),
                                array('id' => '1', 'name' => $this->l('Yes')),
                            ),
                            'id' => 'id',
                            'name' => 'name'),
                        'default' => $this->mod_config->getConfig('PS_ALLOW_SEARCH_FORM_BUTTON'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allow Search From'),
                        'name' => 'PS_ALLOW_SEARCH_FORM',
                        'options' => array('query' => array(
                                array('id' => '1', 'name' => $this->l('Level 1')),
                                array('id' => '2', 'name' => $this->l('Level 2')),
                                array('id' => '3', 'name' => $this->l('Level 3')),
                                array('id' => '4', 'name' => $this->l('Level 4')),
                                array('id' => '5', 'name' => $this->l('Level 5'))
                            ),
                            'id' => 'id',
                            'name' => 'name'),
                        'default' => $this->mod_config->getConfig('PS_ALLOW_SEARCH_FORM'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Allow Search Ajax'),
                        'name' => 'PS_ALLOW_SEARCH_AJAX',
                        'options' => array('query' => array(
                                array('id' => '0', 'name' => $this->l('No')),
                                array('id' => '1', 'name' => $this->l('Yes')),
                            ),
                            'id' => 'id',
                            'name' => 'name'),
                        'default' => $this->mod_config->getConfig('PS_ALLOW_SEARCH_AJAX'),
                    ),
                    
                    array(
                        'type' => 'text',
                        'label' => $this->l('Rewritten URL'),
                        'name' => 'PS_MMY_FILTER_URL',
                        'lang' => true,
                        'default' => '',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $fields_form2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Correct module'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'cw_update',
                        'name' => 'leofilterupdate',
                        'default' => Tools::getValue('action'),
                        'link' => 'index.php?controller=AdminModules&configure=leopartsfilter&action=updatemodule&token=' . Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Correct module'),
                    ),
                ),
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = $fields_form;
         $this->fields_form2 = $fields_form2;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'SubmitCarFilterSettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $data = LeopartsfilterConfig::getConfigValue('cfg_leopartsfilter');
        $obj = new stdClass();

        if ($data && $tmp = json_decode(LeopartsfilterConfig::base64Decode($data))) {
            foreach ($tmp as $key => $value) {
                # validate module
                $obj->$key = $value;
            }
        }
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($obj),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        $helper->module = $this;

        $return = $helper->generateForm(array($this->fields_form2));
        $return .= $helper->generateForm(array($this->fields_form));
        
        return $return;
    }

    public function getConfigFieldsValues($obj)
    {
        $languages = Language::getLanguages(false);
        $fields_values = array();
        foreach ($this->fields_form['form']['input'] as $j => $input) {
            // validate module
            unset($j);
            if (isset($input['lang'])) {
                foreach ($languages as $lang) {
                    if (isset($obj->{trim($input['name']) . '_' . $lang['id_lang']})) {
                        $data = $obj->{trim($input['name']) . '_' . $lang['id_lang']};
                        $fields_values[$input['name']][$lang['id_lang']] = $data;
                    } else {
                        # validate module
                        $fields_values[$input['name']][$lang['id_lang']] = $input['default'];
                    }
                }
            } else {
                if (isset($obj->{trim($input['name'])})) {
                    $data = $obj->{trim($input['name'])};
                    $fields_values[$input['name']] = $data;
                } else {
                    # validate module
                    $fields_values[$input['name']] = $input['default'];
                }
            }
        }

        return $fields_values;
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $filterData = $this->getFilterDataByProductId((int) $params['id_product']);
        $make_list = array();
        $results = $this->mod_make->getList($active = 1);
        foreach ($results as $row) {
            $make_list[] = array(
                'mid' => $row['id_leopartsfilter_make'],
                'name' => $row['name']
            );
        }
        $make_ddl_default_text = $this->mod_config->getConfig('PS_MMY_MAKE_DEFAULT_TEXT');
        $model_ddl_default_text = $this->mod_config->getConfig('PS_MMY_MODEL_DEFAULT_TEXT');
        $year_ddl_default_text = $this->mod_config->getConfig('PS_MMY_YEAR_DEFAULT_TEXT');
        $device_ddl_default_text = $this->mod_config->getConfig('PS_MMY_DEVICE_DEFAULT_TEXT');
        $level5_ddl_default_text = $this->mod_config->getConfig('PS_MMY_LEVEL5_DEFAULT_TEXT');

        $base_url_module = _PS_BASE_URL_.__PS_BASE_URI__ . 'modules/leopartsfilter/';
        $base_url = _PS_BASE_URL_.__PS_BASE_URI__;
        if (Tools::usingSecureMode()) {
            $base_url_module = _PS_BASE_URL_SSL_.__PS_BASE_URI__ . 'modules/leopartsfilter/';
            $base_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        }

        $this->context->smarty->assign(array(
            'allow_search_form' => (int) $this->mod_config->getConfig('PS_ALLOW_SEARCH_FORM', false, false),
            'value' => is_array($filterData) ? count($filterData) : 0,
            'base_url_module' => $base_url_module,
            'ajax_url' => Tools::getHttpHost(true).__PS_BASE_URI__ . 'index.php?fc=module&module=leopartsfilter&controller=search',
            'make_list' => $make_list,
            'fdata' => $filterData,
            'make_ddl_default_text' => $make_ddl_default_text,
            'model_ddl_default_text' => $model_ddl_default_text,
            'year_ddl_default_text' => $year_ddl_default_text,
            'device_ddl_default_text' => $device_ddl_default_text,
            'level5_ddl_default_text' => $level5_ddl_default_text,
        ));
        return $this->display(__FILE__, 'views/templates/admin/filterdata.tpl');
    }

    public function alterTable($method)
    {
        switch ($method) {
            case 'add':
                $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'leopartsfilter_product(`id_product` int(11),`make` int(11) collate utf8_bin,`model` int(11),`year` int(11))';
                if (!Db::getInstance()->Execute($sql)) {
                    return false;
                }
                break;
        }
        return true;
    }

    public function getAllMakeData()
    {
        $make_list = array();
        $results = $this->mod_make->getList($active = 1);
        foreach ($results as $row) {
            $make_list[] = array(
                'mid' => $row['id_leopartsfilter_make'],
                'name' => $row['name']
            );
        }
        return $make_list;
    }

    public function hookActionProductUpdate($params)
    {
        if (isset($params['id_product'])) {
            if (Tools::getIsset('filter_column_active')) {
                # FIX : ACTIVE || DEACTIVE AT PRODUCT LIST IN BACKOFFICE
                return;
            }
            $id_product = $params['id_product'];
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'leopartsfilter_product WHERE id_product = "' . (int) $id_product . '"';
            Db::getInstance()->Execute($sql);

            if (Tools::getValue('partsfilter') && Tools::getValue('partsfilter') != '') {
                foreach (Tools::getValue('partsfilter') as $partsdata) {
                    if (isset($partsdata['make']) && $partsdata['make']) {
                        if (isset($partsdata['model']) && $partsdata['model']) {
                            $id_model = $partsdata['model'];
                        } else {
                            $id_model = 0;
                        }
                        if (isset($partsdata['year']) && $partsdata['year']) {
                            $id_year = $partsdata['year'];
                        } else {
                            $id_year = 0;
                        }
                        if (isset($partsdata['device']) && $partsdata['device']) {
                            $id_device = $partsdata['device'];
                        } else {
                            $id_device = 0;
                        }
                        if (isset($partsdata['level5']) && $partsdata['level5']) {
                            $id_level5 = $partsdata['level5'];
                        } else {
                            $id_level5 = 0;
                        }
                        try {
                            Db::getInstance()->insert('leopartsfilter_product', array(
                                'id_product' => (int) ($id_product),
                                'id_leopartsfilter_make' => pSQL($partsdata['make']),
                                'id_leopartsfilter_model' => pSQL($id_model),
                                'id_leopartsfilter_year' => pSQL($id_year),
                                'id_leopartsfilter_device' => pSQL($id_device),
                                'id_leopartsfilter_level5' => pSQL($id_level5)
                            ));
                        } catch (Exception $exc) {
                            if (preg_match('#^Duplicate entry#i', $exc->getMessage())) {
                                // Duplicate not show error
                            } else {
                                throw $exc;
                            }
                        }
                    }
                }
            }
        }
    }

    public function getFilterDataByProductId($id_product)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'leopartsfilter_product` WHERE `id_product` = ' . (int) $id_product;
        if ($results = Db::getInstance()->ExecuteS($sql)) {
            for ($i=0; $i<count($results); $i++) {
                $results[$i]['model'] = $this->mod_model->getList(1, null, null, $results[$i]['id_leopartsfilter_make']);
                $results[$i]['year'] = $this->mod_year->getList(1, null, null, $results[$i]['id_leopartsfilter_make'], $results[$i]['id_leopartsfilter_model']);
                $results[$i]['device'] = $this->mod_device->getList(1, null, null, $results[$i]['id_leopartsfilter_make'], $results[$i]['id_leopartsfilter_model'], $results[$i]['id_leopartsfilter_year']);
                $results[$i]['level5'] = $this->mod_level5->getList(1, null, null, $results[$i]['id_leopartsfilter_make'], $results[$i]['id_leopartsfilter_model'], $results[$i]['id_leopartsfilter_year'], $results[$i]['id_leopartsfilter_device']);
            }
            return $results;
        } else {
            return '';
        }
    }

    public function getModelData($makeid)
    {
        $results = $this->mod_model->getList($active = 1, null, null, $makeid);
    }

    public function getURI()
    {
        $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https://' : 'http://';
        return $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        $this->_clearCache("*");
        if (!$this->isCached('module:leopartsfilter/views/templates/front/filterbox.tpl', $this->getCacheId('leopartsfilter'))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch('module:leopartsfilter/views/templates/front/filterbox.tpl', $this->getCacheId('leopartsfilter'));
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        return $this->getDataFilter();
    }

    protected function _clearCache($template, $cacheId = null, $compileId = null)
    {
        parent::_clearCache($template);
    }

    public function getDataFilter()
    {
        $getURL = $this->getURI();
        $header_text = $this->mod_config->getConfig('PS_MMY_BOX_HEADER');
        $make_ddl_default_text = $this->mod_config->getConfig('PS_MMY_MAKE_DEFAULT_TEXT');
        $model_ddl_default_text = $this->mod_config->getConfig('PS_MMY_MODEL_DEFAULT_TEXT');
        $year_ddl_default_text = $this->mod_config->getConfig('PS_MMY_YEAR_DEFAULT_TEXT');
        $device_ddl_default_text = $this->mod_config->getConfig('PS_MMY_DEVICE_DEFAULT_TEXT');
        $level5_ddl_default_text = $this->mod_config->getConfig('PS_MMY_LEVEL5_DEFAULT_TEXT');
        $filter_button_text = $this->mod_config->getConfig('PS_MMY_FILTER_BUTTON_TEXT');
        $current_id = (int)Context::getContext()->language->id;
        #@TODO : rewrite url

        
        if ($this->mod_config->getConfig('PS_MMY_FILTER_URL_'.$current_id, false, false)) {
            $filter_url = Tools::getHttpHost(true).__PS_BASE_URI__ . Tools::strtolower(str_replace(" ", "-", $this->mod_config->getConfig('PS_MMY_FILTER_URL_'.$current_id, false, false)));
            if ($this->mod_config->getConfig('PS_ALLOW_SEARCH_AJAX', false, false) > 0) {
                $filter_url .= '?action=ajaxsearch';
            } else {
                $filter_url .= '?action=';
            }
        } else {
            $filter_url = 'index.php?fc=module&module=leopartsfilter&controller=search';
            if ($this->mod_config->getConfig('PS_ALLOW_SEARCH_AJAX', false, false) > 0) {
                $filter_url .= '&action=ajaxsearch';
            } else {
                $filter_url .= '&action=';
            }
        }
        
        $make_data = $this->getAllMakeData();
        
        $model_data = '';
        if (Tools::getValue('model')) {
            $model_data = $this->mod_model->getList($active = 1, null, null, Tools::getValue('make'), Tools::getValue('model'));
        }
        $year_data = '';
        if (Tools::getValue('year')) {
            $year_data = $this->mod_year->getList($active = 1, null, null, Tools::getValue('make'), Tools::getValue('model'));
        }
        $device_data = '';
        if (Tools::getValue('device')) {
            $device_data = $this->mod_device->getList($active = 1, null, null, Tools::getValue('make'), Tools::getValue('model'), Tools::getValue('year'));
        }

        $level5_data = '';
        if (Tools::getValue('level5')) {
            $level5_data = $this->mod_level5->getList($active = 1, null, null, Tools::getValue('make'), Tools::getValue('model'), Tools::getValue('year'), Tools::getValue('device'));
        }

        // Change URL , SSL NOT WORKING
        $data_return = (array(
            'mod_config' => $this->mod_config,
            'base_url_module' => $getURL.'modules/leopartsfilter/',
            'header_text' => $header_text,
            'make_ddl_default_text' => $make_ddl_default_text,
            'model_ddl_default_text' => $model_ddl_default_text,
            'year_ddl_default_text' => $year_ddl_default_text,
            'device_ddl_default_text' => $device_ddl_default_text,
            'level5_ddl_default_text' => $level5_ddl_default_text,
            'filter_button_text' => $filter_button_text,
            'make_data' => $make_data,
            'filter_url' => $filter_url,
            'ajax_url' => Tools::getHttpHost(true).__PS_BASE_URI__ . $this->mod_config->getConfig('PS_MMY_FILTER_URL_'.Configuration::get('PS_LANG_DEFAULT'), false, false),
            'allow_search_form' => (int) $this->mod_config->getConfig('PS_ALLOW_SEARCH_FORM', false, false),
            'allow_search_button' => (int) $this->mod_config->getConfig('PS_ALLOW_SEARCH_FORM_BUTTON', false, false),
            'id_make' => Tools::getValue('make'),
            'id_model' => Tools::getValue('model'),
            'id_year' => Tools::getValue('year'),
            'id_device' => Tools::getValue('device'),
            'id_level5' => Tools::getValue('level5'),
            's' => Tools::getValue('s'),
            'model_data' => $model_data,
            'year_data' => $year_data,
            'device_data' => $device_data,
            'level5_data' => $level5_data,
            'action' => Tools::getValue('action'),
            'ajaxsearch' => Context::getContext()->controller->php_self ? (int) $this->mod_config->getConfig('PS_ALLOW_SEARCH_AJAX', false, false) : 0,
        ));
        return $data_return;
    }

    public function hookDisplayFilter(array $params)
    {
        return $this->renderWidget('displayFilter', $params);
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS(($this->_path) . 'views/css/leopartsfilter.css', 'all');
        $this->context->controller->addCSS(($this->_path) . 'views/css/bootstrap-select.css', 'all');
        $this->context->controller->addJS(($this->_path) . 'views/js/bootstrap-select.js');
        $this->context->controller->addJS(($this->_path) . 'views/js/custom.js');
    }

    public function hookDisplayBanner($params)
    {
        return $this->renderWidget('displayBanner', $this->getDataFilter());
    }

    public function hookDisplayNav($params)
    {
        return $this->renderWidget('displayNav', $this->getDataFilter());
    }

    public function hookDisplayTop($params)
    {
        return $this->renderWidget('displayTop', $this->getDataFilter());
    }

    public function hookDisplaySlideshow($params)
    {
        return $this->renderWidget('displaySlideshow', $this->getDataFilter());
    }

    public function hookTopNavigation($params)
    {
        return $this->renderWidget('topNavigation', $this->getDataFilter());
    }

    public function hookDisplayPromoteTop($params)
    {
        return $this->renderWidget('displayPromoteTop', $this->getDataFilter());
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->renderWidget('displayRightColumn', $this->getDataFilter());
    }

    public function hookDisplayLeftColumn($params)
    {
        return $this->renderWidget('displayLeftColumn', $this->getDataFilter());
    }

    public function hookDisplayHome($params)
    {
        return $this->renderWidget('displayHome', $this->getDataFilter());
    }

    public function hookDisplayFooter($params)
    {
        return $this->renderWidget('displayFooter', $this->getDataFilter());
    }

    public function hookProductTabContent($params)
    {
        return $this->renderWidget('productTabContent', $this->getDataFilter());
    }

    public function hookDisplayBottom($params)
    {
        return $this->renderWidget('displayBottom', $this->getDataFilter());
    }

    public function hookDisplayFooterProduct($params)
    {
        return $this->renderWidget('displayFooterProduct', $this->getDataFilter());
    }

    public function hookDisplayTopColumn($params)
    {
        return $this->renderWidget('displayTopColumn', $this->getDataFilter());
    }

    public function hookDisplayRightColumnProduct($params)
    {
        return $this->renderWidget('displayRightColumnProduct', $this->getDataFilter());
    }

    public function hookDisplayLeftColumnProduct($params)
    {
        return $this->renderWidget('displayLeftColumnProduct', $this->getDataFilter());
    }

    public function hookDisplayMaintenance($params)
    {
        return $this->renderWidget('displayMaintenance', $this->getDataFilter());
    }

    public function hookDisplayOrderConfirmation($params)
    {
        return $this->renderWidget('displayOrderConfirmation', $this->getDataFilter());
    }

    public function hookDisplayOrderDetail($params)
    {
        return $this->renderWidget('displayOrderDetail', $this->getDataFilter());
    }

    public function hookDisplayPayment($params)
    {
        return $this->renderWidget('displayPayment', $this->getDataFilter());
    }

    public function hookDisplayPaymentReturn($params)
    {
        return $this->renderWidget('displayPaymentReturn', $this->getDataFilter());
    }

    public function hookDisplayProductComparison($params)
    {
        return $this->renderWidget('displayProductComparison', $this->getDataFilter());
    }

    public function hookDisplayShoppingCartFooter($params)
    {
        return $this->renderWidget('displayShoppingCartFooter', $this->getDataFilter());
    }

    public function hookDisplayContentBottom($params)
    {
        return $this->renderWidget('displayContentBottom', $this->getDataFilter());
    }

    public function hookDisplayFootNav($params)
    {
        return $this->renderWidget('displayFootNav', $this->getDataFilter());
    }

    public function hookDisplayFooterTop($params)
    {
        return $this->renderWidget('displayFooterTop', $this->getDataFilter());
    }

    public function hookDisplayFooterBottom($params)
    {
        return $this->renderWidget('displayFooterBottom', $this->getDataFilter());
    }

    public function hookdisplayHomeTab()
    {
        return $this->display(__FILE__, 'htab.tpl', $this->getCacheId($this->name . '-htab'));
    }

    public function hookdisplayHomeTabContent($params)
    {
        return $this->renderWidget('displayHomeTabContent', $this->getDataFilter());
    }

    public function hookProductFooter($params)
    {
        return $this->renderWidget('productFooter', $this->getDataFilter());
    }

    public function displayFootNav($params)
    {
        return $this->renderWidget('displayFootNav', $this->getDataFilter());
    }
    
    public function isCached($template, $cache_id = null, $compile_id = null)
    {
        if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
            // FIX TEMP
            return false;
        }
        return parent::isCached($template, $cache_id, $compile_id);
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     */
    public function getPermission($variable, $employee = null)
    {
        if ($variable == 'configure') {
            // Allow see form if permission is : configure, view
            $configure = Module::getPermissionStatic($this->id, 'configure', $employee);
            $view = Module::getPermissionStatic($this->id, 'view', $employee);
            return ($configure || $view);
        }
        
        return Module::getPermissionStatic($this->id, $variable, $employee);
    }
    
    /**
     * PERMISSION ACCOUNT demo@demo.com
     */
    public function access($action)
    {
        $employee = null;
        return Module::getPermissionStatic($this->id, $action, $employee);
    }
}
