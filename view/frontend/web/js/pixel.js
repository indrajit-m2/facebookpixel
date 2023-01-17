define(
    [
        'jquery'
    ],
    function ($) {
        return function (configData) {
            var isDisabled = configData.isDisabled;
            var id            = configData.id;
            var action        = configData.actionName;
            var registration        = configData.registration;
            var productView = configData.productViewData;
            var categoryView = configData.categoryViewData;
            var search = configData.searchData;
            var addToWishList = configData.addToWishList;
            var initiateCheckout = configData.initiateCheckout;
            var orderData  = configData.orderData;
            window.isQuickSearch = configData.quickSearch
            console.log(configData);

            !function(f,b,e,v,n,t,s) {if(f.fbq)return;n=f.fbq=function() {n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)
            }(window, document,'script', 'https://connect.facebook.net/en_US/fbevents.js');

            window.pixel = function(){
                fbq('init', id);

                if(isDisabled === "disabled") {
                    return true;
                }

                fbq('track', 'PageView');

                if(registration !== 0){
                    fbq('track', 'CompleteRegistration', {
                        customer_id: registration.customer_id
                    });
                }
                if (search !== "0") {
                    fbq('track', 'Search', {
                        search_key : search
                    });
                }
                if (productView !== 0) {
                    fbq('track', 'ViewContent', {
                        content_name: productView.content_name ,
                        content_ids: productView.content_ids,
                        content_type: 'product',
                        value: productView.value,
                        currency: productView.currency
                    });
                }
                if (categoryView !== 0) {
                    fbq('trackCustom', 'ViewCategory', {
                        content_name: categoryView.content_name,
                        content_ids: categoryView.content_ids,
                        content_type: 'category',
                        currency: categoryView.currency
                    });
                }
                if (addToWishList !== 0) {
                    fbq('track', 'AddToWishlist', {
                        content_type : 'wishlist_product',
                        content_ids : addToWishList.content_ids,
                        content_name : addToWishList.content_name,
                        currency : addToWishList.currency
                    });
                }

                if (initiateCheckout !== 0) {
                    fbq('track', 'InitiateCheckout', {
                        content_ids: initiateCheckout.content_ids,
                        content_type: 'checkout_product',
                        contents: initiateCheckout.contents,
                        value: initiateCheckout.value,
                        currency: initiateCheckout.currency
                    });
                }

                if (orderData !== 0) {
                    fbq('track', 'Purchase', {
                        content_ids: orderData.content_ids,
                        content_type: 'product',
                        contents: orderData.contents,
                        value: orderData.value,
                        num_items : orderData.num_items,
                        currency: orderData.currency
                    });
                }
            }

            return window.pixel();

        }
    });
