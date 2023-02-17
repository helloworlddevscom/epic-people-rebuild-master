<?php
//include licence
require_once (TAXOPRESS_ABSPATH . '/includes-pro/classes/licence.php');

function taxopress_load_admin_licence_menu(){
    TaxoPress_License::get_instance();
}
add_action('taxopress_admin_class_after_includes', 'taxopress_load_admin_licence_menu');

function taxopress_pro_admin_pages($taxopress_pages){

    $taxopress_pages[] = 'st_licence';

    return $taxopress_pages;
}
add_filter('taxopress_admin_pages', 'taxopress_pro_admin_pages');

function taxopress_load_admin_pro_assets(){
    wp_register_style( 'st-admin-pro', STAGS_URL . '/includes-pro/assets/css/pro.css', array(), STAGS_VERSION, 'all' );
    wp_register_script( 'st-admin-pro', STAGS_URL . '/includes-pro/assets/js/pro.js', array( 'jquery' ), STAGS_VERSION );
}
add_action('taxopress_admin_class_before_assets_register', 'taxopress_load_admin_pro_assets');

function taxopress_load_admin_pro_styles(){
    wp_enqueue_style( 'st-admin-pro' );
    wp_enqueue_script( 'st-admin-pro' );
}
add_action('taxopress_admin_class_after_styles_enqueue', 'taxopress_load_admin_pro_styles');

function taxopress_action_is_false($limit){
    return false;
}
add_filter('taxopress_post_tags_create_limit', 'taxopress_action_is_false');
add_filter('taxopress_related_posts_create_limit', 'taxopress_action_is_false');
add_filter('taxopress_tag_clouds_create_limit', 'taxopress_action_is_false');
add_filter('taxopress_autolinks_create_limit', 'taxopress_action_is_false');
add_filter('taxopress_autoterms_create_limit', 'taxopress_action_is_false');
add_filter('taxopress_suggestterms_create_limit', 'taxopress_action_is_false');


function taxopress_pro_autoterm_schedule_field($current){

    $ui = new taxopress_admin_ui();

    $cron_options = [
        'disable'=> __('None', 'simple-tags'),
        'hourly' => __('Hourly', 'simple-tags'),
        'daily'  => __('Daily', 'simple-tags'),
    ];
 
    ?>
   <tr valign="top"><th scope="row"><label><?php echo __('Schedule Auto Terms for your content', 'simple-tags'); ?></label></th>

        <td>
            <?php 
                 $cron_schedule = (!empty($current['cron_schedule'])) ? $current['cron_schedule'] : 'disable';
                 foreach($cron_options as $option => $label){
                    $checked_status = ($option === $cron_schedule)  ? 'checked' : '';
                    echo '<label> <input 
                    class="autoterm_cron" 
                    type="radio" id="autoterm_cron_'.$option.'" 
                    name="taxopress_autoterm[cron_schedule]" 
                    value="'.$option.'"
                    '.$checked_status.'
                    > '.$label.'</label> <br /><br />';                            
                }
            ?>
        </td>
    </tr>
                                                        <?php

                                                            $select             = [
                                                                'options' => [
                                                                    [
                                                                        'attr'    => '0',
                                                                        'text'    => esc_attr__('False', 'simple-tags'),
                                                                        'default' => 'true',
                                                                    ],
                                                                    [
                                                                        'attr' => '1',
                                                                        'text' => esc_attr__('True', 'simple-tags'),
                                                                    ],
                                                                ],
                                                            ];
                                                            $selected           = (isset($current) && isset($current['autoterm_schedule_exclude'])) ? taxopress_disp_boolean($current['autoterm_schedule_exclude']) : '';
                                                            $select['selected'] = !empty($selected) ? $current['autoterm_schedule_exclude'] : '';
                                                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                            echo $ui->get_select_checkbox_input([
                                                                'namearray'  => 'taxopress_autoterm',
                                                                'name'       => 'autoterm_schedule_exclude',
                                                                'class'      => '',
                                                                'labeltext'  => esc_html__('Exclude previously analyzed content', 'simple-tags'),
                                                                'aftertext'  => esc_html__('This enables you to skip posts that have already been analyzed by the Schedule feature.', 'simple-tags'),
                                                                'selections' => $select,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                            ]);
                                                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                            echo $ui->get_number_input([
                                                                'namearray' => 'taxopress_autoterm',
                                                                'name'      => 'schedule_terms_batches',
                                                                'textvalue' => isset($current['schedule_terms_batches']) ? esc_attr($current['schedule_terms_batches']) : '20',
                                                                'labeltext' => esc_html__('Limit per batches',
                                                                    'simple-tags'),
                                                                'helptext'  => esc_html__('This enables your scheduled Auto Terms to run in batches. If you have a lot of content, set this to a lower number to avoid timeouts.', 'simple-tags'),
                                                                'min'       => '1',
                                                                'required'  => true,
                                                            ]);

                                                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                            echo $ui->get_number_input([
                                                                'namearray' => 'taxopress_autoterm',
                                                                'name'      => 'schedule_terms_sleep',
                                                                'textvalue' => isset($current['schedule_terms_sleep']) ? esc_attr($current['schedule_terms_sleep']) : '10',
                                                                'labeltext' => esc_html__('Batches wait time', 'simple-tags'),
                                                                'helptext'  => esc_html__('This is the wait time (in seconds) between processing batches of Auto Terms. If you have a lot of existing content, set this to a higher number to avoid timeouts.', 'simple-tags'),
                                                                'min'       => '0',
                                                                'required'  => true,
                                                            ]);

                                                            $select             = [
                                                                'options' => [
                                                                    [
                                                                        'attr' => '1',
                                                                        'text' => esc_attr__('24 hours ago', 'simple-tags')
                                                                    ],
                                                                    [
                                                                        'attr' => '7',
                                                                        'text' => esc_attr__('7 days ago', 'simple-tags')
                                                                    ],
                                                                    [
                                                                        'attr' => '14',
                                                                        'text' => esc_attr__('2 weeks ago', 'simple-tags')
                                                                    ],
                                                                    [
                                                                        'attr' => '30',
                                                                        'text' => esc_attr__('1 month ago', 'simple-tags'),
                                                                        'default' => 'true'
                                                                    ],
                                                                    [
                                                                        'attr' => '180',
                                                                        'text' => esc_attr__('6 months ago', 'simple-tags')
                                                                    ],
                                                                    [
                                                                        'attr' => '365',
                                                                        'text' => esc_attr__('1 year ago', 'simple-tags')
                                                                    ],
                                                                    [
                                                                        'attr'    => '0',
                                                                        'text'    => esc_attr__('No limit', 'simple-tags')
                                                                    ],
                                                                ],
                                                            ];

                                                            if(isset($current) && is_array($current)){
                                                                $select             = [
                                                                    'options' => [
                                                                        [
                                                                            'attr' => '1',
                                                                            'text' => esc_attr__('24 hours ago', 'simple-tags')
                                                                        ],
                                                                        [
                                                                            'attr' => '7',
                                                                            'text' => esc_attr__('7 days ago', 'simple-tags')
                                                                        ],
                                                                        [
                                                                            'attr' => '14',
                                                                            'text' => esc_attr__('2 weeks ago', 'simple-tags')
                                                                        ],
                                                                        [
                                                                            'attr' => '30',
                                                                            'text' => esc_attr__('1 month ago', 'simple-tags'),
                                                                        ],
                                                                        [
                                                                            'attr' => '180',
                                                                            'text' => esc_attr__('6 months ago', 'simple-tags')
                                                                        ],
                                                                        [
                                                                            'attr' => '365',
                                                                            'text' => esc_attr__('1 year ago', 'simple-tags')
                                                                        ],
                                                                        [
                                                                            'attr'    => '0',
                                                                            'text'    => esc_attr__('No limit', 'simple-tags'),
                                                                            'default' => 'true'
                                                                        ],
                                                                    ],
                                                                ];
                                                            }
                                                            
                                                            $selected           = (isset($current) && isset($current['schedule_terms_limit_days'])) ? taxopress_disp_boolean($current['schedule_terms_limit_days']) : '';
                                                            $select['selected'] = !empty($selected) ? $current['schedule_terms_limit_days'] : '';
                                                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                            echo $ui->get_select_number_select([
                                                                'namearray'  => 'taxopress_autoterm',
                                                                'name'       => 'schedule_terms_limit_days',
                                                                'labeltext'  => esc_html__('Limit Auto Terms, based on published date',
                                                                    'simple-tags'),
                                                                    'aftertext'  => esc_html__('This setting can limit your scheduled Auto Terms query to only recent content. We recommend using this feature to avoid timeouts on large sites.', 'simple-tags'),
                                                                'selections' => $select,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                            ]);
                                                        ?>
    <?php 
}
add_action('taxopress_autoterms_after_autoterm_schedule', 'taxopress_pro_autoterm_schedule_field');


add_action( 'taxopress_cron_autoterms_hourly', 'taxopress_cron_autoterms_hourly_execution' );
if ( ! wp_next_scheduled( 'taxopress_cron_autoterms_hourly' ) ) {
    wp_schedule_event( time(), 'hourly', 'taxopress_cron_autoterms_hourly' );
}
function taxopress_cron_autoterms_hourly_execution(){

    global $wpdb;

    $autoterms = taxopress_get_autoterm_data();
    $flag = false;
    foreach($autoterms as $autoterm_key => $autoterm_data){
        $cron_schedule = isset($autoterm_data['cron_schedule']) ? $autoterm_data['cron_schedule'] : 'disable';
        $post_types = isset($autoterm_data['post_types']) ? (array)$autoterm_data['post_types'] : [];
        $post_status = isset($autoterm_data['post_status']) && is_array($autoterm_data['post_status']) ? $autoterm_data['post_status'] : ['publish'];
        $autoterm_schedule_exclude = isset($autoterm_data['autoterm_schedule_exclude']) ? (int)$autoterm_data['autoterm_schedule_exclude'] : 0;
         
        if($cron_schedule !== 'hourly'){
            continue;
        }

        if(empty($post_types)){
            continue;
        }

        $schedule_terms_limit_days     = (int) $autoterm_data['schedule_terms_limit_days'];
		$schedule_terms_limit_days_sql = '';
		if ( $schedule_terms_limit_days > 0 ) {
			$schedule_terms_limit_days_sql = 'AND post_date > "' . date( 'Y-m-d H:i:s', time() - $schedule_terms_limit_days * 86400 ) . '"';
		}
        

        $limit = (isset($autoterm_data['schedule_terms_batches']) && (int)$autoterm_data['schedule_terms_batches'] > 0) ? (int)$autoterm_data['schedule_terms_batches'] : 20;

        $sleep = (isset($autoterm_data['schedule_terms_sleep']) && (int)$autoterm_data['schedule_terms_sleep'] > 0) ? (int)$autoterm_data['schedule_terms_sleep'] : 0;

        if($autoterm_schedule_exclude > 0){
            $objects = (array) $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} ON ( ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_taxopress_autotermed' ) WHERE post_type IN ('" . implode("', '", $post_types) . "') AND {$wpdb->postmeta}.post_id IS NULL AND post_status IN ('" . implode("', '", $post_status) . "') {$schedule_terms_limit_days_sql} ORDER BY ID DESC LIMIT {$limit}");
        }else{
            $objects = (array) $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} WHERE post_type IN ('" . implode("', '", $post_types) . "') AND post_status IN ('" . implode("', '", $post_status) . "') {$schedule_terms_limit_days_sql} ORDER BY ID DESC LIMIT {$limit}");
        }

        if (!empty($objects)) {
            $current_post = 0;
            foreach ($objects as $object) {
                $current_post++;
                update_post_meta($object->ID, '_taxopress_autotermed', 1);
                SimpleTags_Client_Autoterms::auto_terms_post( $object, $autoterm_data['taxonomy'], $autoterm_data, true, 'hourly_cron_schedule', 'st_autoterms' );
                unset($object);
                if ($sleep > 0 && $current_post % $limit == 0) {
                    sleep($sleep);
                }
            } 
        }
    }

}

if ( ! wp_next_scheduled( 'taxopress_cron_autoterms_daily' ) ) {
    wp_schedule_event( time(), 'daily', 'taxopress_cron_autoterms_daily' );
}
add_action( 'taxopress_cron_autoterms_daily', 'taxopress_cron_autoterms_daily_execution' );
function taxopress_cron_autoterms_daily_execution(){

    global $wpdb;

    $autoterms = taxopress_get_autoterm_data();
    $flag = false;
    foreach($autoterms as $autoterm_key => $autoterm_data){
        $cron_schedule = isset($autoterm_data['cron_schedule']) ? $autoterm_data['cron_schedule'] : 'disable';
        $post_types = isset($autoterm_data['post_types']) ? (array)$autoterm_data['post_types'] : [];
        $post_status = isset($autoterm_data['post_status']) && is_array($autoterm_data['post_status']) ? $autoterm_data['post_status'] : ['publish'];
        $autoterm_schedule_exclude = isset($autoterm_data['autoterm_schedule_exclude']) ? (int)$autoterm_data['autoterm_schedule_exclude'] : 0;
         
         
        if($cron_schedule !== 'daily'){
            continue;
        }

        if(empty($post_types)){
            continue;
        }

        $schedule_terms_limit_days     = (int) $autoterm_data['schedule_terms_limit_days'];
		$schedule_terms_limit_days_sql = '';
		if ( $schedule_terms_limit_days > 0 ) {
			$schedule_terms_limit_days_sql = 'AND post_date > "' . date( 'Y-m-d H:i:s', time() - $schedule_terms_limit_days * 86400 ) . '"';
		}

        $limit = (isset($autoterm_data['schedule_terms_batches']) && (int)$autoterm_data['schedule_terms_batches'] > 0) ? (int)$autoterm_data['schedule_terms_batches'] : 20;

        $sleep = (isset($autoterm_data['schedule_terms_sleep']) && (int)$autoterm_data['schedule_terms_sleep'] > 0) ? (int)$autoterm_data['schedule_terms_sleep'] : 0;
        
        if($autoterm_schedule_exclude > 0){
            $objects = (array) $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} ON ( ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_taxopress_autotermed' ) WHERE post_type IN ('" . implode("', '", $post_types) . "') AND {$wpdb->postmeta}.post_id IS NULL AND post_status IN ('" . implode("', '", $post_status) . "') {$schedule_terms_limit_days_sql} ORDER BY ID DESC LIMIT {$limit}");
        }else{
            $objects = (array) $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} WHERE post_type IN ('" . implode("', '", $post_types) . "') AND post_status IN ('" . implode("', '", $post_status) . "') {$schedule_terms_limit_days_sql} LIMIT {$limit}");
        }

        if (!empty($objects)) {
            $current_post = 0;
            foreach ($objects as $object) {
                $current_post++;
                update_post_meta($object->ID, '_taxopress_autotermed', 1);
                SimpleTags_Client_Autoterms::auto_terms_post( $object, $autoterm_data['taxonomy'], $autoterm_data, true, 'daily_cron_schedule', 'st_autoterms' );
                unset($object);
                if ($sleep > 0 && $current_post % $limit == 0) {
                    sleep($sleep);
                }
            } 
        }
    }

}

function taxopress_autoterms_after_autoterm_terms_to_use_field($current){
    $ui = new taxopress_admin_ui();
    $select             = [
        'options' => [
            [
                'attr'    => '0',
                'text'    => esc_attr__('False', 'simple-tags'),
                'default' => 'true',
            ],
            [
                'attr' => '1',
                'text' => esc_attr__('True', 'simple-tags'),
            ],
        ],
    ];
    $selected           = (isset($current) && isset($current['autoterm_use_dandelion'])) ? taxopress_disp_boolean($current['autoterm_use_dandelion']) : '';
    $select['selected'] = !empty($selected) ? $current['autoterm_use_dandelion'] : '';
    echo $ui->get_select_checkbox_input([
        'namearray'  => 'taxopress_autoterm',
        'name'       => 'autoterm_use_dandelion',
        'class'      => 'autoterm_use_dandelion  autoterm-terms-to-use-field',
        'labeltext'  => esc_html__('Dandelion', 'simple-tags'),
        'aftertext'  => esc_html__('This will automatically add new terms from the Dandelion API service. Please test carefully before use.', 'simple-tags'),
        'selections' => $select,
    ]);

    echo $ui->get_text_input([
        'namearray' => 'taxopress_autoterm',
        'name'      => 'terms_datatxt_access_token',
        'class'     => 'terms_datatxt_access_token',
        'textvalue' => isset($current['terms_datatxt_access_token']) ? esc_attr($current['terms_datatxt_access_token']) : '',
        'toplabel' => esc_html__('Dandelion API token', 'simple-tags'),
        'labeltext'  => '',
        'helptext'  => __('You need an API key to use Dandelion for auto terms. <br /> <a href="https://taxopress.com/docs/dandelion-api/">Click here for documentation.</a>',
            'simple-tags'),
        'required'  => false,
    ]);

    if (!isset($current)) {
        $terms_datatxt_min_confidence = '0.6';
    } else {
        $terms_datatxt_min_confidence = isset($current['terms_datatxt_min_confidence']) ? esc_attr($current['terms_datatxt_min_confidence']) : '0';
    }

    echo $ui->get_number_input([
        'namearray'  => 'taxopress_autoterm',
        'name'       => 'terms_datatxt_min_confidence',
        'class'      => 'terms_datatxt_min_confidence',
        'textvalue'  => $terms_datatxt_min_confidence,
        'toplabel'  => esc_html__('Dandelion API confidence value', 'simple-tags'),
        'labeltext'  => '',
        'helptext'   => __('Choose a value between 0 and 1. A high value such as 0.8 will provide a few, accurate suggestions. A low value such as 0.2 will produce more suggestions, but they may be less accurate.',
            'simple-tags'),
        'min'        => '0',
        'max'        => '1',
        'other_attr' => 'step=".1"',
        'required'   => false,
    ]);


    $select             = [
        'options' => [
            [
                'attr'    => '0',
                'text'    => esc_attr__('False', 'simple-tags'),
                'default' => 'true',
            ],
            [
                'attr' => '1',
                'text' => esc_attr__('True', 'simple-tags'),
            ],
        ],
    ];
    $selected           = (isset($current) && isset($current['autoterm_use_opencalais'])) ? taxopress_disp_boolean($current['autoterm_use_opencalais']) : '';
    $select['selected'] = !empty($selected) ? $current['autoterm_use_opencalais'] : '';
    echo $ui->get_select_checkbox_input([
        'namearray'  => 'taxopress_autoterm',
        'name'       => 'autoterm_use_opencalais',
        'class'      => 'autoterm_use_opencalais  autoterm-terms-to-use-field',
        'labeltext'  => esc_html__('Open Calais', 'simple-tags'),
        'aftertext'  => esc_html__('This will automatically add new terms from the Open Calais service. Please test carefully before use.', 'simple-tags'),
        'selections' => $select,
    ]);

    echo $ui->get_text_input([
        'namearray' => 'taxopress_autoterm',
        'name'      => 'terms_opencalais_key',
        'class'     => 'terms_opencalais_key',
        'textvalue' => isset($current['terms_opencalais_key']) ? esc_attr($current['terms_opencalais_key']) : '',
        'toplabel' => esc_html__('OpenCalais API Key', 'simple-tags'),
        'labeltext'  => '',
        'helptext'  => __('You need an API key to use OpenCalais for auto terms. <br /> <a href="https://taxopress.com/docs/opencalais/">Click here for documentation.</a>',
            'simple-tags'),
        'required'  => false,
    ]);

}
add_action('taxopress_autoterms_after_autoterm_terms_to_use', 'taxopress_autoterms_after_autoterm_terms_to_use_field');

if (!function_exists('taxopress_pro_only_upgrade_function')) {
    //activation functions/codes
    add_action('admin_init', 'taxopress_pro_only_upgrade_function');
    function taxopress_pro_only_upgrade_function()
    {

        if (!get_option('taxopress_pro_3_5_2_upgraded')) {
            //this upgrade is neccessary due to free version uninstall removing role for author
            if ( function_exists( 'get_role' ) ) {
                $role = get_role( 'administrator' );
                if ( null !== $role && ! $role->has_cap( 'simple_tags' ) ) {
                    $role->add_cap( 'simple_tags' );
                }
    
                if ( null !== $role && ! $role->has_cap( 'admin_simple_tags' ) ) {
                    $role->add_cap( 'admin_simple_tags' );
                }
    
                $role = get_role( 'editor' );
                if ( null !== $role && ! $role->has_cap( 'simple_tags' ) ) {
                    $role->add_cap( 'simple_tags' );
                }
            }
          update_option('taxopress_pro_3_5_2_upgraded', true);
       }

    }
}



function taxopress_pro_autoterm_advanced_field($current){
    $ui = new taxopress_admin_ui();
  


    $select             = [
        'options' => [
            [
                'attr'    => '0',
                'text'    => esc_attr__('False', 'simple-tags'),
                'default' => 'true',
            ],
            [
                'attr' => '1',
                'text' => esc_attr__('True', 'simple-tags'),
            ],
        ],
    ];
    $selected           = (isset($current) && isset($current['autoterm_use_regex'])) ? taxopress_disp_boolean($current['autoterm_use_regex']) : '';
    $select['selected'] = !empty($selected) ? $current['autoterm_use_regex'] : '';
    echo $ui->get_select_checkbox_input([
        'namearray'  => 'taxopress_autoterm',
        'name'       => 'autoterm_use_regex',
        'class'      => 'autoterm_use_regex',
        'labeltext'  => esc_html__('Regular Expressions', 'simple-tags'),
        'aftertext'  => esc_html__('Use Regular Expressions to change how Auto Terms analyzes your posts.', 'simple-tags'),
        'selections' => $select,
    ]);

    echo $ui->get_text_input([
        'namearray' => 'taxopress_autoterm',
        'name'      => 'terms_regex_code',
        'class'     => 'terms_regex_code',
        'textvalue' => isset($current['terms_regex_code']) ? esc_attr(stripslashes($current['terms_regex_code'])) : '',
        'toplabel' => esc_html__('Regex code', 'simple-tags'),
        'labeltext'  => '',
        'helptext'  => __('Example <code>/\b({term})\b/i</code> will match whole word and <code>{term}</code> will be replaced with the term name before the regex action.',
            'simple-tags'),
        'required'  => false,
    ]);

}
add_action('taxopress_autoterms_after_autoterm_advanced', 'taxopress_pro_autoterm_advanced_field');