define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (ko,Component, customerData) {
    'use strict';
    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            customerData.get('indrajit_subscribe_section').subscribe(function (subscribeData) {
                if (subscribeData) {
                    if(typeof subscribeData.id !== 'undefined') {
                        fbq('track', 'Subscribe', {
                            id: subscribeData.id,
                            email: subscribeData.email,
                            value: 0.00,
                            currency: subscribeData.currentCurrency
                        });
                        customerData.set('indrajit_subscribe_section', {})
                    }
                }
            });
        }
    });
});
