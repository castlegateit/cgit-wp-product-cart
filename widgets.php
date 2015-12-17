<?php

namespace Cgit;

/**
 * Cart contents widget
 */
class CartContentsWidget extends \WP_Widget
{

    /**
     * Register widget
     */
    function __construct()
    {
        parent::__construct(
            'cgit_cart_contents_widget',
            __('Cart Contents', 'text_domain')
        );
    }

    /**
     * Display widget content
     */
    public function widget($args, $instance)
    {
        $cart = cgit_cart();

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title']
                . apply_filters('widget_title', $instance['title'])
                . $args['after_title'];
        }

        echo $cart->render('contents');
        echo $args['after_widget'];
    }

    /**
     * Display widget settings
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] :
            __('Cart contents', 'text_domain');
        $id = $this->get_field_id('title');
        $name = $this->get_field_name('title');
        $label = __('Title:');
        $value = esc_attr($title);

        echo '<p><label for="' . $id . '">' . $label
            . '</label><input type="text" name="' . $name . '" id="'
            . $id . '" class="widefat" value="' . $value . '" /></p>';
    }

    /**
     * Save widget settings
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ?
            strip_tags($new_instance['title']) : '';

        return $instance;
    }
}

/**
 * Cart add widget
 */
class CartAddWidget extends \WP_Widget
{

    /**
     * Register widget
     */
    function __construct()
    {
        parent::__construct(
            'cgit_cart_add_widget',
            __('Add to Cart', 'text_domain')
        );
    }

    /**
     * Display widget content
     *
     * This widget will only be displayed on single product pages.
     */
    public function widget($args, $instance)
    {
        if (!is_singular(CGIT_PRODUCT_POST_TYPE)) {
            return;
        }

        $cart = cgit_cart();

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title']
                . apply_filters('widget_title', $instance['title'])
                . $args['after_title'];
        }

        echo $cart->render('add');
        echo $args['after_widget'];
    }

    /**
     * Display widget settings
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] :
            __('Add to cart', 'text_domain');
        $id = $this->get_field_id('title');
        $name = $this->get_field_name('title');
        $label = __('Title:');
        $value = esc_attr($title);

        echo '<p><label for="' . $id . '">' . $label
            . '</label><input type="text" name="' . $name . '" id="'
            . $id . '" class="widefat" value="' . $value . '" /></p>';
    }

    /**
     * Save widget settings
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ?
            strip_tags($new_instance['title']) : '';

        return $instance;
    }
}

/**
 * Register widgets
 */
add_action('widgets_init', function() {
    register_widget('Cgit\CartContentsWidget');
    register_widget('Cgit\CartAddWidget');
});
