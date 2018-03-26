/**
 CustomText editable input.
 Internally value stored as {city: "Moscow", street: "Lenina", building: "15"}

 @class customText
 @extends abstractinput
 @final
 @example
 <a href="#" id="customText" data-type="customText" data-pk="1">awesome</a>
 <script>
 $(function(){
    $('#customText').editable({
        url: '/post',
        title: 'Enter city, street and building #',
        value: {
            city: "Moscow",
            street: "Lenina",
            building: "15"
        }
    });
});
 </script>
 **/
(function ($) {
    "use strict";

    var CustomText = function (options) {
        this.init('customText', options, CustomText.defaults);

        //new to Select
        this.sourceData = options.source;
    };

    //inherit from Abstract input
    $.fn.editableutils.inherit(CustomText, $.fn.editabletypes.abstractinput);

    $.extend(CustomText.prototype, {
        /**
         Renders input from tpl

         @method render()
         **/
        render: function() {
            this.$input = this.$tpl.find('.custom-xeditable');


            //new to Select
            this.$list = this.$tpl.find('select');
            this.$list.empty();
            var fillItems = function ($el, data) {
                if ($.isArray(data)) {
                    for (var i = 0; i < data.length; i++) {
                        if (data[i].children) {
                            $el.append(fillItems($('<optgroup>', {label: data[i].text}), data[i].children));
                        } else {
                            $el.append($('<option>', {value: data[i].value}).text(data[i].text));
                        }
                    }
                }
                return $el;
            };

            var that = this;
            this.$list.each(function (i, el) {
                var name = $(this).attr('name');
                fillItems($(this), that.sourceData[name]);
            });
        },

        /**
         Default method to show value in element. Can be overwritten by display option.

         @method value2html(value, element)
         **/
        value2html: function (value, element) {
            if (!value) {
                $(element).empty();
                return;
            }

            var mainFieldValue = value[this.options.mainField];
            if (this.sourceData && this.sourceData[this.options.mainField]) {
                var text = [];
                $.each(this.sourceData[this.options.mainField], function (i, v) {
                    if (mainFieldValue.includes(Number(v.value)) || mainFieldValue.includes(String(v.value))) {
                        text.push(v.text);
                    }
                });

                value = text;
            } else {
                value = value[this.options.mainField];
            }
            var html = $('<div>').text(value).html();
            $(element).html(html);
        },

        /**
         Gets value from element's html

         @method html2value(html)
         **/
        html2value: function(html) {
            /*
             you may write parsing method to get value by element's html
             e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
             but for complex structures it's not recommended.
             Better set value directly via javascript, e.g.
             editable({
             value: {
             city: "Moscow",
             street: "Lenina",
             building: "15"
             }
             });
             */
            return null;
        },

        /**
         Converts value to string.
         It is used in internal comparing (not for sending to server).

         @method value2str(value)
         **/
        value2str: function(value) {
            var str = '';
            if(value) {
                for(var k in value) {
                    str = str + k + ':' + value[k] + ';';
                }
            }
            return str;
        },

        /*
         Converts string to value. Used for reading value from 'data-value' attribute.

         @method str2value(str)
         */
        str2value: function(str) {
            /*
             this is mainly for parsing value defined in data-value attribute.
             If you will always set value by javascript, no need to overwrite it
             */
            return str;
        },

        /**
         Sets value of input.

         @method value2input(value)
         @param {mixed} value
         **/
        value2input: function(value) {
            if(!value) {
                return;
            }
            this.$input.filter('[class="custom-xeditable"]').each(function (i, el) {
                var name = $(this).attr('name');
                $(this).val(value[name]);
            });

            //need enhancemnt
            this.$list.each(function (i, el) {
                var name = $(this).attr('name');
                $(this).val(value[name]);
                $(this).select2();
            });

        },

        /**
         Returns value of input.

         @method input2value()
         **/
        input2value: function() {
            var arr = {};
            this.$input.filter('[class="custom-xeditable"]').each(function (i, el) {
                var name = $(this).attr('name');
                arr[name] = $(this).val()
            });

            //need to get arr values dinamicly

            this.$list.each(function (i, el) {
                var name = $(this).attr('name');
                arr[name] = $(this).select2('val');
            });
            return arr;
        },

        /**
         Activates input: sets focus on the first field.

         @method activate()
         **/
        activate: function() {
            this.$input.filter('[name="'+this.options.mainField+'"]').focus();
        },

        /**
         Attaches handler to submit form in case of 'showbuttons=false' mode

         @method autosubmit()
         **/
        autosubmit: function() {
            this.$input.keydown(function (e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
            });
        }
    });

    CustomText.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        mainField: null,
        inputclass: '',
        source: []
    });

    $.fn.editabletypes.customText = CustomText;

}(window.jQuery));