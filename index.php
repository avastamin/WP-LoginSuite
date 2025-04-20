<?php
/*
Plugin Name: LoginSuite - WordPress Login Page Customizer
Plugin URI: http://www.ruhulamin.me/
Description: Easily customize your WordPress login page with custom logo, background colors, images, and more.
Version: 1.1.1
Author: ruhul105
Author URI: http://www.ruhulamin.me/
License: GPLv2 or later
Text Domain: wp-admin-logo
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
        add_menu_page(
            __('LoginSuite', 'wp-admin-logo'),
            __('LoginSuite', 'wp-admin-logo'),
            'manage_options',
            'wp-admin-logo',
            array($this, 'create_admin_page'),
            'dashicons-admin-appearance',
            60
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
                        <td style="display: flex; flex-direction: column-reverse; align-items: left; gap: 10px; max-width: 400px;">
                            <input type="file" name="wp_alc_settings[login_logo]" accept="image/*">
                            <?php if (isset($options['login_logo'])): ?>
                                <div id="logo-preview" style="margin-top: 10px; position: relative; display: inline-block; max-width: 230px;">
                                    <img src="<?php echo esc_url($options['login_logo']); ?>" style="max-width: 200px;">
                                    <span class="remove-logo" style="position: absolute; top: -10px; right: -10px; cursor: pointer; background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px;">×</span>
                                    <input type="hidden" name="wp_alc_settings[remove_login_logo]" id="remove-logo-input" value="0">
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Background Color</th>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="color" name="wp_alc_settings[bg_color]" id="bg-color-picker" value="<?php echo isset($options['bg_color']) ? esc_attr($options['bg_color']) : '#ffffff'; ?>">
                                <button type="button" id="reset-bg-color" class="button button-secondary" style="height: 30px;">Reset to Default</button>
                                <input type="hidden" name="wp_alc_settings[reset_bg_color]" id="reset-bg-color-input" value="0">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Background Image</th>
                        <td>
                            <input type="file" name="wp_alc_settings[bg_image]" accept="image/*">
                            <?php if (isset($options['bg_image'])): ?>
                                <div style="margin-top: 10px;">
                                    <img src="<?php echo esc_url($options['bg_image']); ?>" style="max-width: 200px;">
                                    <div style="margin-top: 10px;">
                                        <label style="display: block; margin-bottom: 5px;">Background Size:</label>
                                        <select name="wp_alc_settings[bg_size]" style="margin-bottom: 10px;">
                                            <option value="cover" <?php selected(isset($options['bg_size']) ? $options['bg_size'] : 'cover', 'cover'); ?>>Cover</option>
                                            <option value="contain" <?php selected(isset($options['bg_size']) ? $options['bg_size'] : 'cover', 'contain'); ?>>Contain</option>
                                            <option value="auto" <?php selected(isset($options['bg_size']) ? $options['bg_size'] : 'cover', 'auto'); ?>>Auto</option>
                                        </select>

                                        <label style="display: block; margin-bottom: 5px;">Background Repeat:</label>
                                        <select name="wp_alc_settings[bg_repeat]" style="margin-bottom: 10px;">
                                            <option value="no-repeat" <?php selected(isset($options['bg_repeat']) ? $options['bg_repeat'] : 'no-repeat', 'no-repeat'); ?>>No Repeat</option>
                                            <option value="repeat" <?php selected(isset($options['bg_repeat']) ? $options['bg_repeat'] : 'no-repeat', 'repeat'); ?>>Repeat</option>
                                            <option value="repeat-x" <?php selected(isset($options['bg_repeat']) ? $options['bg_repeat'] : 'no-repeat', 'repeat-x'); ?>>Repeat Horizontally</option>
                                            <option value="repeat-y" <?php selected(isset($options['bg_repeat']) ? $options['bg_repeat'] : 'no-repeat', 'repeat-y'); ?>>Repeat Vertically</option>
                                        </select>

                                        <label style="display: block; margin-bottom: 5px;">Background Position:</label>
                                        <select name="wp_alc_settings[bg_position]" style="margin-bottom: 10px;">
                                            <option value="center center" <?php selected(isset($options['bg_position']) ? $options['bg_position'] : 'center center', 'center center'); ?>>Center</option>
                                            <option value="left top" <?php selected(isset($options['bg_position']) ? $options['bg_position'] : 'center center', 'left top'); ?>>Left Top</option>
                                            <option value="left center" <?php selected(isset($options['bg_position']) ? $options['bg_position'] : 'center center', 'left center'); ?>>Left Center</option>
                                            <option value="left bottom" <?php selected(isset($options['bg_position']) ? $options['bg_position'] : 'center center', 'left bottom'); ?>>Left Bottom</option>
                                            <option value="right top" <?php selected(isset($options['bg_position']) ? $options['bg_position'] : 'center center', 'right top'); ?>>Right Top</option>
                                            <option value="right center" <?php selected(isset($options['bg_position']) ? $options['bg_position'] : 'center center', 'right center'); ?>>Right Center</option>
                                            <option value="right bottom" <?php selected(isset($options['bg_position']) ? $options['bg_position'] : 'center center', 'right bottom'); ?>>Right Bottom</option>
                                            <option value="center top" <?php selected(isset($options['bg_position']) ? $options['bg_position'] : 'center center', 'center top'); ?>>Center Top</option>
                                            <option value="center bottom" <?php selected(isset($options['bg_position']) ? $options['bg_position'] : 'center center', 'center bottom'); ?>>Center Bottom</option>
                                        </select>

                                        <br>
                                        <label>
                                            <input type="checkbox" name="wp_alc_settings[remove_bg_image]" value="1">
                                            Remove background image
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Text Color</th>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="color" name="wp_alc_settings[text_color]" id="text-color-picker" value="<?php echo isset($options['text_color']) ? esc_attr($options['text_color']) : '#3c434a'; ?>">
                                <button type="button" id="reset-text-color" class="button button-secondary" style="height: 30px;">Reset to Default</button>
                                <input type="hidden" name="wp_alc_settings[reset_text_color]" id="reset-text-color-input" value="0">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Form Field Border Color</th>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="color" 
                                       name="wp_alc_settings[field_border_color]" 
                                       id="field-border-color-picker" 
                                       value="<?php echo isset($options['field_border_color']) ? esc_attr($options['field_border_color']) : '#dcdcde'; ?>">
                                <button type="button" id="reset-field-border-color" class="button button-secondary" style="height: 30px;">Reset to Default</button>
                                <input type="hidden" name="wp_alc_settings[reset_field_border_color]" id="reset-field-border-color-input" value="0">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Button Background Color</th>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="color" 
                                       name="wp_alc_settings[button_bg_color]" 
                                       id="button-bg-color-picker" 
                                       value="<?php echo isset($options['button_bg_color']) ? esc_attr($options['button_bg_color']) : '#2271b1'; ?>">
                                <button type="button" id="reset-button-bg-color" class="button button-secondary" style="height: 30px;">Reset to Default</button>
                                <input type="hidden" name="wp_alc_settings[reset_button_bg_color]" id="reset-button-bg-color-input" value="0">
                            </div>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>

        <style>
            .remove-logo:hover {
                background: #c82333 !important;
            }
        </style>

        <script>
            jQuery(document).ready(function($) {
                $('.remove-logo').on('click', function() {
                    $('#logo-preview').hide();
                    $('#remove-logo-input').val('1');
                });

                $('#reset-bg-color').on('click', function() {
                    $('#bg-color-picker').val('#ffffff');
                    $('#reset-bg-color-input').val('1');
                });

                $('#reset-text-color').on('click', function() {
                    $('#text-color-picker').val('#3c434a');
                    $('#reset-text-color-input').val('1');
                });

                $('#reset-field-border-color').on('click', function() {
                    $('#field-border-color-picker').val('#dcdcde');
                    $('#reset-field-border-color-input').val('1');
                });

                $('#reset-button-bg-color').on('click', function() {
                    $('#button-bg-color-picker').val('#2271b1');
                    $('#reset-button-bg-color-input').val('1');
                });
            });
        </script>
        <?php
    }

    public function init_settings() {
        register_setting('wp_alc_options', 'wp_alc_settings', array($this, 'handle_file_upload'));
    }

    public function handle_file_upload($options) {
        $existing_options = get_option('wp_alc_settings');

        // Check if user wants to remove logo
        if (isset($options['remove_login_logo']) && $options['remove_login_logo'] == 1) {
            unset($existing_options['login_logo']);
            // Merge back other options
            $options = array_merge($existing_options, $options);
            unset($options['remove_login_logo']);
            return $options;
        }

        // Check if user wants to remove background image
        if (isset($options['remove_bg_image']) && $options['remove_bg_image'] == 1) {
            // Preserve all existing options except background image
            $options = array_merge($existing_options, $options);
            unset($options['bg_image']);
            unset($options['remove_bg_image']);
            return $options;
        }

        // Check if background color reset is requested
        if (isset($options['reset_bg_color']) && $options['reset_bg_color'] == 1) {
            $options['bg_color'] = '#ffffff';
        }

        // Check if text color reset is requested
        if (isset($options['reset_text_color']) && $options['reset_text_color'] == 1) {
            $options['text_color'] = '#3c434a'; // WordPress default text color
        }

        // Handle color resets
        if (isset($options['reset_field_border_color']) && $options['reset_field_border_color'] == 1) {
            $options['field_border_color'] = '#dcdcde';
        }
        if (isset($options['reset_button_bg_color']) && $options['reset_button_bg_color'] == 1) {
            $options['button_bg_color'] = '#2271b1';
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
        
        // Always remove the checkbox values from options
        unset($options['remove_login_logo']);
        unset($options['remove_bg_image']);
        unset($options['reset_bg_color']);
        unset($options['reset_text_color']);
        unset($options['reset_field_border_color']);
        unset($options['reset_button_bg_color']);
        return $options;
    }

    public function enqueue_admin_scripts($hook) {
        if('toplevel_page_wp-admin-logo' !== $hook) {
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
                        background-size: <?php echo isset($options['bg_size']) ? esc_attr($options['bg_size']) : 'cover'; ?>;
                        background-position: <?php echo isset($options['bg_position']) ? esc_attr($options['bg_position']) : 'center center'; ?>;
                        background-repeat: <?php echo isset($options['bg_repeat']) ? esc_attr($options['bg_repeat']) : 'no-repeat'; ?>;
                    <?php endif; ?>

                    <?php if (isset($options['text_color'])): ?>
                        color: <?php echo esc_attr($options['text_color']); ?>;
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

                <?php if (isset($options['text_color'])): ?>
                    .login label,
                    .login #nav a,
                    .login #backtoblog a,
                    .login .message,
                    .login .privacy-policy-page-link a {
                        color: <?php echo esc_attr($options['text_color']); ?> !important;
                    }
                    
                    .login #nav a:hover,
                    .login #backtoblog a:hover,
                    .login .privacy-policy-page-link a:hover {
                        color: <?php echo esc_attr($options['text_color']); ?> !important;
                        opacity: 0.8;
                    }
                <?php endif; ?>

                /* Add new form field and button styles */
                .login form .input,
                .login input[type="text"],
                .login input[type="password"] {
                    <?php if (isset($options['field_border_color'])): ?>
                    border-color: <?php echo esc_attr($options['field_border_color']); ?> !important;
                    <?php endif; ?>
                }

                .login form .input:focus,
                .login input[type="text"]:focus,
                .login input[type="password"]:focus {
                    <?php if (isset($options['field_border_color'])): ?>
                    border-color: <?php echo esc_attr($options['field_border_color']); ?> !important;
                    box-shadow: 0 0 0 1px <?php echo esc_attr($options['field_border_color']); ?> !important;
                    <?php endif; ?>
                }

                .login .button-primary {
                    <?php if (isset($options['button_bg_color'])): ?>
                    background: <?php echo esc_attr($options['button_bg_color']); ?> !important;
                    border-color: <?php echo esc_attr($options['button_bg_color']); ?> !important;
                    <?php endif; ?>
                }

                .login .button-primary:hover,
                .login .button-primary:focus, 
                 {
                    <?php if (isset($options['button_bg_color'])): ?>
                    background: <?php echo esc_attr($this->adjust_brightness($options['button_bg_color'], -20)); ?> !important;
                    border-color: <?php echo esc_attr($this->adjust_brightness($options['button_bg_color'], -20)); ?> !important;
                    <?php endif; ?>
                }
                .login .wp-hide-pw, .login .wp-hide-pw:hover {
                    <?php if (isset($options['button_bg_color'])): ?>
                    color: <?php echo esc_attr($this->adjust_brightness($options['button_bg_color'], -20)); ?> !important;
                    <?php endif; ?>
                    outline: none !important;
                }
            </style>
            <?php
        }
    }

    private function adjust_brightness($hex, $steps) {
        // Remove # if present
        $hex = ltrim($hex, '#');
        
        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Adjust brightness
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        // Convert back to hex
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}

// Initialize plugin
add_action('plugins_loaded', function() {
    new WP_Admin_Logo_Customization();
});


