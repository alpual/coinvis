<?php


include_once('alpual_LifeCycle.php');

class alpual_Plugin extends alpual_LifeCycle {

    protected $features = ['graph'=>false, 'table'=>true];
    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'ATextInput' => array(__('Enter in some text', 'my-awesome-plugin')),
            'AmAwesome' => array(__('I like this awesome plugin', 'my-awesome-plugin'), 'false', 'true'),
            'CanDoSomething' => array(__('Which user role can do something', 'my-awesome-plugin'),
                                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone')
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'CoinVis';
    }

    protected function getMainPluginFileName() {
        return 'coinvis.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37


        // Adding scripts & styles to all pages
        // Examples:
        //        wp_enqueue_script('jquery');
                wp_enqueue_style('my-style', plugins_url('/css/coinvis.css', __FILE__));
                wp_enqueue_script('my-script', plugins_url('/js/coinvis.js', __FILE__));


        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39
        // add_shortcode('say-hello-world', array($this, 'doMyShortcode'));
        add_shortcode('coin-vis', array($this, 'ap_coin_visualization'));


        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }

    function ap_coin_visualization($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // override default attributes with user attributes
        $coin_atts = shortcode_atts([
            'coin' => 'bitcoin',
            'datapoint' => false,
            'graph' => "on"
        ], $atts, $tag);

        // start output
        $o = '';

        if (!$coin_atts['datapoint']) {
            // start box
            $o .= '<div class="simple-crypto-box">';

            // title
            $o .= '<h2 class="coinHeader">' . esc_html__($coin_atts['coin']) . '</h2>';
        }

        $url = "https://api.coinmarketcap.com/v1/ticker/" . $coin_atts['coin'] . "/";

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

        if (!$result) {
            die('Error: "' . curl_error($result) . '" - Code: ' . curl_errno($result));
        }

        // Will dump a beauty json :3

        $coinData = json_decode($result, true)[0];
        //$coinData[percent_change_1h] = "-2.5"; // for debugging negatives

        /*var_dump($coinData);*/
        $content = "";

        if ($this->features['table'] && !$coin_atts['datapoint']) {

            $content .= '<ul>
            <li class="coinData">Price (USD): <span class="coinDataVal coinPrice">$' . number_format($coinData[price_usd], 2) . '&nbsp;</span></li>
            <li class="coinData">Percent Change Last Hour: <span class="coinDataVal coinPercent">' . number_format($coinData[percent_change_1h], 2) . '%</span></li>
            <li class="coinData">Percent Change Last Day: <span class="coinDataVal coinPercent">' . number_format($coinData[percent_change_24h], 2) . '%</span></li>
            <li class="coinData">Percent Change Last Week: <span class="coinDataVal coinPercent">' . number_format($coinData[percent_change_7d], 2) . '%</span></li>
            <li class="coinData">Market Cap: <span class="coinDataVal coinUSD">$' . number_format($coinData[market_cap_usd], 1) . '&nbsp;&nbsp;</span></li>
            <li class="coinData">24 Hour Volume: <span class="coinDataVal coinUSD">$' . number_format($coinData['24h_volume_usd'], 1) . '&nbsp;&nbsp;</span></li>
            <li class="coinData">Available Coin Supply: <span class="coinDataVal coinAmount">' . number_format($coinData[available_supply], 1) . '&nbsp;&nbsp;</span></li>
            <li class="coinData">Total Supply: <span class="coinDataVal coinAmount">' . number_format($coinData[total_supply], 1) . '&nbsp;&nbsp;</span></li>
        </ul>';

        } else {
            $dpoint = strtolower($coin_atts['datapoint']);
            $content .=  number_format($coinData[$dpoint]);
        }
        if ($this->features['graph'] && strtolower($coin_atts['graph']) != "off" && !$coin_atts['datapoint']) {
            $content .='<svg id="' . $coinData[id] . '" data-coin=\'' . json_encode($coinData) . '\' class="simpleCoin"></svg>';
        }

        $o .= $content;
        // end box
        if (!$coin_atts['datapoint']) {
            $o .= '
        </div>';
        }

        return $o;
    }


}
