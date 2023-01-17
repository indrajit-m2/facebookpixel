define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (ko,Component, customerData) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            var self = this;
            self._super();
            customerData.get('indrajit_add_to_cart_section').subscribe(function (cartData) {
                if (cartData) {
                    if(typeof cartData.content_type !== "undefined") {
                        console.log(cartData);
                        fbq('track', 'AddToCart', cartData);
                        customerData.set('indrajit_add_to_cart_section', {})
                    }
                }
            });
        }
    });
});
