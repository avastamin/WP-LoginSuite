<?php
/**
 * Plugin Name: Admin Logo Customization
 * Plugin URI: http://ruhulamin.me
 * Description: Customize your WordPress admin and login page logo easily
 * Version: 1.1.0
 * Author: Ruhul Amin
 * Author URI: http://www.ruhulamin.me
 * License: GPL2
 * Text Domain: wp-admin-logo
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class WP_Admin_Logo_Customization {
    private $options;
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'init_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('login_head', array($this, 'custom_login_logo'));
    }

    public function add_plugin_page() {
        add_options_page(
            __('Login Logo Settings', 'wp-admin-logo'),
            __('Login Logo', 'wp-admin-logo'),
            'manage_options',
            'wp-admin-logo',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $options = get_option('wp_alc_settings'); ?>
        <div class="wrap">
            <h2>WordPress Admin Login Customization</h2>
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php
                settings_fields('wp_alc_options');
                do_settings_sections('wp_alc_options');
                ?>
                <table class="form-table">
                    <tr>
                        <th>Login Logo</th>
                        <td>
                            <input type="file" name="wp_alc_settings[login_logo]" accept="image/*">
                            <?php if (isset($options['login_logo'])): ?>
                                <div style="margin-top: 10px;">
                                    <img src="<?php echo esc_url($options['login_logo']); ?>" style="max-width: 200px;">
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Background Color</th>
                        <td>
                            <input type="color" name="wp_alc_settings[bg_color]" value="<?php echo isset($options['bg_color']) ? esc_attr($options['bg_color']) : '#ffffff'; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>Background Image</th>
                        <td>
                            <input type="file" name="wp_alc_settings[bg_image]" accept="image/*">
                            <?php if (isset($options['bg_image'])): ?>
                                <div style="margin-top: 10px;">
                                    <img src="<?php echo esc_url($options['bg_image']); ?>" style="max-width: 200px;">
                                    <br>
                                    <label>
                                        <input type="checkbox" name="wp_alc_settings[remove_bg_image]" value="1">
                                        Remove background image
                                    </label>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function init_settings() {
        register_setting('wp_alc_options', 'wp_alc_settings', array($this, 'handle_file_upload'));
    }

    public function handle_file_upload($options) {
        $existing_options = get_option('wp_alc_settings');

        // Check if user wants to remove background image
        if (isset($options['remove_bg_image']) && $options['remove_bg_image'] == 1) {
            // Preserve all existing options except background image
            $options = array_merge($existing_options, $options);
            unset($options['bg_image']);
            unset($options['remove_bg_image']);
            return $options;
        }

        if (!empty($_FILES['wp_alc_settings'])) {
            $files = $_FILES['wp_alc_settings'];
            
            // Handle logo upload
            if (!empty($files['name']['login_logo'])) {
                $logo_file = array(
                    'name' => $files['name']['login_logo'],
                    'type' => $files['type']['login_logo'],
                    'tmp_name' => $files['tmp_name']['login_logo'],
                    'error' => $files['error']['login_logo'],
                    'size' => $files['size']['login_logo']
                );
                
                $uploaded_logo = media_handle_sideload($logo_file, 0);
                if (!is_wp_error($uploaded_logo)) {
                    $options['login_logo'] = wp_get_attachment_url($uploaded_logo);
                }
            } else {
                // Preserve existing logo if no new upload
                if (isset($existing_options['login_logo'])) {
                    $options['login_logo'] = $existing_options['login_logo'];
                }
            }
            
            // Handle background image upload
            if (!empty($files['name']['bg_image'])) {
                $bg_file = array(
                    'name' => $files['name']['bg_image'],
                    'type' => $files['type']['bg_image'],
                    'tmp_name' => $files['tmp_name']['bg_image'],
                    'error' => $files['error']['bg_image'],
                    'size' => $files['size']['bg_image']
                );
                
                $uploaded_bg = media_handle_sideload($bg_file, 0);
                if (!is_wp_error($uploaded_bg)) {
                    $options['bg_image'] = wp_get_attachment_url($uploaded_bg);
                }
            } else {
                // Preserve existing background image if no new upload and not removing
                if (isset($existing_options['bg_image'])) {
                    $options['bg_image'] = $existing_options['bg_image'];
                }
            }
        }

        // Preserve background color if it exists in old settings but not in new
        if (!isset($options['bg_color']) && isset($existing_options['bg_color'])) {
            $options['bg_color'] = $existing_options['bg_color'];
        }
        
        // Always remove the checkbox value from options
        unset($options['remove_bg_image']);
        return $options;
    }

    public function enqueue_admin_scripts($hook) {
        if('settings_page_wp-admin-logo' !== $hook) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(
            'wp-admin-logo-js',
            plugins_url('/js/admin-logo.js', __FILE__),
            array('jquery'),
            '1.1.0',
            true
        );
    }

    public function custom_login_logo() {
        $options = get_option('wp_alc_settings');
        if(!empty($options['login_logo'])) {
            ?>
            <style type="text/css">
                body.login {
                    <?php if (isset($options['bg_color'])): ?>
                        background-color: <?php echo esc_attr($options['bg_color']); ?>;
                    <?php endif; ?>
                    
                    <?php if (isset($options['bg_image'])): ?>
                        background-image: url('<?php echo esc_url($options['bg_image']); ?>');
                        background-size: cover;
                        background-position: center;
                        background-repeat: no-repeat;
                    <?php endif; ?>
                }
                
                .login h1 a {
                    <?php if (isset($options['login_logo'])): ?>
                        background-image: url('<?php echo esc_url($options['login_logo']); ?>') !important;
                        background-size: contain;
                        width: 320px;
                        height: 120px;
                    <?php endif; ?>
                }
            </style>
            <?php
        }
    }
}

// Initialize plugin
add_action('plugins_loaded', function() {
    new WP_Admin_Logo_Customization();
});


