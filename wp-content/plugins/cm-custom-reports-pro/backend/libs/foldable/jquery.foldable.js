/*
 * jquery.foldable.js (https://github.com/MythThrazz/jquery-foldable)
 * @author Marcin Dudek
 * @version 1.1
 *
 * Copyright (c) 2013 Marcin Dudek aka MythThrazz (https://github.com/MythThrazz)
 * The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

(function ($, undefined) {

    var defaultOptions = {
        event: 'click', /* nazwa zdarzenia odpalającego zwijanie */
        hideText: 'Fold', /* tekst przycisku zwijającego */
        showText: 'Unfold', /* tekst przycisku rozwijającego */
        titleWrapper: '<h2/>', /* wrapper tytułu dla zwijalnego bloku */
        titleText: undefined, /* tekst tytułu zwijalnego bloku */
        titleAsHtml: false, /* czy tytul ma być wstawiony jako tekst czy html */
        titleSearchSelector: 'h1,h2,h3,h4,h5,h6', /* selektor tytułu dla zwijalnego bloku */
        defaultFold: false, /* czy bloki domyślnie zwinięte */
        showTime: 100, /* czas animacji pokazującej tytuł zwijalnego bloku */
        hideTime: 'fast', /* czas animacji ukrywającej tytuł zwijalnego bloku */
        animationTime: 'slow' /* czas animacji slide */
    };

    var api = {
        log: function () {
            console.log(this);
        },
        toggle: function () {
            var options = $(this).data('foldable').options;
            return this.find('.foldable-row').trigger(options.event + '.foldable');
        },
        show: function () {
            var options = $.extend({'action': 'show'}, defaultOptions, $(this).data('foldable').options || {});
            ;
            return api.showHide(this.find('.foldable-row'), options, false);
        },
        hide: function () {
            var options = $.extend({'action': 'hide'}, defaultOptions, $(this).data('foldable').options || {});
            ;
            return api.showHide(this.find('.foldable-row'), options, false);
        },
        setInLocalStorage: function (object, value) {
            var id, foldableObject;

            if (object.hasClass('foldable'))
            {
                foldableObject = object;
            }
            else
            {
                foldableObject = object.parents('.foldable');
            }
            id = (foldableObject) ? foldableObject.attr('id') : null;
            if (localStorage && typeof id === 'string' && typeof value !== 'undefined')
            {
                localStorage[id] = value;
            }
            else
            {
                return null;
            }
        },
        getFromLocalStorage: function (object) {
            var id, foldableObject;

            if (object.hasClass('foldable'))
            {
                foldableObject = object;
            }
            else
            {
                foldableObject = object.parents('.foldable');
            }
            id = (foldableObject) ? foldableObject.attr('id') : null;
            if (localStorage && typeof id === 'string')
            {
                return localStorage[id];
            }
            else
            {
                return null;
            }
        },
        showHide: function (object, options, returnValue) {
            var hideTime, animationTime, showTime, $object;

            $object = $(object);

            if (options.quickHide)
            {
                hideTime = 0;
                showTime = 0;
                animationTime = 0;
            }
            else
            {
                hideTime = options.hideTime;
                showTime = options.showTime;
                animationTime = options.animationTime;
            }

            var action = (options.action) ? options.action : 'toggle';

            var $elementToFoldUnfold = $object.siblings('.foldable-wrapper');
            var $btn = $object.find('.foldable-button');
            var $title = $object.find(options.titleSearchSelector);

            /*
             * Unfold
             */
            if ((action === 'toggle' && !$elementToFoldUnfold.is(':visible')) || action === 'show')
            {
                api.setInLocalStorage($object, 'unfolded');
                $object.addClass('unfolded');
                $object.removeClass('folded');
                $title.hide(hideTime);
                $elementToFoldUnfold.slideDown(animationTime);
                $btn.text(options.hideText);
                $btn.addClass('on');
            }
            /*
             * Fold
             */
            else if ((action === 'toggle' && $elementToFoldUnfold.is(':visible')) || action === 'hide')
            {
                api.setInLocalStorage($object, 'folded');
                $object.addClass('folded');
                $object.removeClass('unfolded');
                $elementToFoldUnfold.slideUp(animationTime, function () {
                    $title.show(showTime);
                });
                $btn.text(options.showText);
                $btn.removeClass('on');
            }

            return returnValue;
        },
        init: function (options) {

            var $this = $(this), data = $this.data('foldable');
            options = $.extend({}, defaultOptions, options || {});
            if (options.defaultFold)
            {
                var quickOptions = $.extend({quickHide: true}, options);
            }

            if (!data)
            {
                $(this).data('foldable', {
                    options: options
                });
            }

            return this.each(function () {
                var $foldableTitleText, localStorageSetting;
                var $foldable = this;
                var $foldableWrapper = $('<div class="foldable-wrapper" />');
                var $foldableRow = $('<div class="foldable-row unfolded" />');
                var $button = $('<span class="btn-small foldable-button screen-only pull-right on">' + options.hideText + '</button>');

                localStorageSetting = api.getFromLocalStorage($($foldable));

//                $($foldableRow).append($button);
                $($foldable).children().wrapAll($foldableWrapper);
                $($foldable).prepend($foldableRow);

                var $title = $($foldable).find(options.titleSearchSelector);
                if ($title.length > 0)
                {
                    $foldableTitleText = $($title[0]).text();
                }

                if (options.titleText)
                {
                    if (typeof options.titleText === 'function')
                    {
                        var test = options.titleText;
                        $foldableTitleText = test.apply($foldable);
                    }
                    else
                    {
                        $foldableTitleText = options.titleText;
                    }
                }

                if ($foldableTitleText)
                {
                    var $foldableTitle = $(options.titleWrapper, {
                        "class": 'foldable-title'
                    });

                    if (options.titleAsHtml)
                    {
                        if (typeof $foldableTitleText === 'object')
                        {
                            $foldableTitleText.detach();
                            $foldableTitle.append($foldableTitleText.html());
                        }
                        else if (typeof $foldableTitleText === 'string')
                        {
                            $foldableTitle.html($foldableTitleText);
                        }
                    }
                    else
                    {
                        $foldableTitle.text($foldableTitleText);
                    }

                    $($foldableTitle).hide();
                    $foldableRow.append($foldableTitle);
                }

                $foldableRow.append('<div class="clear clearfix"></div>');

                $($foldable).on(options.event + '.foldable', '.foldable-row', function (e) {
                    api.currentEvent = e;
                    var $target = $(e.target), returnValue = true;

                    /*
                     * If the clicked element has one of the following classes we don't want the event to bubble
                     */
                    if ($target.hasClass('foldable-row') || $target.hasClass('foldable-title') || $target.hasClass('foldable-button') || $target.hasClass('foldable-item'))
                    {
                        returnValue = false;
                    }
                    api.showHide($foldableRow, options, returnValue);

                });

                if (options.defaultFold && localStorageSetting !== 'unfolded')
                {
                    api.showHide($foldableRow, quickOptions);
                }
            });
        },
        destroy: function () {
            return this.each(function () {
                var $this = $(this),
                        data = $this.data('foldable');

                var options = data.options;

                var $foldableWrapper = $this.find('.foldable-wrapper');
                var $foldableRow = $this.find('.foldable-row');

                $foldableRow.find('.folded').trigger(options.event);

                $foldableWrapper.children().unwrap();
                $foldableRow.off('.foldable');
                $foldableRow.remove();

                $this.removeData('foldable');
            });
        }
    };
    $.fn.foldable = function (options) {
        // Method calling logic
        if (api[options]) {
            return api[options].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof options === 'object' || !options) {
            return api.init.apply(this, arguments);
        }
        else {
            $.error('Method ' + options + ' does not exist on jQuery.foldable');
        }
    };
})(jQuery);