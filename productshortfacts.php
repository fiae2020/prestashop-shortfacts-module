<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author Dzemal Imamovic <dzemal.imamovic@outlook.com>
 * @copyright Since 2025 Dzemal Imamovic
 * @license MIT
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductShortFacts extends Module
{
    public function __construct()
    {
        $this->name = 'productshortfacts';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'Dzemal Imamovic';
        $this->need_instance = 0;
        $this->bootstrap = true;

        $this->ps_versions_compliancy = [
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        ];

        parent::__construct();

        $this->displayName = $this->l('Product Short Facts');
        $this->description = $this->l('Add customizable short facts with checkmarks to your products');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        if (!Configuration::get('PRODUCTSHORTFACTS_DISPLAY_HOOK')) {
            $this->warning = $this->l('Module needs to be configured');
        }
    }

    public function install()
    {
        try {
            // Register only essential hooks during installation
            if (!parent::install()
                || !$this->installSQL()
                || !$this->registerHook('displayAdminProductsExtra')
                || !$this->registerHook('actionProductSave')
                || !$this->registerHook('displayBackOfficeHeader')
                || !Configuration::updateValue('PRODUCTSHORTFACTS_DISPLAY_HOOK', 'displayProductAdditionalInfo')
                || !Configuration::updateValue('PRODUCTSHORTFACTS_SHOW_CHECKMARKS', 1)
            ) {
                throw new Exception('Error during module installation');
            }

            // Register the default display hook
            if (!$this->registerHook('displayProductAdditionalInfo')) {
                throw new Exception('Failed to register default display hook');
            }

            return true;
        } catch (Exception $e) {
            PrestaShopLogger::addLog('ProductShortFacts module installation error: ' . $e->getMessage(), 3);
            return false;
        }
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallSQL()
            && Configuration::deleteByName('PRODUCTSHORTFACTS_DISPLAY_HOOK')
            && Configuration::deleteByName('PRODUCTSHORTFACTS_SHOW_CHECKMARKS');
    }

    private function installSQL()
    {
        try {
            $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'product_shortfacts` (
                `id_shortfact` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `shortfact_text` TEXT NOT NULL,
                `position` int(11) DEFAULT 1,
                `active` tinyint(1) DEFAULT 1,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_shortfact`),
                KEY `id_product` (`id_product`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';

            if (!Db::getInstance()->execute($sql)) {
                throw new Exception('Error creating product_shortfacts table: ' . Db::getInstance()->getMsgError());
            }

            return true;
        } catch (Exception $e) {
            PrestaShopLogger::addLog('ProductShortFacts SQL installation error: ' . $e->getMessage(), 3);
            return false;
        }
    }

    private function uninstallSQL()
    {
        try {
            $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'product_shortfacts`';
            if (!Db::getInstance()->execute($sql)) {
                throw new Exception('Error dropping product_shortfacts table: ' . Db::getInstance()->getMsgError());
            }

            return true;
        } catch (Exception $e) {
            PrestaShopLogger::addLog('ProductShortFacts SQL uninstallation error: ' . $e->getMessage(), 3);
            return false;
        }
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $displayHook = (string) Tools::getValue('PRODUCTSHORTFACTS_DISPLAY_HOOK');
            $showCheckmarks = (bool) Tools::getValue('PRODUCTSHORTFACTS_SHOW_CHECKMARKS');

            if (!$displayHook) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                // Unregister old hook if it's different
                $oldHook = Configuration::get('PRODUCTSHORTFACTS_DISPLAY_HOOK');
                if ($oldHook && $oldHook != $displayHook) {
                    $this->unregisterHook($oldHook);
                }

                // Register new hook
                if (!$this->registerHook($displayHook)) {
                    $output .= $this->displayError($this->l('Failed to register the selected hook'));
                } else {
                    Configuration::updateValue('PRODUCTSHORTFACTS_DISPLAY_HOOK', $displayHook);
                    Configuration::updateValue('PRODUCTSHORTFACTS_SHOW_CHECKMARKS', $showCheckmarks);
                    $output .= $this->displayConfirmation($this->l('Settings updated'));
                }
            }
        }

        if (Tools::isSubmit('submitBulkImport')) {
            $output .= $this->processBulkImport();
        }

        // Add the delete all functionality
        if (Tools::isSubmit('submitDeleteAll')) {
            // Validate security token
            if (!Tools::isSubmit('token') || Tools::getValue('token') !== Tools::getAdminTokenLite('AdminModules')) {
                $output .= $this->displayError($this->l('Invalid security token'));
            } else {
                try {
                    // Delete all short facts from the database
                    $result = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'product_shortfacts`');

                    if ($result) {
                        // Get count of deleted records (optional)
                        $deletedCount = Db::getInstance()->Affected_Rows();
                        $output .= $this->displayConfirmation(
                            sprintf($this->l('All short facts have been deleted successfully. (%d records removed)'), $deletedCount)
                        );
                    } else {
                        $output .= $this->displayError($this->l('Error occurred while deleting short facts: ') . Db::getInstance()->getMsgError());
                    }
                } catch (Exception $e) {
                    PrestaShopLogger::addLog('ProductShortFacts delete all error: ' . $e->getMessage(), 3);
                    $output .= $this->displayError($this->l('An error occurred during deletion. Please check the logs.'));
                }
            }
        }

        return $output . $this->displayForm() . $this->displayBulkImportForm();
    }

    public function displayForm()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->l('Display Hook'),
                    'name' => 'PRODUCTSHORTFACTS_DISPLAY_HOOK',
                    'required' => true,
                    'options' => [
                        'query' => [
                            ['id' => 'displayProductAdditionalInfo', 'name' => $this->l('Product Additional Info (Recommended)')],
                            // ['id' => 'displayRightColumnProduct', 'name' => $this->l('Right Column Product')],
                            // ['id' => 'displayReassurance', 'name' => $this->l('Reassurance Area')],
                            // ['id' => 'displayProductTabContent', 'name' => $this->l('Product Tab Content')],
                            // ['id' => 'displayFooterProduct', 'name' => $this->l('Product Footer')],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'desc' => $this->l('Select where to display the short facts'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Show Checkmarks'),
                    'name' => 'PRODUCTSHORTFACTS_SHOW_CHECKMARKS',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        $fieldsForm[1]['form'] = [
            'legend' => [
                'title' => '<i class="icon-user"></i> ' . $this->l('Author Information'),
            ],
            'input' => [],
            'buttons' => [
                [
                    'title' => $this->l('DONATE VIA PAYPAL'),
                    'icon' => 'process-icon-globe',
                    'href' => 'https://www.paypal.com/paypalme/jimmyweb',
                    'target' => '_blank',
                    'class' => 'btn btn-primary pull-right',
                ],
                [
                    'title' => $this->l('DONATE VIA BUYMEACOFFE'),
                    'icon' => 'process-icon-globe',
                    'href' => 'https://buymeacoffee.com/jimmyweb',
                    'target' => '_blank',
                    'class' => 'btn btn-primary pull-right',
                ],
                [
                    'title' => $this->l('Contact on Fiverr'),
                    'icon' => 'process-icon-globe',
                    'href' => 'https://www.fiverr.com/s/qDvrV1g',
                    'target' => '_blank',
                    'class' => 'btn btn-primary pull-right',
                ],
                [
                    'title' => $this->l('Send Email'),
                    'icon' => 'process-icon-envelope',
                    'href' => 'mailto:dzemal.imamovic@outlook.com',
                    'class' => 'btn btn-success pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list'),
            ],
        ];

        $helper->fields_value['PRODUCTSHORTFACTS_DISPLAY_HOOK'] = Configuration::get('PRODUCTSHORTFACTS_DISPLAY_HOOK');
        $helper->fields_value['PRODUCTSHORTFACTS_SHOW_CHECKMARKS'] = Configuration::get('PRODUCTSHORTFACTS_SHOW_CHECKMARKS');

        // Assign data for author information template
        $this->context->smarty->assign([
            'module_author_text' => $this->l('Module created by Dzemal Imamovic - Certified Software Developer with 10+ years of E-Commerce experience'),
            'module_support_text' => $this->l('Looking for development assistance? Need help with custom modules, server customization or speed optimization? Let me know!'),
        ]);

        $fieldsForm[1]['form']['description'] = $this->display(__FILE__, 'views/templates/admin/author_info.tpl');

        return $helper->generateForm($fieldsForm);
    }

    public function displayBulkImportForm()
    {
        $this->context->smarty->assign([
            'module_name' => $this->name,
            'admin_token' => Tools::getAdminTokenLite('AdminModules'),
            'current_index' => AdminController::$currentIndex,
            'bulk_import_title' => $this->l('Bulk Import Short Facts'),
            'delete_all_title' => $this->l('Delete All Short Facts'),
            'import_label' => $this->l('Import Data'),
            'import_placeholder' => $this->l('Format: product_id|short_fact_text (one per line)'),
            'import_example' => $this->l('Example:'),
            'import_button' => $this->l('Import'),
            'delete_confirm' => addslashes($this->l('Are you sure you want to delete all short facts? This action cannot be undone.')),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/bulk_import_form.tpl');
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->smarty->assign([
                'delete_confirm_message' => addslashes($this->l('Are you sure you want to delete all short facts? This action cannot be undone.')),
            ]);

            return $this->display(__FILE__, 'views/templates/admin/backoffice_header.tpl');
        }
    }

    private function processBulkImport()
    {
        if (!Tools::isSubmit('token') || Tools::getValue('token') !== Tools::getAdminTokenLite('AdminModules')) {
            return $this->displayError($this->l('Invalid security token'));
        }

        $importData = Tools::getValue('bulk_import_data');
        if (empty($importData)) {
            return $this->displayError($this->l('No import data provided'));
        }

        $lines = explode("\n", $importData);
        $imported = 0;
        $errors = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $parts = explode('|', $line, 2);
            if (count($parts) != 2) {
                ++$errors;
                continue;
            }

            $productId = (int) trim($parts[0]);
            $shortFact = trim($parts[1]);

            if (!Validate::isUnsignedId($productId) || empty($shortFact) || !Validate::isGenericName($shortFact)) {
                ++$errors;
                continue;
            }

            $data = [
                'id_product' => $productId,
                'shortfact_text' => pSQL($shortFact),
                'position' => 1,
                'active' => 1,
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s'),
            ];

            if (Db::getInstance()->insert('product_shortfacts', $data)) {
                ++$imported;
            } else {
                ++$errors;
            }
        }

        $message = sprintf($this->l('Import completed: %d imported, %d errors'), $imported, $errors);

        return $errors > 0 ? $this->displayWarning($message) : $this->displayConfirmation($message);
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $productId = (int) Tools::getValue('id_product');
        if (!$productId || !Validate::isUnsignedId($productId)) {
            return '';
        }

        $shortFacts = $this->getProductShortFacts($productId);

        if (!empty($shortFacts)) {
            foreach ($shortFacts as &$fact) {
                $fact['shortfact_text'] = Tools::safeOutput($fact['shortfact_text']);
            }
        }

        $this->context->smarty->assign([
            'short_facts' => $shortFacts,
            'product_id' => $productId,
            'module_dir' => $this->_path,
            'token' => Tools::getAdminTokenLite('AdminProducts'),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/admin_product_form.tpl');
    }

    public function hookActionProductSave($params)
    {
        $productId = (int) $params['id_product'];
        $shortFacts = Tools::getValue('short_facts', []);

        Db::getInstance()->delete('product_shortfacts', 'id_product = ' . (int) $productId);

        if (!empty($shortFacts)) {
            foreach ($shortFacts as $position => $shortFact) {
                if (!empty(trim($shortFact))) {
                    $data = [
                        'id_product' => (int) $productId,
                        'shortfact_text' => pSQL($shortFact),
                        'position' => (int) ($position + 1),
                        'active' => 1,
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_upd' => date('Y-m-d H:i:s'),
                    ];
                    Db::getInstance()->insert('product_shortfacts', $data);
                }
            }
        }
    }

    // Hook methods for display
    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->renderShortFacts();
    }

    // NEW (commented out):
    /*
    // Commented out hook methods - only displayProductAdditionalInfo is active


        public function hookDisplayRightColumnProduct($params)
        {
            return $this->renderShortFacts();
        }

        public function hookDisplayReassurance($params)
        {
            return $this->renderShortFacts();
        }

        public function hookDisplayProductTabContent($params)
        {
            return $this->renderShortFacts();
        }

        public function hookDisplayFooterProduct($params)
        {
            return $this->renderShortFacts();
        }

        */

    private function renderShortFacts()
    {
        $productId = (int) Tools::getValue('id_product');
        if (!$productId && isset($this->context->controller->php_self) && $this->context->controller->php_self == 'product') {
            $productId = (int) Tools::getValue('id_product');
        }

        if (!$productId) {
            return '';
        }

        $cacheId = 'productshortfacts_' . $productId;
        if (!$this->isCached('product_short_facts.tpl', $this->getCacheId($cacheId))) {
            $shortFacts = $this->getProductShortFacts($productId);

            if (empty($shortFacts)) {
                return '';
            }

            foreach ($shortFacts as &$fact) {
                $fact['shortfact_text'] = Tools::safeOutput($fact['shortfact_text']);
            }

            $this->context->smarty->assign([
                'short_facts' => $shortFacts,
                'show_checkmarks' => (bool) Configuration::get('PRODUCTSHORTFACTS_SHOW_CHECKMARKS'),
                'module_dir' => $this->_path,
            ]);
        }

        return $this->display(__FILE__, 'views/templates/hook/product_short_facts.tpl', $this->getCacheId($cacheId));
    }

    private function getProductShortFacts($productId)
    {
        $productId = (int) $productId;
        if (!Validate::isUnsignedId($productId)) {
            return [];
        }

        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'product_shortfacts` 
            WHERE `id_product` = ' . (int) $productId . ' 
            AND `active` = 1 
            ORDER BY `position` ASC'
        );
    }
}
