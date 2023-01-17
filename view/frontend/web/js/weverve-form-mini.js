define([
    'jquery',
    'underscore',
    'jquery/ui',
], function ($,_) {

    return function (widget) {
        $.widget('indrajit.quickSearch', widget, {
            _updateAriaHasPopup: function (show) {
                if (show) {
                    if (typeof fbq !== 'undefined') {
                        if(this._isSend(this.element.val())) {
                            fbq('track', 'Search', {
                                search_key: this.element.val()
                            });
                        }
                    }
                    this.element.attr('aria-haspopup', 'true');
                } else {
                    this.element.attr('aria-haspopup', 'false');
                }
            },
            _isSend : function (key) {
                console.log('In is Send');
                if(typeof window.isQuickSearch === "undefined"){
                    return false;
                }
                if(window.isQuickSearch === 1){
                    var keyWords    = sessionStorage.getItem('searchKeys');
                    var keyData     = [];
                    if(keyWords === null){
                        sessionStorage.setItem('searchKeys',key);
                        window.previousTime = new Date();
                        return true;
                    }else{
                        keyData = keyWords.split(',');
                        if(keyData.indexOf(key) >= 0 && this._isValidTime()){
                            keyData.push(key);
                            return  true;
                        }
                    }
                }
                return false;
            },
            _isValidTime : function () {
                var currentTime =  new Date();
                var diff        = currentTime - window.previousTime;
                var second      = Math.floor(diff/1000);
                return second > 3;
            }
        });
        return $.indrajit.quickSearch;
    };

});
