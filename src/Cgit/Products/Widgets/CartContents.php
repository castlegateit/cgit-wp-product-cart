<?php

namespace Cgit\Products;

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
        $cart = Cart::getInstance();

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
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ?
            strip_tags($new_instance['title']) : '';

        return $instance;
    }
}
