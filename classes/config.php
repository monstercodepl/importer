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

class LeopartsfilterConfig
{

    public $params;

    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            # validate module
            $instance = new LeopartsfilterConfig();
        }
        return $instance;
    }

    public function __construct()
    {
        $data = self::getConfigValue('cfg_leopartsfilter');

        if ($data && $tmp = json_decode(LeopartsfilterConfig::base64Decode($data))) {
            # validate module 12
            $arr_data = array();
            foreach ($tmp as $key => $value) {
                $arr_data[$key] = $value;
            }
            $this->params = $arr_data;
        }
    }

    public function mergeParams($params)
    {
        # validate module
        unset($params);
    }

    public function setVar($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function get($name, $value = '')
    {
        if (isset($this->params[$name])) {
            # validate module
            return $this->params[$name];
        }
        return $value;
    }

    public static function getConfigName($name)
    {
        return Tools::strtoupper($name);
    }

    public static function updateConfigValue($name, $value = '')
    {
        $value = LeopartsfilterConfig::base64Encode(json_encode($value));
        ConfigurationCore::updateValue(self::getConfigName($name), $value, true);
    }

    public static function getConfigValue($name)
    {
        return Configuration::get(self::getConfigName($name));
    }

    public static function getConfigKey($multi_lang = false)
    {
        if ($multi_lang == true) {
            return array(
                'PS_MMY_TOP_TEXT',
                'PS_MMY_BOX_HEADER',
                'PS_MMY_MAKE_DEFAULT_TEXT',
                'PS_MMY_MODEL_DEFAULT_TEXT',
                'PS_MMY_YEAR_DEFAULT_TEXT',
                'PS_MMY_DEVICE_DEFAULT_TEXT',
                'PS_MMY_LEVEL5_DEFAULT_TEXT',
                'PS_MMY_MAKE_TEXT',
                'PS_MMY_MODEL_TEXT',
                'PS_MMY_YEAR_TEXT',
                'PS_MMY_MAKE_REQUIRED_TEXT',
                'PS_MMY_MODEL_REQUIRED_TEXT',
                'PS_MMY_YEAR_REQUIRED_TEXT',
                'PS_MMY_FILTER_BUTTON_TEXT',
                'PS_ALLOW_SEARCH_FORM_BUTTON',
                'PS_MMY_FILTER_URL',
            );
        } else {
            return array(
                'PS_ALLOW_SEARCH_FORM',
                'PS_ALLOW_SEARCH_FORM_BUTTON',
                'PS_ALLOW_SEARCH_AJAX',
            );
        }
    }

    public static function getDefaultValue($multi_lang = false)
    {
        if ($multi_lang == true) {
            return array(
                'PS_MMY_TOP_TEXT' => 'Make Model Year Filter Result',
                'PS_MMY_BOX_HEADER' => 'Find Your Parts',
                'PS_MMY_MAKE_DEFAULT_TEXT' => 'Select Make',
                'PS_MMY_MODEL_DEFAULT_TEXT' => 'Select Model',
                'PS_MMY_YEAR_DEFAULT_TEXT' => 'Select Year',
                'PS_MMY_DEVICE_DEFAULT_TEXT' => 'Select Device',
                'PS_MMY_LEVEL5_DEFAULT_TEXT' => 'Select level 5',
                'PS_MMY_MAKE_TEXT' => 'Make',
                'PS_MMY_MODEL_TEXT' => 'Model',
                'PS_MMY_YEAR_TEXT' => 'Year',
                'PS_MMY_DEVICE_TEXT' => 'Device',
                'PS_MMY_MAKE_REQUIRED_TEXT' => 'Make Reguired',
                'PS_MMY_MODEL_REQUIRED_TEXT' => 'Model Reguired',
                'PS_MMY_YEAR_REQUIRED_TEXT' => 'Year Reguired',
                'PS_MMY_DEVICE_REQUIRED_TEXT' => 'Device Reguired',
                'PS_MMY_FILTER_BUTTON_TEXT' => 'Filter',
                'PS_MMY_FILTER_URL' => 'product-filter',
            );
        } else {
            return array(
                'PS_ALLOW_SEARCH_FORM' => 4,
                'PS_ALLOW_SEARCH_FORM_BUTTON' => 1,
                'PS_ALLOW_SEARCH_AJAX' => 1,
            );
        }
    }

    /**
     * id_lang is false, id_lang will not use
     */
    public function getConfig($name, $value = '', $id_lang = null)
    {
        if ($id_lang === null) {
            $id_lang = '_' . Context::getContext()->language->id;
        } elseif ($id_lang === false) {
            $id_lang = '';
        } elseif (isset($id_lang) && $id_lang) {
            $id_lang = '_' . $id_lang;
        }

        if (isset($this->params[$name . $id_lang])) {
            # Get from database
            return $this->params[$name . $id_lang];
        } elseif (isset($this->params[$name])) {
            # Get from controller
            return $this->params[$name];
        } else {
            # Get from default
            $default_value = array_merge($this->getDefaultValue(true), $this->getDefaultValue(false));
            if (!empty($default_value[$name])) {
                return $default_value[$name];
            }
        }

        # default this function
        return '';
    }

    public static function base64Decode($data)
    {
        return call_user_func('base64_decode', $data);
    }

    public static function base64Encode($data)
    {
        return call_user_func('base64_encode', $data);
    }
}
