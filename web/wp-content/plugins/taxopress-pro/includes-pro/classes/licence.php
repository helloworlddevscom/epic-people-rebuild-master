<?php

use PublishPress\EDD_License\Core\Container as EDDContainer;
use PublishPress\EDD_License\Core\Services as EDDServices;
use PublishPress\EDD_License\Core\ServicesConfig as EDDServicesConfig;

class TaxoPress_License
{

    const MENU_SLUG = 'st_options';

    // class instance
    static $instance;

    /**
     * @var Container
     */
    private $edd_container;

    /**
     * Constructor
     *
     * @return void
     * @author Olatechpro
     */
    public function __construct()
    {
        // Admin menu
        add_action('admin_menu', [$this, 'admin_menu']);

        $this->init_edd_connector();
    }

    /** Singleton instance */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init_edd_connector()
    {
        $config = new EDDServicesConfig();
        $config->setApiUrl(TAXOPRESS_EDD_STORE_URL);
        $config->setLicenseKey($this->get_license_key());
        $config->setLicenseStatus($this->get_license_status());
        $config->setPluginVersion(TAXOPRESS_PRO_VERSION);
        $config->setEddItemId(TAXOPRESS_PRO_EDD_ITEM_ID);
        $config->setPluginAuthor(TAXOPRESS_PLUGIN_AUTHOR);
        $config->setPluginFile(TAXOPRESS_PLUGIN_FILE);

        $this->edd_container = new EDDContainer();
        $this->edd_container->register(new EDDServices($config));

        // Instantiate the update manager
        $this->edd_container['update_manager'];
    }

    /**
     * Add WP admin menu for Tags
     *
     * @return void
     * @author Olatechpro
     */
    public function admin_menu()
    {
        add_submenu_page(
            self::MENU_SLUG,
            __('Licence', 'simpletags'),
            __('Licence', 'simpletags'),
            'admin_simple_tags',
            'st_licence',
            [
                $this,
                'page_taxopress_licence',
            ]
        );
    }

    private function get_license_key()
    {
        return get_option('taxopress_license_key');
    }

    private function get_license_status()
    {
        $status = get_option('taxopress_license_status');

        return ($status !== false && $status == 'valid') ? 'active' : 'inactive';
    }

    /**
     * Method for build the page HTML manage tags
     *
     * @return void
     * @author Olatechpro
     */
    public function page_taxopress_licence()
    {
        echo '<div class="taxopress-licence-form-wrap">';
        $this->process_licence_save();

        $license = $this->get_license_key();
        $status  = $this->get_license_status();
        ?>

        <form class="basic-settings" action="<?php
        echo admin_url('admin.php?page=st_licence'); ?>" method="post">

            <h2><?php
                _e('Licence', 'simpletags'); ?>:</h2>
            <table class="form-table" role="presentation">
                <tbody>
                <tr>
                    <th scope="row"><?php
                        _e('License key:', 'simpletags'); ?></th>
                    <td><label for="taxopress_licence_key_input">
                            <input type="text" id="taxopress_licence_key_input" name="taxopress_licence_key_input"
                                   value="<?php
                                   esc_attr_e($license); ?>">
                            <div class="taxopress_licence_key_status <?php
                            echo $status; ?>"><span class="taxopress_licence_key_label"><?php
                                    _e('Status', 'simpletags'); ?>: </span><?php
                                echo ucwords($status); ?></div>
                            <p class="taxopress_settings_field_description"><?php
                                _e('Your license key provides access to updates and support.', 'simpletags'); ?></p>
                        </label>
                    </td>
                </tr>


                <?php
                if (false !== $license) { ?>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php
                            _e('Activate License', 'simpletags'); ?>
                        </th>
                        <td>
                            <?php
                            if ($status !== false && $status == 'active') { ?>
                                <?php
                                wp_nonce_field('taxopress_submit_licence', 'taxopress_nonce'); ?>
                                <input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php
                                _e('Deactivate License', 'simpletags'); ?>"/>
                                <?php
                            } else {
                                wp_nonce_field('taxopress_submit_licence', 'taxopress_nonce'); ?>
                                <input type="submit" class="button-secondary" name="edd_license_activate" value="<?php
                                _e('Activate License', 'simpletags'); ?>"/>
                                <?php
                            } ?>
                        </td>
                    </tr>
                    <?php
                } ?>

                </tbody>
            </table>

            <?php
            wp_nonce_field('taxopress_submit_licence', 'taxopress_nonce'); ?>

            <input type="submit" name="submit-licence" id="submit" class="button button-primary" value="<?php
            esc_attr_e('Save Changes', 'simpletags'); ?>"></form>
        </div>

        <?php
        SimpleTags_Admin::printAdminFooter();
    }

    public function process_licence_save()
    {
        if(!current_user_can('admin_simple_tags')){
            return;
        }

        if (isset($_POST['submit-licence'])) {
            // run a quick security check
            if (!check_admin_referer('taxopress_submit_licence', 'taxopress_nonce')) {
                return;
            }

            $licence_key_save = isset($_POST['taxopress_licence_key_input']) ? sanitize_text_field($_POST['taxopress_licence_key_input']) : '';
            update_option('taxopress_license_key', $licence_key_save);
            //activate
            $status = $this->activate_licence_key($licence_key_save);
            update_option('taxopress_license_status', $status);
        }

        if (isset($_POST['edd_license_activate'])) {
            $licence_key_save = isset($_POST['taxopress_licence_key_input']) ? sanitize_text_field($_POST['taxopress_licence_key_input']) : '';
            update_option('taxopress_license_key', $licence_key_save);
            //activate
            $status = $this->activate_licence_key($licence_key_save);
            update_option('taxopress_license_status', $status);
        }

        if (isset($_POST['edd_license_deactivate'])) {
            $licence_key_save = isset($_POST['taxopress_licence_key_input']) ? sanitize_text_field($_POST['taxopress_licence_key_input']) : '';
            update_option('taxopress_license_key', $licence_key_save);
            //activate
            $status = $this->deactivate_licence_key($licence_key_save);
            update_option('taxopress_license_status', $status);
        }
    }

    public function activate_licence_key($licence_key)
    {
        $licence_key = trim($licence_key);

        if (!empty($licence_key)) {
            $license_manager = $this->edd_container['license_manager'];

            return $license_manager->validate_license_key($licence_key, TAXOPRESS_PRO_EDD_ITEM_ID);
        }
    }

    public function deactivate_licence_key($licence_key)
    {
        $licence_key = trim($licence_key);

        if (!empty($licence_key)) {
            $license_manager = $this->edd_container['license_manager'];

            return $license_manager->deactivate_license_key($licence_key, TAXOPRESS_PRO_EDD_ITEM_ID);
        }
    }
}
