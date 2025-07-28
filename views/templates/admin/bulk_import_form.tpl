{**
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
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

<div class="panel">
    <div class="panel-heading" style="position: relative;">
        <i class="icon-upload"></i> {$bulk_import_title}
        <div style="position: absolute; top: 10px; right: 15px;">
            <button type="button" class="btn btn-danger btn-sm" onclick="if(confirm('{$delete_confirm}')) { document.getElementById('deleteAllForm').submit(); }" title="{$delete_all_title}">
                <i class="icon-trash"></i>
            </button>
        </div>
    </div>
    <div class="panel-body">
        <form action="{$current_index}&configure={$module_name}&token={$admin_token}" method="post">
            <input type="hidden" name="token" value="{$admin_token}">
            <div class="form-group">
                <label>{$import_label}</label>
                <textarea name="bulk_import_data" rows="10" class="form-control" placeholder="{$import_placeholder}"></textarea>
                <p class="help-block">{$import_example}<br>1|Fast shipping worldwide<br>2|30-day money back guarantee<br>3|Free technical support</p>
            </div>
            <button type="submit" name="submitBulkImport" class="btn btn-primary">
                <i class="icon-upload"></i> {$import_button}
            </button>
        </form>
        
        <!-- Hidden form for delete functionality -->
        <form id="deleteAllForm" action="{$current_index}&configure={$module_name}&token={$admin_token}" method="post" style="display: none;">
            <input type="hidden" name="token" value="{$admin_token}">
            <input type="hidden" name="submitDeleteAll" value="1">
        </form>
    </div>
</div>
