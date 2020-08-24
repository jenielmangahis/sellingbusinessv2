<?php 
vc_map( array(
    "name" => __("Section", "sphene"),
    "base" => "section",
    "category" => __('Sphene',"sphene"),
    "as_parent" => array('except' => 'counter'),
    "content_element" => true,
    "show_settings_on_create" => false,
    "is_container" => true,
    "params" => array(
        // add params same as with any other content element
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", "sphene"),
            "param_name" => "el_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "sphene")
        )
    ),

) );
?>