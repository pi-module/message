(function($) {
    var options;
    var app = {
        init: function() {
            this.cacheElements();
            this.bindEvents();
        },
        $: function(selector) {
            return this.$el.find(selector);
        },
        cacheElements: function() {
            this.$el = $('#message-js');
            this.$form = this.$('form');
            this.$content = this.$("[name='content']");
        },
        bindEvents: function() {
            this.$form.submit(this.submitAction);
            this.$content.focus(this.conFocus);
        },
        submitAction: function() {
            var self = $('[name="content"]'),
                sendTxt = $('.message-send-text'),
                val = self.val();
            sendTxt.find('span').remove();
            if (val == '') {
                sendTxt.append('<span></span>');
                sendTxt.find('span').addClass('pull-right message-help-block message-error').html('You can’t send a empty message');
                self.addClass('message-username');
                return false;
            }  
        },
        conFocus: function() {
            $(this).removeClass('message-username');
            $(this).parent().find('span').empty();
        },
    };

    this.messageIndex = function(opts) {
        options = opts || {};
        app.init();
    };
})(jQuery);