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

<div class="panel product-tab">
    <header class="panel-heading">
        <h3 class="panel-title">
            <i class="icon-check" aria-hidden="true"></i>
            {l s='Product Short Facts' mod='productshortfacts'}
        </h3>
    </header>
    
    <div class="panel-body">
        <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
        
        <div class="alert alert-info" role="status">
            <p>{l s='Add key features and highlights for this product. These will be displayed with checkmarks to attract customer attention.' mod='productshortfacts'}</p>
        </div>
        
        <section class="short-facts-container">
            <div id="short-facts-list" role="list">
                {if $short_facts && count($short_facts) > 0}
                    {foreach from=$short_facts item=fact name=facts}
                        <div class="short-fact-row" data-index="{$smarty.foreach.facts.index}" role="listitem">
                            <div class="form-group row">
                                <label class="control-label col-lg-2" for="short-fact-{$smarty.foreach.facts.index}">
                                    {l s='Short Fact' mod='productshortfacts'} #{$smarty.foreach.facts.index + 1}
                                </label>
                                <div class="col-lg-8">
                                    <input type="text" 
                                           id="short-fact-{$smarty.foreach.facts.index}"
                                           name="short_facts[{$smarty.foreach.facts.index}]" 
                                           value="{$fact.shortfact_text|escape:'htmlall':'UTF-8'}" 
                                           class="form-control short-fact-input"
                                           placeholder="{l s='Enter a short fact or feature...' mod='productshortfacts'}"
                                           maxlength="255"
                                           aria-describedby="short-fact-help-{$smarty.foreach.facts.index}" />
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-fact" aria-label="{l s='Remove this fact' mod='productshortfacts'}">
                                        <i class="icon-trash" aria-hidden="true"></i> {l s='Remove' mod='productshortfacts'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {else}
                    <div class="short-fact-row" data-index="0" role="listitem">
                        <div class="form-group row">
                            <label class="control-label col-lg-2" for="short-fact-0">
                                {l s='Short Fact' mod='productshortfacts'} #1
                            </label>
                            <div class="col-lg-8">
                                <input type="text" 
                                       id="short-fact-0"
                                       name="short_facts[0]" 
                                       value="" 
                                       class="form-control short-fact-input"
                                       placeholder="{l s='Enter a short fact or feature...' mod='productshortfacts'}"
                                       maxlength="255"
                                       aria-describedby="short-fact-help-0" />
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-danger btn-sm remove-fact" aria-label="{l s='Remove this fact' mod='productshortfacts'}">
                                    <i class="icon-trash" aria-hidden="true"></i> {l s='Remove' mod='productshortfacts'}
                                </button>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
            
            <div class="form-group row">
                <div class="col-lg-offset-2 col-lg-10">
                    <button type="button" id="add-short-fact" class="btn btn-primary">
                        <i class="icon-plus" aria-hidden="true"></i> {l s='Add Short Fact' mod='productshortfacts'}
                    </button>
                </div>
            </div>
        </section>
        
        <section class="form-actions">
            <div class="form-group row">
                <div class="col-lg-offset-2 col-lg-10">
                    <button type="submit" id="save-short-facts" class="btn btn-success">
                        <i class="icon-save" aria-hidden="true"></i> {l s='Save Short Facts' mod='productshortfacts'}
                    </button>
                </div>
            </div>
        </section>
        
        <aside class="alert alert-success" role="complementary">
            <h4>{l s='Examples of effective short facts:' mod='productshortfacts'}</h4>
            <ul class="list-unstyled">
                <li><i class="icon-check" aria-hidden="true"></i> Free shipping worldwide</li>
                <li><i class="icon-check" aria-hidden="true"></i> 30-day money back guarantee</li>
                <li><i class="icon-check" aria-hidden="true"></i> 24/7 customer support</li>
                <li><i class="icon-check" aria-hidden="true"></i> Made from premium materials</li>
                <li><i class="icon-check" aria-hidden="true"></i> Easy installation in 5 minutes</li>
            </ul>
        </aside>
        
        <div class="alert alert-info" role="note">
            <div class="d-flex align-items-start">
                <i class="icon-info-circle me-3 fs-4" aria-hidden="true"></i>
                <div>
                    <p class="mb-2"><strong>This module automatically loads GTM container and configures GA4 integration.</strong></p>
                    <p class="mb-2">The recommended solution for professional analytics implementation.</p>
                    
                    <hr class="my-2" style="border-color: rgba(255,255,255,0.2)">
                    
                    <p class="mb-1"><strong>Need expertise with PrestaShop?</strong></p>
                    <ul class="ps-3 mb-2">
                        <li>Module Development & Updates</li>
                        <li>PrestaShop Migrations</li>
                        <li>Server Optimization</li>
                        <li>Performance Tuning</li>
                    </ul>
                    
                    <div class="bg-light p-2 rounded">
                        <p class="mb-1 small text-muted">
                            Certified Software Developer | 10+ Years E-Commerce Experience
                        </p>

                        <p class="mb-1">
                            <i class="icon-certificate me-1" aria-hidden="true"></i> <strong>Dzemal Imamovic</strong>
                        </p>

                        <div class="mb-2 text-center">
                            <a href="https://buymeacoffee.com/jimmyweb" target="_blank" class="btn btn-outline-warning btn-sm me-1" rel="noopener noreferrer">
                                <i class="icon-gift me-1" aria-hidden="true"></i> Buy Me a Coffee
                            </a>
                            <a href="https://www.paypal.com/paypalme/jimmyweb" target="_blank" class="btn btn-outline-primary btn-sm" rel="noopener noreferrer">
                                <i class="icon-money me-1" aria-hidden="true"></i> Donate via PayPal
                            </a>
                        </div>

                        <p class="mb-0 small">
                            <a href="mailto:dzemal.imamovic@outlook.com" class="alert-link">dzemal.imamovic@outlook.com</a> | 
                            <a href="https://www.fiverr.com/s/qDvrV1g" target="_blank" rel="noopener noreferrer" class="alert-link">Fiverr Profile</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.short-fact-row {
    margin-bottom: 15px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #f9f9f9;
}

.short-fact-row .form-group {
    margin-bottom: 0;
}

.short-fact-row .form-group.row {
    display: flex;
    align-items: center;
}

.short-fact-input {
    font-size: 14px;
}

.short-fact-input.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.remove-fact {
    margin-top: 0;
}

#add-short-fact, #save-short-facts {
    margin-top: 10px;
    margin-right: 10px;
}

.alert ul {
    margin-bottom: 0;
    padding-left: 20px;
}

.alert li {
    margin-bottom: 5px;
    color: #28a745;
    font-weight: 500;
}

.short-facts-container {
    margin-bottom: 20px;
}
</style>

<script type="text/javascript">
$(document).ready(function() {
    var factIndex = {if $short_facts && count($short_facts) > 0}{count($short_facts)}{else}1{/if};
    
    // Form validation before submission
    $('form').on('submit', function(e) {
        var isValid = true;
        var hasContent = false;
        
        $('.short-fact-input').each(function() {
            var value = $(this).val().trim();
            if (value !== '') {
                hasContent = true;
                // Basic validation - check for minimum length
                if (value.length < 3) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!hasContent) {
            e.preventDefault();
            alert('{l s="Please add at least one short fact before saving." mod="productshortfacts"}');
            return false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('{l s="Please correct the invalid short facts before saving. Each fact should be at least 3 characters long." mod="productshortfacts"}');
            return false;
        }
        
        return true;
    });
    
    // Add new short fact
    $('#add-short-fact').on('click', function() {
        var newRowHtml = '<div class="short-fact-row" data-index="' + factIndex + '">' +
            '<div class="form-group row">' +
                '<label class="control-label col-lg-2">' +
                    '{l s="Short Fact" mod="productshortfacts"} #' + (factIndex + 1) +
                '</label>' +
                '<div class="col-lg-8">' +
                    '<input type="text" ' +
                           'name="short_facts[' + factIndex + ']" ' +
                           'value="" ' +
                           'class="form-control short-fact-input" ' +
                           'placeholder="{l s="Enter a short fact or feature..." mod="productshortfacts"}" ' +
                           'maxlength="255" />' +
                '</div>' +
                '<div class="col-lg-2">' +
                    '<button type="button" class="btn btn-danger btn-sm remove-fact">' +
                        '<i class="icon-trash"></i> {l s="Remove" mod="productshortfacts"}' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        $('#short-facts-list').append(newRowHtml);
        factIndex++;
        updateLabels();
        
        // Focus on the new input field
        $('.short-fact-row:last .short-fact-input').focus();
    });
    
    // Remove short fact
    $(document).on('click', '.remove-fact', function() {
        if ($('.short-fact-row').length > 1) {
            $(this).closest('.short-fact-row').remove();
            updateLabels();
            factIndex = $('.short-fact-row').length;
        } else {
            alert('{l s="You must have at least one short fact field." mod="productshortfacts"}');
        }
    });
    
    // Update labels and indices after add/remove
    function updateLabels() {
        $('.short-fact-row').each(function(index) {
            $(this).find('label').text('{l s="Short Fact" mod="productshortfacts"} #' + (index + 1));
            $(this).find('input').attr('name', 'short_facts[' + index + ']');
            $(this).attr('data-index', index);
        });
    }
    
    // Real-time input validation
    $(document).on('input', '.short-fact-input', function() {
        var value = $(this).val().trim();
        if (value !== '' && value.length < 3) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Initialize with proper indices
    updateLabels();
});
</script>