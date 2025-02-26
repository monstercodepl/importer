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

require_once _PS_MODULE_DIR_ . 'leopartsfilter/classes/LeopartsfilterImportEdit.php';

class AdminLeopartsfilterImportEditController extends ModuleAdminController
{
    public function __construct()
    {
        if (!Tools::getValue('id')) {
            Tools::redirect(Context::getContext()->link->getAdminLink('AdminLeopartsfilterImport'));
        }

        $this->context = Context::getContext();
        $this->table = 'leopartsfilter_import';
        $this->identifier = 'id';
        $this->className = 'LeopartsfilterImportEdit';
        $this->lang = false;
        $this->bootstrap = true;
        $this->addRowAction('add');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        parent::__construct();
        $this->fields_list = array(
            'id' => array('title' => $this->l('ID'), 'width' => 100),
            'id_product' => array('title' => $this->l('Id Product'), 'width' => 200),
            'product_name' => array('title' => $this->l('Product Name'), 'width' => 200),
            'level1' => array('title' => $this->l('Level 1')),
            'level2' => array('title' => $this->l('Level 2')),
            'level3' => array('title' => $this->l('Level 3')),
            'level4' => array('title' => $this->l('Level 4')),
            'level5' => array('title' => $this->l('Level 5')),
            'status' => array('title' => $this->l('Status')),
        );
    }

    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        $tabs = array();
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
            'quick_access_current_link_name' => $breadcrumbs2['tab']['name'].
            (isset($breadcrumbs2['action']) ? ' - '.$breadcrumbs2['action']['name'] : ''),
            'quick_access_current_link_icon' => $breadcrumbs2['container']['icon']
        ));

        $this->breadcrumbs[] = 'Apcarrental Services';
        $navigation_pipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
        $this->context->smarty->assign('navigationPipe', $navigation_pipe);
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Pic Custom Product Fonts'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('ID Product'),
                    'name' => 'id_product',
                    'required' => false,
                    'lang' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product Name'),
                    'name' => 'product_name',
                    'required' => false,
                    'lang' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Level 1'),
                    'name' => 'level1',
                    'required' => false,
                    'lang' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Level 2'),
                    'name' => 'level2',
                    'required' => false,
                    'lang' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Level 3'),
                    'name' => 'level3',
                    'required' => false,
                    'lang' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Level 4'),
                    'name' => 'level4',
                    'required' => false,
                    'lang' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Level 5'),
                    'name' => 'level5',
                    'required' => false,
                    'lang' => false
                ),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and Stay'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            )
        );

        return parent::renderForm();
    }

    public function setAcceptTypes($file_type)
    {
        $img_mimes = array('image/gif','image/jpeg','image/png');
        return (in_array($file_type, $img_mimes, TRUE)) ? TRUE : FALSE;
    }
        
    public function processAdd()
    {
        return;
    }

    public function processUpdate()
    {
        if (Tools::getValue('submitAddleopartsfilter_import') && Tools::getValue('submitAddleopartsfilter_import') != '' && Tools::getValue('id')) {
            $id = (int)Tools::getValue('id');
            $id_product = (int)Tools::getValue('id_product');
            $product_name = Tools::getValue('product_name');
            $level1 = Tools::getValue('level1');
            $level2 = Tools::getValue('level2');
            $level3 = Tools::getValue('level3');
            $level4 = Tools::getValue('level4');
            $level5 = Tools::getValue('level5');

            $sql = 'UPDATE `'._DB_PREFIX_.'leopartsfilter_import`
            SET id_product='.$id_product.', product_name="'.pSQL($product_name).'", level1="'.pSQL($level1).'", level2="'.pSQL($level2).'", level3="'.pSQL($level3).'", level4="'.pSQL($level4).'", level5="'.pSQL($level5).'", status=0 WHERE id =' . $id;
            Db::getInstance()->execute($sql);

            Tools::redirect(Context::getContext()->link->getAdminLink('AdminLeopartsfilterImport'));
        }
        return false;
    }

    // protected function _childValidation()
    // {
    //     $model = new PiccustomproductFonts();
    //     $field = 'name';
    //     $languages = Language::getLanguages(false);
    //     foreach ($languages as $language) {
    //         $value = Tools::getValue($field.'_'.$language['id_lang']);
    //         $id = Tools::getValue('id_piccustomproduct_fonts');
    //         if (!empty($value)) {
    //             $exist = $model->existName($value, $id, $language['id_lang'], Context::getContext()->shop->id);
    //             if ($exist) {
    //                 $this->errors[$field.'_'.$language['id_lang']] = sprintf(
    //                     $this->l('The field %1$s "%2$s" in %3$s is exist.'),
    //                     $field,
    //                     $value,
    //                     $language['name']
    //                 );
    //             }
    //         }
    //     }
    // }
}
