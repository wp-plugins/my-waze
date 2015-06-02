<?php
/**
* Plugin Name: MyWaze
* Plugin URI: http://savvy.co.il
* Description: Add a Waze navigation button to your mobile Wordpress site and get visitors navigate to your location in a click !
* Version: 1.0.0
* Author: Roee Yossef
* Author URI: http://savvy.co.il
* License: GPL2
*/


function my_waze_shortcode(){
$waze_options = get_option('my_waze_settings');
if ( wp_is_mobile() ) {
    /* Display and echo mobile specific stuff here */
    echo '<a class="my_waze" href="waze://?ll='.$waze_options['my_waze_long'].','.$waze_options['my_waze_lat'].'&navigate=yes">My Waze</a>';

}
}
add_shortcode('my_waze', 'my_waze_shortcode');


/**
 * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
 */
add_action( 'wp_enqueue_scripts', 'my_waze_add_my_stylesheet' );

/**
 * Enqueue plugin style-file
 */
function my_waze_add_my_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'my_waze_style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'my_waze_style' );
}


/*
 * Add the admin page
 */
add_action('admin_menu', 'my_waze_admin_page');
function my_waze_admin_page(){
    add_menu_page('MyWaze Settings', 'MyWaze Settings', 'administrator', 'my_waze-settings', 'my_waze_admin_page_callback');
}

/*
 * Register the settings
 */
add_action('admin_init', 'my_waze_register_settings');
function my_waze_register_settings(){
    //this will save the option in the wp_options table as 'my_waze_settings'
    //the third parameter is a function that will validate your input values
    register_setting('my_waze_settings', 'my_waze_settings', 'my_waze_settings_validate');
}

function my_waze_settings_validate($args){
    //$args will contain the values posted in your settings form.
    if(!isset($args['my_waze_long']) || !isset($args['my_waze_lat']) ){
        //add a settings error because the form fields blank, so that the user can enter again
        $args['my_waze_long'] = '';
        $args['my_waze_lat'] = '';
    add_settings_error('my_waze_settings', 'my_waze_no_data', 'Please enter a valid longitude &amp; latitude.', $type = 'error');   
    }

    //make sure you return the args
    return $args;
}

//Display the validation errors and update messages
/*
 * Admin notices
 */
add_action('admin_notices', 'my_waze_admin_notices');
function my_waze_admin_notices(){
   settings_errors();
}

//The markup for your plugin settings page
function my_waze_admin_page_callback(){ ?>
    <div class="wrap">
    <h2>Waze Button Settings</h2>
    <form action="options.php" method="post"><?php
        settings_fields( 'my_waze_settings' );
        do_settings_sections( __FILE__ );

        //get the older values, wont work the first time
        $options = get_option( 'my_waze_settings' ); ?>

        <table class="form-table">
            <tr>
                <th scope="row">Longitude</th>
                <td>
                    <fieldset>
                        <label>
                            <input name="my_waze_settings[my_waze_long]" type="text" id="my_waze_long" value="<?php echo (isset($options['my_waze_long']) && $options['my_waze_long'] != '') ? $options['my_waze_long'] : ''; ?>"/>
                            <br />
                            <span class="description">Please enter longitude.</span>
                        </label>
                    </fieldset>
                </td>

            </tr>
            <tr>
            <th scope="row">Latitude</th>
                <td>
                    <fieldset>
                        <label>
                            <input name="my_waze_settings[my_waze_lat]" type="text" id="my_waze_lat" value="<?php echo (isset($options['my_waze_lat']) && $options['my_waze_lat'] != '') ? $options['my_waze_lat'] : ''; ?>"/>
                            <br />
                            <span class="description">Please enter latitude.</span>
                        </label>
                    </fieldset>
                </td>
             </tr>
        </table>
        <input type="submit" value="Save" />
    </form>
</div>
<?php }



