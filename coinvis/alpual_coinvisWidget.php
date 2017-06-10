<?php

/**
 * Created by PhpStorm.
 * User: Paul
 * Date: 6/9/2017
 * Time: 6:19 PM
 */
class alpual_coinvisWidget extends WP_Widget {

    /** constructor -- name this the same as the class above */
    function alpual_coinvisWidget() {
        parent::WP_Widget(false, $name = 'CoinVis Widget');
    }

    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) {
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
        $coin_id 	= $instance['coin'];
        $url = "https://api.coinmarketcap.com/v1/ticker/$coin_id/";

        //  Initiate curl
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification
            CURLOPT_RETURNTRANSFER => true, // Will return the response, if false it print the response
            CURLOPT_URL => $url // Set the url
        ));

        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);

        if(!$result){
            die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }

        // Will dump a beauty json :3
        $coinData = json_decode($result, true)[0];
        /*var_dump($coinData);*/

        ?>
        <?php echo $before_widget;?>
        <?php if ( $title )
            echo $before_title . $title .  $after_title;
        $content = '<ul>
            <li class="coinData">Price (USD): <span class="coinDataVal coinPrice">$' . $coinData[price_usd]  .'</span></li>
            <li class="coinData">Percent Change Last Hour: <span class="coinDataVal coinPercent">' . $coinData[percent_change_1h] .'%</span></li>
            <li class="coinData">Percent Change Last Day: <span class="coinDataVal coinPercent">' . $coinData[percent_change_24h] . '%</span></li>
            <li class="coinData">Percent Change Last Week: <span class="coinDataVal coinPercent">' . $coinData[percent_change_7d] . '%</span></li>
        </ul>';
        echo $content;?>

        <?= "<script>var coinDataSet = " . $result . "</script>"?>
        <?php wp_enqueue_script( 'coinVis', get_stylesheet_directory_uri() . '/coinVis.js');?>
        <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['coin'] = strip_tags($new_instance['coin']);
        return $instance;
    }

    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {

        $title 		= esc_attr($instance['title']);
        $coin	= esc_attr($instance['coin']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('coin'); ?>"><?php _e('Coin ID (from Coin Marketcap API):'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('coin'); ?>" name="<?php echo $this->get_field_name('coin'); ?>" type="text" value="<?php echo $coin; ?>" />
        </p>

        <?php
    }


} // end class example_widget
