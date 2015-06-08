<?php

abstract class Abstract_Finesse_Shortcode
{

    function register_shortcode()
    {
        $names = $this->get_names();
        if (is_array($names)) {
            foreach ($names as $name) {
                add_shortcode($name, array($this, 'render_shortcode'));
            }
        } else {
            add_shortcode($names, array($this, 'render_shortcode'));
        }
    }

    final function render_shortcode($attr, $inner_content = null, $code = "")
    {
        $inner_content = ___($inner_content);
        return $this->render($attr, $inner_content, $code);
    }

    abstract function render($attr, $inner_content = null, $code = "");

    abstract function get_names();

    protected function prepare_content($inner_content)
    {
        $inner_content = shortcode_unautop($inner_content);
        $inner_content = trim($inner_content, "\x00..\x1F");
        return $inner_content;
    }

}
