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

require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/config.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/make.php');
require_once(_PS_MODULE_DIR_ . 'leopartsfilter/classes/model.php');

class AdminLeopartsfilterModelController extends ModuleAdminControllerCore
{
    public $mod_config;
    public function __construct()
    {
        parent::__construct();
        $this->mod_config = LeopartsfilterConfig::getInstance();
        $this->bootstrap = true;
        $this->table = 'leopartsfilter_model';
        $this->identifier = 'id_leopartsfilter_model';
        $this->className = 'LeopartsfilterModel';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));
        $this->fields_list = array(
            'id_leopartsfilter_model' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'),
            'name' => array(
                'title' => $this->l('Level 2'),
                'filter_key' => 'b!name'),
            'make_name' => array(
                'title' => $this->l('Level 1'),
                'filter_key' => 'cl!name'),
            'date_add' => array(
                'title' => $this->l('Date Create'),
                'filter_key' => 'a!date_add',
                'width' => 150,
                'class' => 'fixed-width-xs'),
            'position' => array(
                'title' => $this->l('Position'),
                'width' => 100,
                'class' => 'fixed-width-xs',
                'filter_key' => 'a!position'),
            'active' => array(
                'title' => $this->l('Displayed'),
                'align' => 'center',
                'active' => 'status',
                'class' => 'fixed-width-xs',
                'type' => 'bool',
                'orderby' => false)
        );
        $this->_select .= ' cl.name make_name';
        $this->_join .= 'LEFT JOIN ' . _DB_PREFIX_ . 'leopartsfilter_make_lang cl ON cl.id_leopartsfilter_make=a.id_leopartsfilter_make AND cl.id_lang=b.id_lang';
        $this->_group = ' GROUP BY (a.id_leopartsfilter_model) ';
        $this->_orderBy = 'a.position';
        // Show Left Menu is active
        Media::addJsDef(array('js_leopartsfilter_controller' => 'module_configuration'));
    }

    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        if (is_array($tabs)) {
            $tabs = array();
        }

        if (is_null($tab_id)) {
            $tab_id = $this->id;
        }

        $tabs = Tab::recursiveTab($tab_id, $tabs);

        $dummy = array('name' => '', 'href' => '', 'icon' => '');
        $breadcrumbs2 = array(
            'container' => $dummy,
            'tab' => $dummy,
            'action' => $dummy
        );

        if (isset($tabs[0]) && count($tabs[0])) {
            $this->addMetaTitle($tabs[0]['name']);
            $breadcrumbs2['tab']['name'] = 'Level 2 Management';
            $breadcrumbs2['tab']['href'] = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/' . $this->context->link->getAdminLink($tabs[0]['class_name']);
            if (!isset($tabs[1])) {
                $breadcrumbs2['tab']['icon'] = 'icon-' . $tabs[0]['class_name'];
            }
        }
        // if (isset($tabs[1]) && count($tabs[1])) {
        //     $breadcrumbs2['container']['name'] = 'Level Management';
        //     $breadcrumbs2['container']['href'] = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/' . $this->context->link->getAdminLink($tabs[1]['class_name']);
        //     $breadcrumbs2['container']['icon'] = 'icon-' . $tabs[1]['class_name'];
        // }

        /* content, edit, list, add, details, options, view */
        switch ($this->display) {
            case 'add':
                $breadcrumbs2['action']['name'] = $this->l('Add', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-plus';
                break;
            case 'edit':
                $breadcrumbs2['action']['name'] = $this->l('Edit', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-pencil';
                break;
            case '':
            case 'list':
                $breadcrumbs2['action']['name'] = $this->l('List', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-th-list';
                break;
            case 'details':
            case 'view':
                $breadcrumbs2['action']['name'] = $this->l('View details', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-zoom-in';
                break;
            case 'options':
                $breadcrumbs2['action']['name'] = $this->l('Options', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-cogs';
                break;
            case 'generator':
                $breadcrumbs2['action']['name'] = $this->l('Generator', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-flask';
                break;
        }

        $this->context->smarty->assign(array(
            'breadcrumbs2' => $breadcrumbs2,
            'quick_access_current_link_name' => $breadcrumbs2['tab']['name'] . (isset($breadcrumbs2['action']) ? ' - ' . $breadcrumbs2['action']['name'] : ''),
            'quick_access_current_link_icon' => $breadcrumbs2['container']['icon']
        ));

        /* BEGIN - Backward compatibility < 1.6.0.3 */
        $this->breadcrumbs[] = 'Level 2 Management';
        $navigation_pipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
        $this->context->smarty->assign('navigationPipe', $navigation_pipe);
        /* END - Backward compatibility < 1.6.0.3 */
    }

    public function renderList()
    {
        $this->toolbar_title = $this->l('Level 2 Management');
        return parent::renderList();
    }

    /**
     * Add/Edit page
     */
    public function renderForm()
    {
        if ($this->display == 'add' || $this->display == 'edit') {
            $this->context->controller->addJS(__PS_BASE_URI__ . 'modules/leopartsfilter/views/js/admin/home.js');
        }

        $this->multiple_fieldsets = true;

        $mod_make = new LeopartsfilterMake(0);
        $list_make = $mod_make->getDropdown(0);

        $form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Level 2 Management'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name :'),
                    'name' => 'name',
                    'id' => 'name',
                    'lang' => true,
                    'required' => true,
                    'class' => 'leofirstfocus',
//                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Level 1 :'),
                    'name' => 'id_leopartsfilter_make',
                    'required' => true,
                    'options' => array('query' => $list_make,
                        'id' => 'id_leopartsfilter_make',
                        'name' => 'name'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Position :'),
                    'name' => 'position',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Displayed :'),
                    'name' => 'active',
                    'is_bool' => true,
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'display',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'display',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'default_value' => 1,
                ),
                array(
                    'type' => 'hidden',
                    'label' => $this->l('baseurl :'),
                    'name' => 'baseurl',
                    'default_value' => Tools::getHttpHost(true).__PS_BASE_URI__ . $this->mod_config->getConfig('PS_MMY_FILTER_URL_'.Configuration::get('PS_LANG_DEFAULT'), false, false),
                ),
            ),
        );
        if ($this->display == 'add') {
            $form['buttons']['save_and_add'] = array(
                'name' => 'save_and_add',
                'type' => 'submit',
                'title' => $this->l('Save and Add'),
                'class' => 'btn btn-default pull-right',
                'icon' => 'process-icon-save'
            );
        }
        $form['buttons']['save'] = array(
            'name' => 'submitAddleopartsfilter_model',
            'type' => 'submit',
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right',
            'icon' => 'process-icon-save'
        );
        $this->fields_form[0]['form'] = $form;
        return parent::renderForm();
    }

    public function processAdd()
    {
        parent::validateRules();

        if (count($this->errors) <= 0) {
            $mod_model = new LeopartsfilterModel();
            $this->copyFromPost($mod_model, 'model');

//            $mod_model->date_add = date('Y-m-d', time());

            if (!$mod_model->add()) {
                $this->errors[] = $this->l('An error occurred while creating an object.')
                        . ' <b>' . $this->table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
            }

            if (Tools::getValue('save_and_add') === '' || ToolsCore::getValue('save_and_add')) {
                $this->redirect_after = self::$currentIndex . '&conf=3&add' . $this->table . '&token=' . $this->token;
            }
        }

        $this->errors = array_unique($this->errors);
        if (!empty($this->errors)) {
            // if we have errors, we stay on the form instead of going back to the list
            $this->display = 'edit';
            return false;
        }

        $this->display = 'list';
        if (empty($this->errors)) {
            $this->confirmations[] = $this->_conf[3];
        }
        return $this->object;
    }

    /**
     * parent::validateRules();
     */
    protected function _childValidation()
    {
        $mod_model = new LeopartsfilterModel();
        $field = 'name';

        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $value = Tools::getValue($field . '_' . $language['id_lang']);
            $id_make = Tools::getValue('id_leopartsfilter_make');
            $id_model = Tools::getValue('id_leopartsfilter_model');
            if (!empty($value)) {
                $exist = $mod_model->existName($value, $id_make, $id_model, $language['id_lang']);
                if ($exist) {
                    $this->errors[$field . '_' . $language['id_lang']] = sprintf($this->l('The field %1$s : %2$s in (%3$s) is exist.'), $field, $value, $language['name']);
                }
            }
        }
    }
}
