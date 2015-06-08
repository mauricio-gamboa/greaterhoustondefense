<?php

class Translation_Shortcode extends Abstract_Finesse_Shortcode
{

    function render($attr, $inner_content = null, $code = '')
    {
        extract(shortcode_atts(array('key' => ''), $attr));
        return ___($key);
    }

    function get_names()
    {
        return 'msg';
    }

}