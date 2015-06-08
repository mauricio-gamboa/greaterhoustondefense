<?php

class Image_Gallery_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    static $HREF_ATTR = "href";
    static $SRC_ATTR = "src";
    static $THUMB_SRC_ATTR = "thumb_src";
    static $SIZE_ATTR = "size";
    static $LIGHTBOX_ATTR = "lightbox";
    static $TITLE_ATTR = "title";
    static $SLIDE_ATTR = "slide";
    static $NAME_ATTR = "name";
    static $CAPTION_ATTR = "caption";
    static $ALIGN_ATTR = "align";
    var $item_index;
    var $items;

    private function init()
    {
        unset($this->items);
        $this->item_index = 1;
        $this->items = array();
    }

    function render($attr, $inner_content = null, $code = '')
    {
        $content = '';
        switch ($code) {
            case "gallery":
                $this->init();
                do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_gallery($attr);
                break;
            case "gallery_item":
                $this->process_gallery_item($attr);
                break;
        }
        return $content;
    }

    private function render_gallery($attr)
    {
        extract(shortcode_atts(array(Image_Gallery_Shortcode::$SLIDE_ATTR => 'false',
            Image_Gallery_Shortcode::$SIZE_ATTR => 'full',
            Image_Gallery_Shortcode::$LIGHTBOX_ATTR => 'false',
            Image_Gallery_Shortcode::$HREF_ATTR => '',
            Image_Gallery_Shortcode::$NAME_ATTR => '',
            Image_Gallery_Shortcode::$CAPTION_ATTR => '',
            Image_Gallery_Shortcode::$ALIGN_ATTR => ''), $attr));
        $gallery_name = strlen($name) == 0 ? uniqid('gallery-') : $name;

        $img_content = '';
        if ($slide == 'true') {
            foreach ($this->items as $i => $item) {
                $style = ($i == 0) ? '' : ' style="display:none;"';
                $img_content .= '<li' . $style . '>' . $item->get_image_link($gallery_name, $size, $lightbox == 'true', true, $href) . "</li>\n";
            }
            $content = "<div class=\"entry-slider\"><ul>$img_content</ul></div>";
        } else {
            foreach ($this->items as $item) {
                $img_content .= $item->get_image_link($gallery_name, $size, $lightbox == 'true', false, $href) . "\n";
            }
            if ($align === 'right') {
                $align_class_name = " float-right";
            } elseif ($align === 'left') {
                $align_class_name = " float-left";
            } else {
                $align_class_name = '';
            }

            if (strlen($caption) > 0) {
                $content = "<div class=\"caption" . $align_class_name . "\">";
                $content .= "<div class=\"entry-image\">$img_content</div>";
                $content .= "<p class=\"caption-text\">$caption</p>";
                $content .= "</div>";
            } else {
                $content = "<div class=\"entry-image" . $align_class_name . "\">$img_content</div>";
            }
        }
        return $content;
    }

    private function process_gallery_item($attr)
    {
        extract(shortcode_atts(array(Image_Gallery_Shortcode::$SRC_ATTR => '',
            Image_Gallery_Shortcode::$THUMB_SRC_ATTR => '',
            Image_Gallery_Shortcode::$TITLE_ATTR => ''), $attr));
        $title = ___($title);
        array_push($this->items, new Gallery_Item($this->item_index, $src, $thumb_src, $title));
        $this->item_index++;
    }

    function get_names()
    {
        return array('gallery', 'gallery_item');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-imgg-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-imgg-name">' . __('Gallery Name', 'finesse') . ':</label>';
        $content .= '<input id="sc-imgg-name" name="sc-imgg-name" type="text">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-imgg-size">' . __('Image Size', 'finesse') . '</label>';
        $content .= '<select id="sc-imgg-size" name="sc-imgg-size">';
        $content .= '<option value="full">' . __('Original Size', 'finesse') . '</option>';
        $content .= '<option value="thumb">' . __('Thumbnail Size', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="sc-imgg-lightbox" name="sc-imgg-lightbox" type="checkbox">';
        $content .= '<label for="sc-imgg-lightbox">' . __('Use Lightbox', 'finesse') . '</label>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="sc-imgg-slidecap" name="sc-imgg-slidecap" type="checkbox">';
        $content .= '<label for="sc-imgg-slidecap">' . __('Add slide capabilities', 'finesse') . '</label>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-imgg-caption">' . __('Caption', 'finesse') . ':</label>';
        $content .= '<input id="sc-imgg-caption" name="sc-imgg-caption" type="text">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-imgg-align">' . __('Align', 'finesse') . ':</label>';
        $content .= '<select id="sc-imgg-align" name="sc-imgg-align">';
        $content .= '<option value="" >' . __('None', 'finesse') . '</option>';
        $content .= '<option value="left">' . __('Left', 'finesse') . '</option>';
        $content .= '<option value="right">' . __('Right', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<ul id="sc-imgg-slides" class="slides sortable-slides"></ul>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-imgg-form-submit" type="submit" name="submit" value="' . __('Insert Image Gallery', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-imgg-form-add" type="submit" name="submit" value="' . __('Add New Image', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        $content .= '<div id="sc-imgg-slide-dialog" title="' . __('Add New Image', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-imgg-slide-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div class="image-tab-content">';
        $content .= '<div class="image-tab-content-left">';
        $content .= '<div class="radio-row">';
        $content .= '<span>';
        $content .= '<input id="sc-imgg-slide-src-uploaded-type-source" type="radio" name="sc-imgg-slide-src-type-source" value="uploaded" checked>';
        $content .= '<label for="sc-imgg-slide-src-uploaded-type-source">' . __('Uploaded Image', 'finesse') . '</label>';
        $content .= '</span>';
        $content .= '<span>';
        $content .= '<input id="sc-imgg-slide-src-new-type-source" type="radio" name="sc-imgg-slide-src-type-source" value="new">';
        $content .= '<label for="sc-imgg-slide-src-new-type-source">' . __('New Image', 'finesse') . '</label>';
        $content .= '</span>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-imgg-slide-src-uploaded-img-src">' . __('Image Name', 'finesse') . ':</label>';
        $content .= '<select id="sc-imgg-slide-src-uploaded-img-src" name="sc-imgg-slide-src-uploaded-img-src" class="required image-selector" data-base-id="sc-imgg-slide-src-">';
        $content .= '<option value="">' . __('Select Image ...', 'finesse') . '</option>';
        $images = Multimedia_Util::get_all_uploaded_images();
        foreach ($images as $img) {
            $images = wp_get_attachment_image_src($img->ID);
            $content .= '<option value="' . $img->post_title . '" data-src="' . $images[0] . '">' . $img->post_title . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div style="display: none">';
        $content .= '<label for="sc-imgg-slide-src-new-img-src">' . __('Image Src', 'finesse') . ':</label>';
        $content .= '<input id="sc-imgg-slide-src-new-img-src" name="sc-imgg-slide-src-new-img-src" type="text" class="required">';
        $content .= '</div>';
        $content .= '<div style="display: none">';
        $content .= '<label for="sc-imgg-slide-src-new-thumb-src">' . __('Thumbnail Src', 'finesse') . ':</label>';
        $content .= '<input id="sc-imgg-slide-src-new-thumb-src" name="sc-imgg-slide-src-new-thumb-src" type="text" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-imgg-slide-title">' . __('Title', 'finesse') . ':</label>';
        $content .= '<input id="sc-imgg-slide-title" name="sc-imgg-slide-title" type="text">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-imgg-slide-form-submit" type="submit" name="submit" value="' . __('Add Image to Gallery', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-imgg-slide-form-cancel" type="submit" name="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="image-tab-content-right">';
        $content .= '<img id="sc-imgg-slide-src-preview" src="#" alt="">';
        $content .= '</div>';

        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        $content .= '</div>';

        return $content;
    }

    function get_group_title()
    {
        return __('Multimedia', 'finesse');
    }

    function get_title()
    {
        return __('Image Gallery / Entry Slider', 'finesse');
    }

}

class Gallery_Item
{

    private $index;
    private $thumb_src;
    private $src;
    private $title;

    function __construct($index, $src, $thumb_src, $title)
    {
        $this->index = $index;
        $this->thumb_src = $thumb_src;
        $this->src = $src;
        $this->title = $title;
    }

    function get_image_link($gallery_name, $size, $is_lightbox, $use_slide, $href)
    {
        $attach_post = Multimedia_Util::get_original_post($this->src);
        if ($attach_post) {
            $orig_size_src = Multimedia_Util::get_post_image_src($attach_post);
            switch ($size) {
                case 'thumb':
                    $src = Multimedia_Util::get_post_thumbnail_image_src($attach_post);
                    break;
                case 'post':
                    $src = Multimedia_Util::get_post_thumbnail_image_src($attach_post, 'custom-blog-post-thumb');
                    break;
                case 'portfolio':
                    $src = Multimedia_Util::get_post_thumbnail_image_src($attach_post, 'custom-portfolio-post-thumb');
                    break;
                default:
                    $src = $orig_size_src;
                    break;
            }
        } else {
            $orig_size_src = Multimedia_Util::get_external_image_src($this->src);
            if (strlen($this->thumb_src) > 0) {
                $src = Multimedia_Util::get_external_image_src($this->thumb_src);
            } else {
                $src = $orig_size_src;
            }
        }

        if (isset($href) && !empty($href)) {
            return '<a href="' . $href . '" title="' . $this->title . '"><span class="overlay link"></span><img src="' . $src . '" alt=""></a>';
        } else {
            if ($use_slide) {
                if ($is_lightbox) {
                    return '<a class="fancybox" rel="' . $gallery_name . '" href="' . $orig_size_src . '" title="' . $this->title . '"><span class="overlay zoom"></span><img src="' . $src . '" alt=""></a>';
                } else {
                    return '<img src="' . $src . '" alt="">';
                }
            } else {
                $class_name = $this->index == 1 ? 'fancybox' : 'fancybox invisible';
                return '<a class="' . $class_name . '" rel="' . $gallery_name . '" href="' . $orig_size_src . '" title="' . $this->title . '"><span class="overlay zoom"></span><img src="' . $src . '" alt=""></a>';
            }
        }

    }

}

