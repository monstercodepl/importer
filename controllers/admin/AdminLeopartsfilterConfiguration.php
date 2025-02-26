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
    # module validation
    exit;
}

class AdminLeopartsfilterConfigurationController extends ModuleAdminControllerCore
{

    public function __construct()
    {
        parent::__construct();
        $url = 'index.php?controller=AdminModules&configure=leopartsfilter&token=' . Tools::getAdminTokenLite('AdminModules');
        Tools::redirectAdmin($url);
    }
}
