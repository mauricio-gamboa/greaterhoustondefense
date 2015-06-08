var AdminUtil = {

    doPost:function (params, type, successCallback, failureCallback) {
        jQuery.ajax({type:"POST",
            url:ajaxurl,
            dataType:type,
            data:params,
            success:function (html, textStatus, jqXHR) {
                if (typeof successCallback == 'function') {
                    successCallback(html);
                }
            },
            error:function (jqXHR, textStatus, errorThrown) {
                if (typeof failureCallback == 'function') {
                    failureCallback(errorThrown + ': ' + jqXHR.responseText);
                } else {
                    alert(errorThrown + ': ' + jqXHR.responseText);
                }
            }
        });
    },

    getFormParams:function (formSelector) {

        if (jQuery(formSelector).valid()) {
            var params = {};
            jQuery(formSelector + " :input").each(function () {
                var field = jQuery(this);
                var fieldName = field.attr('name');
                params[fieldName] = field.val();
            });
            return params;
        } else {
            return false;
        }
    },

    uuid:function () {
        // http://www.ietf.org/rfc/rfc4122.txt
        var s = [];
        var hexDigits = "0123456789abcdef";
        for (var i = 0; i < 36; i++) {
            s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
        }
        s[14] = "4";  // bits 12-15 of the time_hi_and_version field to 0010
        s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);  // bits 6-7 of the clock_seq_hi_and_reserved to 01
        s[8] = s[13] = s[18] = s[23] = "-";
        return s.join("");
    },

    isRequiredFieldValid:function (fieldId) {
        var field = jQuery('#' + fieldId);
        if (jQuery.trim(field.val()).length > 0) {
            field.css('border-color', '');
            field.css('border', '');
            return true;
        } else {
            field.css('border-color', '#ff0000');
            field.css('border', '1px solid #ff0000');
            return false;
        }
    }
};

(function ($) {

    $.fn.colorRadioButtons = function () {
        var invoker = $(this);
        var refId = invoker.data('ref');
        var refField = $('#' + refId);
        var defaultSelectedValue = refField.val();
        var links = invoker.find("li > a");

        setValue(defaultSelectedValue);

        links.click(function (e) {
            e.preventDefault();
            var color = $(this).data('color');
            setValue(color);
            return false;
        });

        function setValue(value) {
            refField.val(value);
            links.removeClass('selected');
            links.each(function () {
                var color = $(this).data('color');
                if (color == value) {
                    $(this).addClass('selected');
                }
            });
        }

    };

})(jQuery);

jQuery(document).ready(function ($) {

    function doAddOrUpdate(button, formSelector, refreshListSelector) {
        var formParams = AdminUtil.getFormParams(formSelector);
        if (formParams) {
            button.attr('disabled', 'disabled');
            AdminUtil.doPost(formParams, 'html', function (response) {
                tb_remove();
                $(refreshListSelector).html(response);
            }, function (errorThrown) {
                button.removeAttr('disabled');
                alert(errorThrown);
            });
        }
        return false;
    }

    function doDelete(link, refreshListSelector) {
        if (confirm("Are you sure you want to proceed?")) {
            var formParams = link.attr('href');
            AdminUtil.doPost(formParams, 'html', function (response) {
                $(refreshListSelector).html(response);
            }, function (errorThrown) {
                alert(errorThrown);
            });
        }
        return false;
    }

    //---------------------------------------------- Theme Options -----------------------------------------------------
    $('.section-title').click(function () {
        var parent = $(this).parent();
        parent.toggleClass('closed');
    });

    $('#restore-theme-settings').live('click', function () {
        if (confirm("Are you sure you want to reset the values for these settings?")) {
            $(':input', '#theme-settings-form').each(function () {
                var tagName = this.type.toLowerCase();
                if (tagName != 'hidden') {
                    var defaultValue = $(this).attr('data-default-value');
                    if (defaultValue != undefined) {
                        if (tagName.indexOf('text') >= 0) {
                            $(this).val(defaultValue);
                        } else if (tagName == 'checkbox') {
                            if (defaultValue == 'on') {
                                $(this).attr('checked', 'checked');
                            } else {
                                $(this).attr('checked', '');
                            }
                        } else if (tagName.indexOf('select') >= 0) {
                            $(this).val(defaultValue);
                        }
                    }
                }
            });
            return true;
        } else {
            return false;
        }
    });

    $('.upload-button').live('click', function () {
        var id = $(this).attr('id');
        var fieldId = id.substr('upload_'.length, id.length - 'upload_'.length);

        tb_show('', 'media-upload.php?type=image&post_id=0&TB_iframe=true&flash=1');
        $('iframe#TB_iframeContent').load(function () {
            $('#TB_iframeContent').contents().find('.savesend .button').val('Use This Image');
        });

        window.send_to_editor = function (html) {
            console.log('aaaa ' + html);
            var x1 = html.indexOf("src=\"") + 5;
            var x2 = html.indexOf("\"", x1);
            var imageURL = html.substr(x1, x2 - x1);
            $('#' + fieldId).attr('value', imageURL);
            tb_remove();
        };

        return false;
    });

    $('.color-selector').each(function () {
        var bgEl = $(this).children('div');
        var inputEl = $(this).next('input');
        var initialColor = inputEl.attr('value');

        $(this).ColorPicker({
            color:initialColor,
            onShow:function (colpkr) {
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide:function (colpkr) {
                $(colpkr).fadeOut(500);
                return false;
            },
            onChange:function (hsb, hex, rgb) {
                bgEl.css('backgroundColor', '#' + hex);
                inputEl.attr('value', '#' + hex);
            }
        });
    });


    $('.predefined-colors-selector').each(function () {
        var invoker = this;
        var selectEl = $(invoker).next('select');
        var bgEl = $(invoker).children('div');

        var initialColor = selectEl.val();
        $(this).ColorPicker({
            color:initialColor,
            onShow:function (colpkr) {
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide:function (colpkr) {
                $(colpkr).fadeOut(500);
                return false;
            },
            onChange:function (hsb, hex, rgb) {
                changeBgColor(hex, true);
            }
        });

        selectEl.bind('change', function () {
            changeBgColor(this.value, false);
        });

        function changeBgColor(hex, add) {
            if (hex.length > 0) {
                hex = hex.indexOf("#") < 0 ? '#' + hex : hex;
                bgEl.css('backgroundColor', hex);
            } else {
                bgEl.css('backgroundColor', '');
            }
            if (add) {
                selectEl.find("option").each(function () {
                    if (this.value == hex) {
                        add = false;
                        $(this).attr("selected", "selected");
                    } else if ($(this).data("custom-color")) {
                        add = false;
                        $(this).val(hex);
                        $(this).text(hex);
                        $(this).attr("selected", "selected");
                    }
                });
                if (add) {
                    selectEl.append('<option value="' + hex + '" selected="selected" data-custom-color="true">' + hex + '</option>');
                }
            }
        }
    });

    $('.predefined-patterns-selector').bind('click', function () {
        var id = $(this).attr('id');
        var fieldId = id.substr('upload_'.length, id.length - 'upload_'.length);

        tb_show('', 'media-upload.php?type=image&amp;post_id=0&amp;TB_iframe=true&amp;flash=1');
        $('iframe#TB_iframeContent').load(function () {
            $('#TB_iframeContent').contents().find('.savesend .button').val('Use This Image');
        });

        window.send_to_editor = function (html) {
            var selectEl = $('#' + fieldId);
            var x1 = html.indexOf("src=\"") + 5;
            var x2 = html.indexOf("\"", x1);
            var imageURL = html.substr(x1, x2 - x1);

            var add = true;
            selectEl.find("> option").each(function () {
                if (this.value == imageURL) {
                    add = false;
                    $(this).attr("selected", "selected");
                }
            });
            if (add) {
                selectEl.append('<option value="' + imageURL + '" selected="selected">' + imageURL + '</option>');
            }
            tb_remove();
        };

        return false;
    });

    //--------------------------------------------- Sidebar Manager ----------------------------------------------------
    $('#sbm-add-form-submit').live('click', function () {
        return doAddOrUpdate($(this), '#sbm-add-form', '#sidebar-list');
    });

    $('.sbm-delete').live('click', function () {
        return doDelete($(this), '#sidebar-list');
    });

    //----------------------------------------------- Font Manager -----------------------------------------------------
    $('#font-manager-form-submit').live('click', function () {
        return doAddOrUpdate($(this), '#font-manager-form', '#font-list');
    });

    $('.font-manager-delete').live('click', function () {
        return doDelete($(this), '#font-list');
    });

    //------------------------------------------- Translation Manager --------------------------------------------------
    $('#tm-add-locale-form-submit').live('click', function () {
        return $('#tm-add-locale-form').valid();
    });
    $('#tm-update-locale-form-submit').live('click', function () {
        return doAddOrUpdate($(this), '#tm-update-locale-form', '#locale-list');
    });
    $('.tm-delete-locale').live('click', function () {
        return doDelete($(this), '#locale-list');
    });

    $('#tm-add-msg-form-submit').live('click', function () {
        return $('#tm-add-msg-form').valid();
    });
    $('#tm-update-msg-form-submit').live('click', function () {
        return doAddOrUpdate($(this), '#tm-update-msg-form', '#msg-list');
    });

    $('.tm-delete-msg').live('click', function () {
        if (confirm("Are you sure you want to delete this message?")) {
            var trId = $(this).data('tr-id');
            var key = $(this).data('key');

            var params = {
                'action':'tm-remove-msg',
                'key':key
            };
            AdminUtil.doPost(params, 'html', function (response) {
                $('#' + trId).remove();
            }, function (errorThrown) {
            });
        }
        return false;
    });

    $('#tm-update-all-messages').live('click', function () {
        var valid = true;
        $('.tm-msg').each(function () {
            if($.trim($(this).val()).length == 0){
                valid = false;
                $(this).css('border', '1px solid #ff0000');
            }
            var keyId = $(this).data('key-id');
            var locale = $(this).data('locale');
            var keyVal = $('#' + keyId).val();
            $(this).attr('name', locale + '-' + keyVal);
        });

        if(valid){
            return true;
        }else{
            alert('One or several messages are empty. Please fill in all fields.');
            return false;
        }
    });

    $('#tm-delete-all-messages').live('click', function () {
        if (confirm("Are you sure you want to delete all messages?")) {
            $('#tm-action').val('tm-remove-all-msg');
            return true;
        }
        return false;
    });

    $('#tm-import-db-messages').live('click', function () {
        if (confirm("Are you sure you want to import all messages from the database?\nThe translation .ini files will be contains only the messages from the database.")) {
            $('#tm-action').val('tm-import-db-msg');
            return true;
        }
        return false;
    });

    //------------------------------------------- Newsletter Manager ---------------------------------------------------

    $('#newsletter-manager-form-submit').live('click', function () {
        var emails = $('input:checkbox[name=emails[]]:checked');
        var doSubmit = false;

        if (AdminUtil.isRequiredFieldValid('newsletter-subject') &&
            AdminUtil.isRequiredFieldValid('newsletter-template')) {

            if (emails.length == 0) {
                alert('No email address was selected.');
            } else {
                doSubmit = true;
            }
        }
        return doSubmit;
    });

    $('.newsletter-manager-remove-email').live('click', function () {
        if (confirm("Remove this email address?")) {
            var uuid = $(this).attr('href');

            var params = {
                'action':'newsletter-manager-remove-email',
                'uuid':uuid
            };

            AdminUtil.doPost(params, 'html', function (response) {
                $('#newsletter-emails-list').html(response);

            }, function (errorThrown) {
                alert(errorThrown);
            });
        }
        return false;
    });

    //------------------------------------------ Import Data Manager ---------------------------------------------------

    $('#import-demo-submit').live('click', function () {
        if (AdminUtil.isRequiredFieldValid('website-url') &&
            AdminUtil.isRequiredFieldValid('email-address')) {
            var button = this;
            if (confirm("Are you sure you want to install the demo content? \nThe entire database will be deleted and replaced with another one.")) {
                var params = {
                    'nonce':$('#import-demo-nonce').val(),
                    'action':$('#import-demo-action').val(),
                    'email-address':$('#email-address').val(),
                    'website-url':$('#website-url').val()
                };
                var successMessage = $('#success-message');
                var errorMessage = $('#error-message');
                var waitMessage = $('#wait-message');
                waitMessage.show();
                $(button).attr('disabled', 'disabled');
                AdminUtil.doPost(params, 'html', function (response) {
                    waitMessage.hide();
                    successMessage.show();
                }, function (errorThrown) {
                    $(button).attr('disabled', '');
                    waitMessage.hide();
                    errorMessage.show();
                });
            }
        }
        return false;
    });

});
