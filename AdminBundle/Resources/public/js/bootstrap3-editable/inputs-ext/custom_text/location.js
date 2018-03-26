(function ($) {
    "use strict";

    var Location = function (options) {
        this.sourceCountryData = options.sourceCountry;
        this.init('location', options, Location.defaults);
    };

    //inherit from Abstract input
    $.fn.editableutils.inherit(Location, $.fn.editabletypes.abstractinput);

    $.extend(Location.prototype, {

        render: function () {
            this.$input = this.$tpl.find('input');
            this.$list = this.$tpl.find('select');

            this.$list.empty();

            var fillItems = function ($el, data) {
                if ($.isArray(data)) {
                    for (var i = 0; i < data.length; i++) {
                        if (data[i].children) {
                            $el.append(fillItems($('<optgroup>', {
                                label: data[i].text
                            }), data[i].children));
                        } else {
                            $el.append($('<option>', {
                                value: data[i].value
                            }).text(data[i].text));
                        }
                    }
                }
                return $el;
            };

            fillItems(this.$list, this.sourceCountryData);


        },

        value2html: function (value, element) {
            if (!value) {
                $(element).empty();
                return;
            }
            var countryText = value.country;
            $.each(this.sourceCountryData, function (i, v) {
                if (v.value == countryText) {
                    countryText = v.text.toUpperCase();
                }
            });
            var html = $('<div>').text(value.city).html() + ' / ' + $('<div>').text(countryText).html();
            $(element).html(html);
        },

        html2value: function (html) {
            return null;
        },

        value2str: function (value) {
            var str = '';
            if (value) {
                for (var k in value) {
                    str = str + k + ':' + value[k] + ';';
                }
            }
            return str;
        },

        str2value: function (str) {
            return str;
        },

        value2input: function (value) {
            if (!value) {
                return;
            }
            this.$input.filter('[name="city"]').val(value.city);
            this.$list.val(value.country);
        },

        input2value: function () {
            return {
                city: this.$input.filter('[name="city"]').val(),
                country: this.$list.val()
            };
        },

        activate: function () {
            this.$input.filter('[name="city"]').focus();
        },

        autosubmit: function () {
            this.$input.keydown(function (e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
            });
        }
    });

    Location.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        tpl: '' +
        '<div class="editable-location">' +
        '<label><span>City: </span><input type="text" name="city"></label>' +
        '</div>' +
        '<div class="editable-location">' +
        '<label><span>Country: </span><select name="country" ></select></label>' +
        '</div>',

        inputclass: '',
        showbuttons: 'bottom', //WHY ISN'T THIS WORKING!!!
        sourceCountry: []
    });

    $.fn.editabletypes.location = Location;

}(window.jQuery));