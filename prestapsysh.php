<?php

/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class Prestapsysh extends Module
{
    public function __construct()
    {
        $this->name = 'prestapsysh';
        $this->tab = 'Administration';
        $this->version = '1.1.0';
        $this->author = 'Prestashop';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Prestashop Psysh', [], 'Modules.Prestapsysh.Admin');
        $this->description = $this->trans('Prestashop Psysh', [], 'Modules.Prestapsysh.Admin');
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
    }

    public function install(): bool
    {
        return parent::install();
    }

    public function uninstall(): bool
    {
        return parent::uninstall();
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    public function getContent(): string
    {
        return $this->postProcess() .$this->_displayCheck(). $this->renderForm();
    }


    private function _displayCheck(): string
    {
        return $this->display(__FILE__, './views/templates/hook/infos.tpl');
    }

    public function postProcess(): string
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            Configuration::updateValue('PSY_CC_ACTIVE', (bool) Tools::getValue('PSY_CC_ACTIVE'));
            $output = $this->displayConfirmation($this->trans('Settings have been saved', [], 'Modules.Prestapsysh.Admin'));
        }
        return $output;
    }

    public function renderForm(): string
    {
        $switchEnable = [
            [
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->trans('Enabled', [], 'Modules.Prestapsysh.Admin'),
            ],
            [
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->trans('Disabled', [], 'Modules.Prestapsysh.Admin'),
            ],
        ];

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('General Settings', [], 'Modules.Prestapsysh.Admin'),
                    'icon' => 'icon-folder-close',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->trans('Activate shell'),
                        'name' => 'PSY_CC_ACTIVE',
                        'desc' => $this->trans('Activate or deactivate the shell command', [], 'Modules.Prestapsysh.Admin'),
                        'required' => true,
                        'values' => $switchEnable,
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save'),
                ],
            ],
        ];

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues(): array
    {
        return [
            'PSY_CC_ACTIVE' => (bool) Configuration::get('PSY_CC_ACTIVE'),
        ];
    }
}
