<?php
namespace LivemeshAddons\ThemeBuilder;

use ElementorPro\Modules\ThemeBuilder\Documents\Single;
use ElementorPro\Modules\ThemeBuilder\Module;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define Loop as a template item
class Livemesh_Item extends Single {

    public static function get_properties() {

        $properties = parent::get_properties();

        $properties['condition_type'] = 'singular';

        $properties['location'] = 'livemesh_item';

        return $properties;
    }

    public function get_name() {
        return 'livemesh_item';
    }

    public static function get_title() {
        return __('Livemesh Item', 'livemesh-el-addons');
    }

    public static function get_preview_as_options() {

        $post_types = self::get_public_post_types();

        $post_types['attachment'] = get_post_type_object('attachment')->label;

        $post_types_options = [];

        foreach ($post_types as $post_type => $label) {
            $post_types_options['single/' . $post_type] = get_post_type_object($post_type)->labels->singular_name;
        }

        return [
            'single' => [
                'label' => __('Single', 'elementor-pro'),
                'options' => $post_types_options,
            ],
            'page/404' => __('404', 'elementor-pro'),
        ];
    }

    public static function get_public_post_types() {

        $post_types_options = [];

        $args = array(
            'public' => true,
        );

        $post_types = get_post_types($args, 'objects');

        foreach ($post_types as $post_type) {

            if ('elementor_library' != $post_type->name)
                $post_types_options[$post_type->name] = $post_type->label;

        }

        return $post_types_options;
    }

}
