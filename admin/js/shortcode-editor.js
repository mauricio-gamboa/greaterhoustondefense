var ShortCodeUtil = {

    addToEditor:function (sc) {
        var ed;
        if (typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden()) {
            ed.focus();
            if (tinymce.isIE) {
                ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);
            }
            ed.execCommand('mceInsertContent', false, sc);
        } else if (typeof edInsertContent == 'function') {
            edInsertContent(edCanvas, sc);
        } else {
            jQuery(edCanvas).val(jQuery(edCanvas).val() + sc);
        }
        tb_remove();
    },

    replaceParagraphsWithShortCode:function (value) {

        while (value.indexOf('<p>') >= 0) {
            value = value.replace('<p>', '[p]');
        }
        while (value.indexOf('<P>') >= 0) {
            value = value.replace('<P>', '[p]');
        }

        while (value.indexOf('</p>') >= 0) {
            value = value.replace('</p>', '[/p]');
        }
        while (value.indexOf('</P>') >= 0) {
            value = value.replace('</P>', '[/p]');
        }

        return value;
    },

    getShortCode:function (formId) {
        var scName = jQuery('#' + formId).data('sc');
        var content = '';
        var attributes = '';
        jQuery('#' + formId + " :input").each(function () {
            var field = jQuery(this);
            var isHiddenField = field.attr('type') == 'hidden';
            if (isHiddenField || field.is(":visible")) {
                var attrType = field.data('attr-type');
                if (attrType) {
                    if (attrType == 'content') {
                        content = field.val().replace('\n', '');
                    } else {
                        var attrName = field.data('attr-name');
                        var val = field.val();
                        if (field.is(':checkbox')) {
                            val = field.is(':checked') ? 'true' : 'false';
                        }
                        if (val.length > 0) {
                            attributes += ' ' + attrName + '="' + val + '"';
                        }
                    }
                }
            }
        });
        return '[' + scName + attributes + ']' + content + '[/' + scName + ']';
    },

    resetForm:function (formId) {
        jQuery('#' + formId + " :input").each(function () {
            var field = jQuery(this);
            var tagName = field.prop("nodeName").toLowerCase();
            if (tagName == 'select') {
                field.prop('selectedIndex', 0);
            } else {
                if (field.is(':checkbox')) {
                    field.attr("checked", field.prop("defaultChecked"));
                } else {
                    var defaultValue = field.prop("defaultValue");
                    if (defaultValue) {
                        field.val(defaultValue);
                    } else {
                        field.val('');
                    }
                }
            }
        });
    }

};

(function ($) {

    $.fn.mediaLibraryImageSelector = function () {
        var selectUploadedImgSrc = $(this);
        var baseId = selectUploadedImgSrc.data('base-id');
        var radioUploadedTypeSourceId = baseId + 'uploaded-type-source';
        var selectUploadedImgSrcId = baseId + 'uploaded-img-src';
        var checkUploadedSizeSrcId = baseId + 'uploaded-size-src';
        var radioNewTypeSourceId = baseId + 'new-type-source';
        var textNewImgSrcId = baseId + 'new-img-src';
        var textNewThumbSrcId = baseId + 'new-thumb-src';
        var imgUploadedImagePreviewId = baseId + 'preview';

        var radioUploadedTypeSource = $('#' + radioUploadedTypeSourceId);
        var selectUploadedSizeSrc = $('#' + checkUploadedSizeSrcId);
        var radioNewTypeSource = $('#' + radioNewTypeSourceId);
        var textNewImgSrc = $('#' + textNewImgSrcId);
        var textNewThumbSrc = $('#' + textNewThumbSrcId);
        var imgUploadedImagePreview = $('#' + imgUploadedImagePreviewId);

        previewSelectedImage();

        radioUploadedTypeSource.bind('click', function () {
            toggleElements();
            previewSelectedImage();
        });
        radioNewTypeSource.bind('click', function () {
            toggleElements();
            clearImagePreview();
        });
        selectUploadedImgSrc.bind('change', function () {
            previewSelectedImage();
        });

        function toggleElements() {
            selectUploadedImgSrc.parent().toggle();
            if (selectUploadedSizeSrc) {
                selectUploadedSizeSrc.parent().toggle();
            }
            textNewImgSrc.parent().toggle();
            textNewThumbSrc.parent().toggle();
        }

        function clearImagePreview() {
            imgUploadedImagePreview.attr('src', '');
        }

        function previewSelectedImage() {
            $('#' + selectUploadedImgSrcId + ' option:selected').each(function () {
                var imgSrc = $(this).data('src');
                if (imgSrc) {
                    imgUploadedImagePreview.attr('src', imgSrc);
                } else {
                    imgUploadedImagePreview.attr('src', '');
                }
            });
        }

    };

})(jQuery);

jQuery(document).ready(function ($) {

    $('#shortcode-editor-button-id').live('click', function () {
        var width = 840;
        var height = 400;
        var tbWindowWidth = width + 30;
        var tbWindowHeight = height + 30;
        var t = this.title;
        var a = this.href + '&width=' + width + '&height=' + height + '';
        var g = this.rel || false;
        var old_tb_init = window.tb_init;
        window.tb_init = function (domChunk) {
            old_tb_init(domChunk);
            $("#TB_window").css({marginLeft:'-' + parseInt((tbWindowWidth / 2), 10) + 'px', width:tbWindowWidth + 'px'});
            window.tb_init = old_tb_init;
            afterThickBoxContentLoad();
        };
        tb_show(t, a, g);
        this.blur();
        return false;
    });

    function afterThickBoxContentLoad() {
        var windowTitleHeight = $("#TB_title").height();
        var windowHeight = $("#TB_window").height();
        var windowContentHeight = windowHeight - windowTitleHeight - 20;
        $('#TB_ajaxContent').height(windowContentHeight);
        $(".tabs").tabs();
        $(".accordion").accordion({
            header:'.accordion-title',
            active:false,
            animate:false,
            collapsible:true,
            autoHeight:false
        });
        $(".color-thumbs").each(function () {
            $(this).colorRadioButtons();
        });
        $(".image-selector").each(function () {
            $(this).mediaLibraryImageSelector();
        });
        $(".sortable-slides").each(function () {
            $(this).sortable();
        });
        initDialogs();
    }

    function initDialogs() {
        $("#sc-add-li-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-add-li-form-submit').live('click', function () {
                    if ($('#sc-add-li-form').valid()) {
                        var itemEl = $('#sc-li-item');
                        var contentEl = $('#sc-li-content');
                        var item = $.trim(itemEl.val());
                        var content = $.trim(contentEl.val());

                        if (item.length > 0) {
                            if (content.length > 0) {
                                content = content + '|\n' + item;
                            } else {
                                content = item;
                            }
                        }
                        itemEl.val('');
                        contentEl.val(content);
                        ShortCodeUtil.resetForm('sc-add-li-form');
                        $("#sc-add-li-dialog").dialog("close");
                    }
                    return false;
                });
                $('#sc-add-li-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-add-li-form');
                    $("#sc-add-li-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-add-tab-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-add-tab-form-submit').live('click', function () {
                    if ($('#sc-add-tab-form').valid()) {
                        var sc = ShortCodeUtil.getShortCode('sc-add-tab-form');
                        var globalContentEl = $('#sc-tabs-content');
                        var globalContent = globalContentEl.val();

                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }

                        ShortCodeUtil.resetForm('sc-add-tab-form');
                        globalContentEl.val(globalContent);
                        $("#sc-add-tab-dialog").dialog("close");
                    }
                    return false;
                });
                $('#sc-add-tab-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-add-tab-form');
                    $("#sc-add-tab-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-add-toggle-tab-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-add-toggle-tab-form-submit').live('click', function () {
                    if ($('#sc-add-toggle-tab-form').valid()) {
                        var sc = ShortCodeUtil.getShortCode('sc-add-toggle-tab-form');
                        var globalContentEl = $('#sc-toggles-content');
                        var globalContent = globalContentEl.val();

                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        ShortCodeUtil.resetForm('sc-add-toggle-tab-form');
                        globalContentEl.val(globalContent);
                        $("#sc-add-toggle-tab-dialog").dialog("close");
                    }

                    return false;
                });
                $('#sc-add-toggle-tab-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-add-toggle-tab-form');
                    $("#sc-add-toggle-tab-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-imgg-slide-dialog").dialog({
            autoOpen:false,
            width:660,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-imgg-slide-form-submit').live('click', function () {
                    if ($('#sc-imgg-slide-form').valid()) {
                        var imgSrc = '';
                        var imgName = '';
                        var thumb = '';
                        var newImgSrcEl = $('#sc-imgg-slide-src-new-img-src');
                        var newThumbSrcEl = $('#sc-imgg-slide-src-new-thumb-src');
                        var titleEl = $('#sc-imgg-slide-title');
                        var title = titleEl.val();

                        if ($('#sc-imgg-slide-src-uploaded-type-source').is(':checked')) {
                            $('#sc-imgg-slide-src-uploaded-img-src').find('option:selected').each(function () {
                                imgSrc = $(this).data('src');
                                imgName = $(this).val();
                            });
                        } else {
                            imgSrc = imgName = newImgSrcEl.val();
                            thumb = newThumbSrcEl.val();
                        }

                        imgName = ' data-img="' + imgName + '"';
                        thumb = thumb.length > 0 ? ' data-thumb="' + thumb + '"' : '';
                        title = title.length > 0 ? ' data-title="' + title + '"' : '';

                        var content = '<li' + imgName + thumb + title + '>';
                        content += '<div><img alt="" src="' + imgSrc + '"></div>';
                        content += '<a href="#" class="delete-gallery-item">Delete</a>';
                        content += '</li>';

                        $('#sc-imgg-slides').append(content);
                        titleEl.val('');
                        newImgSrcEl.val('');
                        newThumbSrcEl.val('');
                        $("#sc-imgg-slide-dialog").dialog("close");
                    }
                    return false;
                });
                $('#sc-imgg-slide-form-cancel').live('click', function () {
                    var newImgSrcEl = $('#sc-imgg-slide-src-new-img-src');
                    var newThumbSrcEl = $('#sc-imgg-slide-src-new-thumb-src');
                    var titleEl = $('#sc-imgg-slide-title');
                    var altEl = $('#sc-imgg-slide-alt');
                    titleEl.val('');
                    altEl.val('');
                    newImgSrcEl.val('');
                    newThumbSrcEl.val('');
                    $("#sc-imgg-slide-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-video-source-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-video-source-form-submit').live('click', function () {
                    if ($('#sc-video-source-form').valid()) {
                        var formatEl = $('#sc-video-source-format');
                        var srcEl = $('#sc-video-source-src');
                        var mainContentEl = $('#sc-video-content');
                        var format = formatEl.val();
                        var src = srcEl.val();
                        var globalContent = mainContentEl.val();

                        format = ' format="' + format + '"';
                        src = ' src="' + src + '"';
                        if (globalContent.indexOf(format) < 0) {
                            var sc = '[video_source' + format + src + '][/video_source]';
                            if ($.trim(globalContent).length > 0) {
                                globalContent = globalContent + '\n' + sc;
                            } else {
                                globalContent = sc;
                            }
                            srcEl.val('');
                            mainContentEl.val(globalContent);
                            ShortCodeUtil.resetForm('sc-video-source-form');
                            $("#sc-video-source-dialog").dialog("close");
                        } else {
                            alert('A video source with this format already exists.');
                        }
                    }
                    return false;
                });

                $('#sc-video-source-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-video-source-form');
                    $("#sc-video-source-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-video-track-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-video-track-form-submit').live('click', function () {
                    if ($('#sc-video-track-form').valid()) {
                        var typeEl = $('#sc-video-track-type');
                        var srcEl = $('#sc-video-track-src');
                        var langEl = $('#sc-video-track-lang');
                        var mainContentEl = $('#sc-video-content');
                        var type = typeEl.val();
                        var src = srcEl.val();
                        var lang = langEl.val();
                        var globalContent = mainContentEl.val();

                        type = ' type="' + type + '"';
                        src = ' src="' + src + '"';
                        lang = ' lang="' + lang + '"';
                        var sc = '[video_track' + type + src + lang + '][/video_track]';
                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        srcEl.val('');
                        langEl.val('en');
                        mainContentEl.val(globalContent);
                        ShortCodeUtil.resetForm('sc-video-track-form');
                        $("#sc-video-track-dialog").dialog("close");
                    }
                    return false;
                });

                $('#sc-video-track-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-video-track-form');
                    $("#sc-video-track-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-audio-source-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-audio-source-form-submit').live('click', function () {
                    if ($('#sc-audio-source-form').valid()) {
                        var formatEl = $('#sc-audio-source-format');
                        var srcEl = $('#sc-audio-source-src');
                        var mainContentEl = $('#sc-audio-content');
                        var format = formatEl.val();
                        var src = srcEl.val();
                        var globalContent = mainContentEl.val();

                        format = ' format="' + format + '"';
                        src = ' src="' + src + '"';
                        if (globalContent.indexOf(format) < 0) {
                            var sc = '[audio_source' + format + src + '][/audio_source]';
                            if ($.trim(globalContent).length > 0) {
                                globalContent = globalContent + '\n' + sc;
                            } else {
                                globalContent = sc;
                            }
                            srcEl.val('');
                            mainContentEl.val(globalContent);
                            ShortCodeUtil.resetForm('sc-audio-source-form');
                            $("#sc-audio-source-dialog").dialog("close");
                        } else {
                            alert('An audio source with this format already exists.');
                        }
                    }
                    return false;
                });

                $('#sc-audio-source-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-audio-source-form');
                    $("#sc-audio-source-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-add-gc-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-add-gc-form-submit').live('click', function () {
                    if ($('#sc-add-gc-form').valid()) {
                        var columnContentEl = $('#sc-gc-col');
                        var globalContentEl = $('#sc-gc-content');
                        var columnContent = columnContentEl.val();
                        var globalContent = globalContentEl.val();

                        if ($.trim(columnContent).length > 0) {
                            if ($.trim(globalContent).length > 0) {
                                globalContent = globalContent + '|\n' + columnContent;
                            } else {
                                globalContent = columnContent;
                            }
                        }
                        columnContentEl.val('');
                        globalContentEl.val(globalContent);
                        ShortCodeUtil.resetForm('sc-add-gc-form');
                        $("#sc-add-gc-dialog").dialog("close");
                    }
                    return false;
                });

                $('#sc-add-gc-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-add-gc-form');
                    $("#sc-add-gc-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-form-input-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-form-input-form-submit').live('click', function () {
                    if ($('#sc-form-input-form').valid()) {
                        var globalContentEl = $('#sc-form-content');
                        var globalContent = globalContentEl.val();

                        var sc = ShortCodeUtil.getShortCode('sc-form-input-form');
                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        globalContentEl.val(globalContent);
                        ShortCodeUtil.resetForm('sc-form-input-form');
                        $("#sc-form-input-dialog").dialog("close");
                    }
                    return false;
                });

                $('#sc-form-input-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-form-input-form');
                    $("#sc-form-input-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-add-ib-carousel-item-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-add-ib-carousel-item-form-submit').live('click', function () {
                    if ($('#sc-add-ib-carousel-item-form').valid()) {
                        var globalContentEl = $('#sc-ib-carousel-content');
                        var globalContent = globalContentEl.val();

                        var sc = ShortCodeUtil.getShortCode('sc-add-ib-carousel-item-form');
                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        globalContentEl.val(globalContent);
                        ShortCodeUtil.resetForm('sc-add-ib-carousel-item-form');
                        $("#sc-add-ib-carousel-item-dialog").dialog("close");
                    }
                    return false;
                });

                $('#sc-add-ib-carousel-item-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-add-ib-carousel-item-form');
                    $("#sc-add-ib-carousel-item-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-add-member-dialog").dialog({
            autoOpen:false,
            width:660,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-add-member-form-submit').live('click', function () {
                    if ($('#sc-add-member-form').valid()) {
                        var globalContentEl = $('#sc-team-content');
                        var globalContent = globalContentEl.val();

                        var sc = ShortCodeUtil.getShortCode('sc-add-member-form');
                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        globalContentEl.val(globalContent);
                        ShortCodeUtil.resetForm('sc-add-member-form');
                        $("#sc-add-member-dialog").dialog("close");
                    }
                    return false;
                });

                $('#sc-add-member-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-add-member-form');
                    $("#sc-add-member-dialog").dialog("close");
                    return false;
                })
            }
        });

        $("#sc-add-client-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            create:function (event, ui) {
                $('#sc-add-client-form-submit').live('click', function () {
                    if ($('#sc-add-client-form').valid()) {
                        var globalContentEl = $('#sc-clients-content');
                        var globalContent = globalContentEl.val();

                        var sc = ShortCodeUtil.getShortCode('sc-add-client-form');
                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        globalContentEl.val(globalContent);
                        ShortCodeUtil.resetForm('sc-add-client-form');
                        $("#sc-add-client-dialog").dialog("close");
                    }
                    return false;
                });

                $('#sc-add-client-form-cancel').live('click', function () {
                    ShortCodeUtil.resetForm('sc-add-client-form');
                    $("#sc-add-client-dialog").dialog("close");
                    return false;
                })
            }
        });
    }

    //---------------------------------------------------- BUTTON ------------------------------------------------------

    $('#sc-button-form-submit').live('click', function () {
        if ($('#sc-button-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-button-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //---------------------------------------------------- LIST --------------------------------------------------------

    $('#sc-list-form-submit').live('click', function () {
        if ($('#sc-list-form').valid()) {
            var content = $('#sc-li-content').val().replace('\n', '');
            var indent = $('#sc-li-indent').is(':checked');
            var tagName;
            var type;

            if ($('#sc-li-ol-type').is(':checked')) {
                tagName = 'ol';
                type = $('#sc-li-ol-icon').val();
            } else {
                tagName = 'ul';
                type = $('#sc-li-ul-icon').val();
            }
            indent = indent ? 'true' : 'false';
            var sc = '[' + tagName + ' type="' + type + '" indent="' + indent + '" separator="|"]' + content + '[/' + tagName + ']';
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $('.sc-li-type-selector').live('click', function () {
        $('#sc-li-ol-icon').toggle();
        $('#sc-li-ul-icon').toggle();
    });

    $('#sc-list-form-add').live('click', function () {
        $("#sc-add-li-dialog").dialog("open");
        return false;
    });

    //--------------------------------------------------- INFO BOX -----------------------------------------------------

    $('#sc-infobox-form-submit').live('click', function () {
        if ($('#sc-infobox-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-infobox-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //------------------------------------------------- ICON BOX -------------------------------------------------------

    $('#sc-iconbox-form-submit').live('click', function () {
        if ($('#sc-iconbox-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-iconbox-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //--------------------------------------------------- IMAGE --------------------------------------------------------

    $('#sc-img-form-submit').live('click', function () {
        if ($('#sc-img-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-img-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //----------------------------------------------- IMAGE GALLERY ----------------------------------------------------

    $('#sc-imgg-form-submit').live('click', function () {
        if ($('#sc-imgg-form').valid()) {
            var slide = $('#sc-imgg-slidecap').is(':checked');
            var name = $('#sc-imgg-name').val();
            var caption = $('#sc-imgg-caption').val();
            var size = $('#sc-imgg-size').val();
            var lightbox = $('#sc-imgg-lightbox').is(':checked');
            var align = $('#sc-imgg-align').val();

            name = name.length > 0 ? ' name="' + name + '"' : '';
            size = ' size="' + size + '"';
            if (slide) {
                caption = '';
                align = '';
            } else {
                caption = caption.length > 0 ? ' caption="' + caption + '"' : '';
                align = align.length > 0 ? ' align="' + align + '"' : '';
            }
            slide = slide ? ' slide="true"' : ' slide="false"';
            lightbox = lightbox ? ' lightbox="true"' : ' lightbox="false"';

            var sc = '[gallery' + slide + name + caption + size + lightbox + align + ']';
            $('#sc-imgg-slides').find('li').each(function () {
                var $this = $(this);
                var imgSrc = $this.data('img');
                var thumbSrc = '';
                var title = '';

                if ($this.data('thumb')) {
                    thumbSrc = ' thumb_src="' + $this.data('thumb') + '"';
                }
                if ($this.data('title')) {
                    title = ' title="' + $this.data('title') + '"';
                }
                sc += '[gallery_item src="' + imgSrc + '"' + thumbSrc + title + ']';
            });
            sc += '[/gallery]';
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $('#sc-imgg-slidecap').live('change', function () {
        var captionDivEl = $('#sc-imgg-caption').parent();
        var alignDivEl = $('#sc-imgg-align').parent();
        if ($(this).is(":checked")) {
            captionDivEl.hide();
            alignDivEl.hide();
        } else {
            captionDivEl.show();
            alignDivEl.show();
        }
    });

    $('#sc-imgg-form-add').live('click', function () {
        $('#sc-imgg-slide-dialog').dialog('open');
        return false;
    });

    $('.delete-gallery-item').live('click', function () {
        var slide = $(this).parent();
        slide.fadeOut(function () {
            $(this).remove();
        });
        return false;
    });

    //---------------------------------------------- EMBEDDED VIDEO ----------------------------------------------------

    $('#sc-evideo-form-submit').live('click', function () {
        if ($('#sc-evideo-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-evideo-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //-------------------------------------------- SELF-HOSTED VIDEO ---------------------------------------------------

    $('#sc-video-form-submit').live('click', function () {
        if ($('#sc-video-form').valid()) {
            var width = $('#sc-video-width').val();
            var height = $('#sc-video-height').val();
            var poster = $('#sc-video-poster').val();
            var isThumb = $('#sc-video-thumb').is(':checked');
            var content = $('#sc-video-content').val().replace('\n', '');
            if (content.indexOf('[video_source') >= 0) {
                width = ' width="' + width + '"';
                height = ' height="' + height + '"';
                poster = ' poster="' + poster + '"';
                var thumbnail = '';
                if (isThumb) {
                    thumbnail = ' is_thumb="true"';
                }
                var sc = '[video' + width + height + poster + thumbnail + ']' + content + '[/video]';
                ShortCodeUtil.addToEditor(sc);
            } else {
                alert('You should add at least one video source.');
            }
        }
        return false;
    });

    $('#sc-video-form-add-src').live('click', function () {
        $('#sc-video-source-dialog').dialog('open');
        return false;
    });

    $('#sc-video-form-add-track').live('click', function () {
        $('#sc-video-track-dialog').dialog('open');
        return false;
    });

    //-------------------------------------------------- AUDIO ---------------------------------------------------------

    $('#sc-audio-form-submit').live('click', function () {
        if ($('#sc-audio-form').valid()) {
            var width = $('#sc-audio-width').val();
            var isThumb = $('#sc-audio-thumb').is(':checked');
            var content = $('#sc-audio-content').val().replace('\n', '');
            if (content.indexOf('[audio_source') >= 0) {
                width = ' width="' + width + '"';
                var thumbnail = '';
                if (isThumb) {
                    thumbnail = ' is_thumb="true"';
                }
                var sc = '[audio' + width + thumbnail + ']' + content + '[/audio]';
                ShortCodeUtil.addToEditor(sc);
            } else {
                alert('You should add at least one audio source.');
            }
        }
        return false;
    });

    $('#sc-audio-form-add-src').live('click', function () {
        $('#sc-audio-source-dialog').dialog('open');
        return false;
    });

    //-------------------------------------------------- DROPCAPS ------------------------------------------------------

    $('#sc-dropcap-form-submit').live('click', function () {
        if ($('#sc-dropcap-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-dropcap-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //------------------------------------------------- HIGHLIGHT ------------------------------------------------------

    $('#sc-highlight-form-submit').live('click', function () {
        if ($('#sc-highlight-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-highlight-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //--------------------------------------------- PREFORMATTED TEXT --------------------------------------------------

    $('#sc-pre-form-submit').live('click', function () {
        if ($('#sc-pre-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-pre-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //------------------------------------------------ BLOCK QUOTE -----------------------------------------------------

    $('#sc-bq-form-submit').live('click', function () {
        if ($('#sc-bq-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-bq-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //----------------------------------------------- GRID COLUMNS -----------------------------------------------------

    $('#sc-gc-form-submit').live('click', function () {
        if ($('#sc-gc-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-gc-form');
            sc = ShortCodeUtil.replaceParagraphsWithShortCode(sc);
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $('#sc-gc-form-add').live('click', function () {
        $('#sc-add-gc-dialog').dialog('open');
        return false;
    });

    //----------------------------------------------------- TABS -------------------------------------------------------

    $('#sc-tabs-form-submit').live('click', function () {
        if ($('#sc-tabs-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-tabs-form');
            sc = ShortCodeUtil.replaceParagraphsWithShortCode(sc);
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $('#sc-tabs-form-add').live('click', function () {
        $('#sc-add-tab-dialog').dialog('open');
        return false;
    });

    //--------------------------------------------------- TOGGLES ------------------------------------------------------

    $('#sc-toggle-form-submit').live('click', function () {
        if ($('#sc-toggle-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-toggle-form');
            sc = ShortCodeUtil.replaceParagraphsWithShortCode(sc);
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $('#sc-toggle-form-add').live('click', function () {
        $('#sc-add-toggle-tab-dialog').dialog('open');
        return false;
    });

    //--------------------------------------------- NOTIFICATION BOX ---------------------------------------------------

    $('#sc-notifbox-form-submit').live('click', function () {
        if ($('#sc-notifbox-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-notifbox-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //---------------------------------------------- POST CAROUSEL -----------------------------------------------------

    $('#sc-post-carousel-form-submit').live('click', function () {
        if ($('#sc-post-carousel-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-post-carousel-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //-------------------------------------------- ICON-BOX CAROUSEL ---------------------------------------------------

    $('#sc-ib-carousel-genmode').live('change', function () {
        var postidDiv = $('#sc-ib-carousel-postid').parent();
        var contentDiv = $('#sc-ib-carousel-content').parent();
        var addBtn = $('#sc-ib-carousel-form-add');
        if ($(this).val() == 'a') {
            postidDiv.show();
            contentDiv.hide();
            addBtn.hide();
        } else {
            postidDiv.hide();
            contentDiv.show();
            addBtn.show();
        }
        return false;
    });

    $('#sc-ib-carousel-form-submit').live('click', function () {
        if ($('#sc-ib-carousel-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-ib-carousel-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $('#sc-ib-carousel-form-add').live('click', function () {
        $('#sc-add-ib-carousel-item-dialog').dialog('open');
        return false;
    });

    //------------------------------------------ TESTIMONIALS CAROUSEL -------------------------------------------------

    $('#sc-testimonials-carousel-form-submit').live('click', function () {
        if ($('#sc-testimonials-carousel-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-testimonials-carousel-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //------------------------------------------------- SITE MAP -------------------------------------------------------

    $('#sc-sm-form-submit').live('click', function () {
        if ($('#sc-sm-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-sm-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //--------------------------------------------------- FORM ---------------------------------------------------------

    $('#sc-form-form-submit').live('click', function () {
        if ($('#sc-form-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-form-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $('#sc-form-form-add').live('click', function () {
        $('#sc-form-input-dialog').dialog('open');
        return false;
    });

    //------------------------------------------ NEWSLETTER SUBSCRIPTION -----------------------------------------------

    $('#sc-nls-form-submit').live('click', function () {
        if ($('#sc-nls-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-nls-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //------------------------------------------------ TEAM MEMBERS ----------------------------------------------------

    $('#sc-team-form-submit').live('click', function () {
        if ($('#sc-team-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-team-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $('#sc-team-form-add').live('click', function () {
        $('#sc-add-member-dialog').dialog('open');
        return false;
    });

    //-------------------------------------------------- CLIENTS -------------------------------------------------------

    $('#sc-clients-form-submit').live('click', function () {
        if ($('#sc-clients-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-clients-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $('#sc-clients-form-add').live('click', function () {
        $('#sc-add-client-dialog').dialog('open');
        return false;
    });

    //-------------------------------------------------- TABLE ---------------------------------------------------------

    $('#sc-table-form-submit').live('click', function () {
        if ($('#sc-table-form').valid()) {
            var caption = $('#sc-table-caption').val();
            var footer = $('#sc-table-footer').val();
            var separator = $('#sc-table-separator').val();
            var columns = $('#sc-table-columns').val();
            var rows = $('#sc-table-rows').val();

            if (columns > 0 && rows > 0) {
                caption = caption.length > 0 ? ' caption="' + caption + '"' : '';
                footer = footer.length > 0 ? ' footer="' + footer + '"' : '';
                var sc = '[table' + caption + footer + ']';
                sc += '\n[table_header]';
                for (var c = 0; c < columns; c++) {
                    if (c > 0) {
                        sc += separator;
                    }
                    sc += 'HEADER-' + c;
                }
                sc += '[/table_header]';
                for (var r = 0; r < rows; r++) {
                    sc += '\n[table_row]';
                    for (c = 0; c < columns; c++) {
                        if (c > 0) {
                            sc += separator;
                        }
                        sc += 'ROW_' + r + '_COLUMN_' + c;
                    }
                    sc += '[/table_row]';
                }
                sc += '\n[/table]';
                ShortCodeUtil.addToEditor(sc);
            } else {
                alert('The number of columns and the number of rows must be greater that zero.');
            }

        }
        return false;
    });

    //---------------------------------------------- PRICING BOXES -----------------------------------------------------

    $('#sc-pb-form-submit').live('click', function () {
        if ($('#sc-pb-form').valid()) {
            var columns = $('#sc-pb-columns').val();
            var rows = $('#sc-pb-rows').val();
            var hc = $('#sc-pb-hc').val();
            var separator = $('#sc-pb-separator').val();

            if (rows > 0) {
                if (hc >= 1 && hc <= columns) {
                    var sc = '[pricing_boxes columns="' + columns + '" highlighted_column="' + hc + '" separator="' + separator + '"]';
                    for (var c = 0; c < columns; c++) {
                        sc += '\n[pricing_box_column title="TITLE_COL_' + c + '" price="PRICE_(e.g. $20)" unit="UNIT_MEASURE_(e.g. month)" order_text="ORDER_TEXT" order_url="ORDER_URL"]\n';
                        for (var r = 0; r < rows; r++) {
                            if (r > 0) {
                                sc += separator;
                            }
                            sc += 'COL_' + c + '_ROW_' + r;
                        }
                        sc += '\n[/pricing_box_column]';
                    }
                    sc += '\n[/pricing_boxes]';
                    ShortCodeUtil.addToEditor(sc);
                } else {
                    alert('The highlighted column must be between 1 and ' + columns + '.');
                }
            } else {
                alert('The number of rows must be greater that zero.');
            }

        }
        return false;
    });

    //---------------------------------------------- PRICING TABLE -----------------------------------------------------

    $('#sc-pt-form-submit').live('click', function () {
        if ($('#sc-pt-form').valid()) {
            var columns = $('#sc-pt-columns').val();
            var rows = $('#sc-pt-rows').val();
            var hc = $('#sc-pt-hc').val();
            var separator = $('#sc-pt-separator').val();

            if (columns > 0 && rows > 0) {
                if (hc >= 1 && hc <= columns) {
                    var sc = '[pricing_table highlighted_column="' + hc + '" separator="' + separator + '"]\n';

                    for (var c = 0; c < columns; c++) {
                        sc += '[pricing_table_header price="PRICE_(e.g. $20)" unit="UNIT_MEASURE_(e.g. month)"]TITLE_COL_' + c + '[/pricing_table_header]\n';
                    }

                    for (c = 0; c < columns; c++) {
                        sc += '[pricing_table_footer order_text="ORDER_TEXT" order_url="ORDER_URL"][/pricing_table_footer]\n';
                    }

                    for (var r = 0; r < rows; r++) {
                        sc += '[pricing_table_row title="ROW_TITLE_(e.g. Payment Integration)"]\n';
                        for (c = 0; c < columns; c++) {
                            if (c > 0) {
                                sc += separator;
                            }
                            sc += 'ROW_' + r + '_COL_' + c;
                        }
                        sc += '\n[/pricing_table_row]\n';
                    }

                    sc += '[/pricing_table]';
                    ShortCodeUtil.addToEditor(sc);
                } else {
                    alert('The highlighted column must be between 1 and ' + columns + '.');
                }
            } else {
                alert('The number of columns and the number of rows must be greater that zero.');
            }

        }
        return false;
    });

});