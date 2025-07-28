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
 * @author Dzemal Imamovic <dzemal.imamovic@outlook.com>
 * @version 1.0.6
 * @license MIT
 *}

document.addEventListener('DOMContentLoaded', function() {
    const shortFactsContainer = document.querySelector('.product-short-facts');
    
    if (shortFactsContainer) {
        // Add CSS class based on the hook context
        addHookSpecificClasses();
        
        // Initialize animations
        initializeAnimations();
        
        // Add hover effects
        addInteractiveEffects();
        
        // Make it accessible
        enhanceAccessibility();
    }
    
    function addHookSpecificClasses() {
        // Detect which hook is being used based on container location
        const rightColumn = document.querySelector('#right_column');
        const productInfo = document.querySelector('.product-information');
        const priceBlock = document.querySelector('.product-prices');
        const reassurance = document.querySelector('.reassurance');
        
        if (rightColumn && rightColumn.contains(shortFactsContainer)) {
            shortFactsContainer.classList.add('hook-displayRightColumn');
        } else if (productInfo && productInfo.contains(shortFactsContainer)) {
            shortFactsContainer.classList.add('hook-displayProductAdditionalInfo');
        } else if (priceBlock && priceBlock.contains(shortFactsContainer)) {
            shortFactsContainer.classList.add('hook-displayProductPriceBlock');
        } else if (reassurance && reassurance.contains(shortFactsContainer)) {
            shortFactsContainer.classList.add('hook-displayReassurance');
        }
    }
    
    function initializeAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    animateShortFacts(entry.target);
                }
            });
        }, observerOptions);
        
        observer.observe(shortFactsContainer);
    }
    
    function animateShortFacts(container) {
        const items = container.querySelectorAll('.short-fact-item');
        items.forEach((item, index) => {
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 150);
        });
    }
    
    function addInteractiveEffects() {
        const items = shortFactsContainer.querySelectorAll('.short-fact-item');
        
        items.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
                
                const checkmark = this.querySelector('.short-fact-checkmark svg');
                if (checkmark) {
                    checkmark.style.transform = 'scale(1.2) rotate(5deg)';
                }
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
                
                const checkmark = this.querySelector('.short-fact-checkmark svg');
                if (checkmark) {
                    checkmark.style.transform = 'scale(1) rotate(0deg)';
                }
            });
        });
    }
    
    function enhanceAccessibility() {
        // Add ARIA labels
        shortFactsContainer.setAttribute('aria-label', 'Product key features');
        shortFactsContainer.setAttribute('role', 'complementary');
        
        const list = shortFactsContainer.querySelector('.short-facts-list');
        if (list) {
            list.setAttribute('role', 'list');
        }
        
        const items = shortFactsContainer.querySelectorAll('.short-fact-item');
        items.forEach((item, index) => {
            item.setAttribute('role', 'listitem');
            item.setAttribute('aria-label', `Key feature ${index + 1}`);
        });
        
        // Add keyboard navigation
        items.forEach((item, index) => {
            item.setAttribute('tabindex', '0');
            
            item.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
                
                if (e.key === 'ArrowDown' && items[index + 1]) {
                    e.preventDefault();
                    items[index + 1].focus();
                }
                
                if (e.key === 'ArrowUp' && items[index - 1]) {
                    e.preventDefault();
                    items[index - 1].focus();
                }
            });
        });
    }
    
    // Analytics tracking (optional)
    function trackShortFactsInteraction() {
        const items = shortFactsContainer.querySelectorAll('.short-fact-item');
        
        items.forEach((item, index) => {
            item.addEventListener('click', function() {
                // Google Analytics 4
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'short_fact_click', {
                        'event_category': 'product_interaction',
                        'event_label': this.querySelector('.short-fact-text').textContent.trim(),
                        'value': index + 1
                    });
                }
                
                // Facebook Pixel
                if (typeof fbq !== 'undefined') {
                    fbq('track', 'ViewContent', {
                        content_type: 'product_feature',
                        content_ids: [index + 1]
                    });
                }
            });
        });
    }
    
    // Initialize tracking if analytics are available
    if (typeof gtag !== 'undefined' || typeof fbq !== 'undefined') {
        trackShortFactsInteraction();
    }
});

// Admin panel JavaScript for dynamic form management
if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        // Sortable short facts
        if ($('#short-facts-list').length) {
            $('#short-facts-list').sortable({
                handle: '.sort-handle',
                placeholder: 'sort-placeholder',
                update: function(event, ui) {
                    updateSortOrder();
                }
            });
        }
        
        function updateSortOrder() {
            $('#short-facts-list .short-fact-row').each(function(index) {
                $(this).find('input[name*="short_facts"]').attr('name', 'short_facts[' + index + ']');
                $(this).find('label').text('Short Fact #' + (index + 1));
            });
        }
        
        // Character counter for short facts
        $(document).on('input', '.short-fact-input', function() {
            const maxLength = 100;
            const currentLength = $(this).val().length;
            const remaining = maxLength - currentLength;
            
            let counter = $(this).siblings('.char-counter');
            if (counter.length === 0) {
                counter = $('<small class="char-counter"></small>');
                $(this).after(counter);
            }
            
            counter.text(remaining + ' characters remaining');
            
            if (remaining < 10) {
                counter.addClass('text-warning');
            } else {
                counter.removeClass('text-warning');
            }
            
            if (remaining < 0) {
                counter.addClass('text-danger').removeClass('text-warning');
                $(this).addClass('has-error');
            } else {
                counter.removeClass('text-danger');
                $(this).removeClass('has-error');
            }
        });
        
        // Auto-save functionality
        let autoSaveTimeout;
        $(document).on('input', '.short-fact-input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(function() {
                // Auto-save logic here if needed
                console.log('Auto-saving short facts...');
            }, 2000);
        });
    });
}