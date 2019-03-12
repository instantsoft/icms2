(function ($) {
    'use strict';

    var defaults = {};

    function Menu(element, options) {
        this.$el = $(element);
        this.opt = $.extend(true, {}, defaults, options);

        this.init(this);
    }

    Menu.prototype = {
        init: function (self) {
            $(document).on('click', function (e) {
                var $target = $(e.target);

                if ($target.closest(self.$el.data('menu-toggle'))[0]) {
                    $target = $target.closest(self.$el.data('menu-toggle'));

                    self.$el
                        .css(self.calcPosition($target))
                        .toggleClass('show');

                    e.preventDefault();
                } else if (!$target.closest(self.$el)[0]) {
                    self.$el.removeClass('show');
                }
            });
        },

        calcPosition: function ($target) {
            var windowWidth, targetOffset, position;

            windowWidth = $(window).width();
            targetOffset = $target.offset();

            position = {
                top: targetOffset.top + ($target.outerHeight() / 2)
            };

            if (targetOffset.left > windowWidth / 2) {
                this.$el
                    .addClass('menu--right')
                    .removeClass('menu--left');

                position.right = (windowWidth - targetOffset.left) - ($target.outerWidth() / 2);
                position.left = 'auto';
            } else {
                this.$el
                    .addClass('menu--left')
                    .removeClass('menu--right');

                position.left = targetOffset.left + ($target.outerWidth() / 2);
                position.right = 'auto';
            }

            return position;
        }
    };

    $.fn.menu = function (options) {
        return this.each(function () {
            if (!$.data(this, 'menu')) {
                $.data(this, 'menu', new Menu(this, options));
            }
        });
    };
})(window.jQuery);

$.fn.toggleText = function (t1, t2) {
    if (this.text() == t1) {
        this.text(t2);
    } else {
        this.text(t1);
    }

    return this;
};

$(function () {
    $('[data-menu]').menu();

    $(document).on('click', '.widgets-menu .dropdown-menu_link', function (e) {
        e.preventDefault();

        let _this = $(this),
            data = _this.data(),
            save_visible_url = _this.parents('.widgets-menu').data('save_visible_url'),
            visibles = [];

        $('#db_' + data.id).toggleClass('hidden');
        $('.material-icons', _this).toggleText('radio_button_checked', 'radio_button_unchecked');

        $('#dashboard .col').each(function () {

            let _this = $(this),
                id = _this.data('id');

            visibles.push({id: id, visible: _this.hasClass('hidden') ? 0 : 1});
        });

        $.post(save_visible_url, {items: visibles}, function () {});
    });
});