<?php
defined('ABSPATH') or die;
/**
 * @var $is_mailpoet_enabled string
 * @var $app_secret_key string
 * @var $mailpoet_webhook string
 * @var $mailpoet_list_id array
 * @var $mailpoet_lists array
 */
?>
<form id="optinly-settings-form">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row" class="app_id">
                <label for="<?php echo OPTINLY_SLUG ?>app_secret_key"><?php _e('App secret key', OPTINLY_TD); ?></label>
            </th>
            <td class="forminp forminp-text">
                <input type="text" name="app_secret_key" class="regular-text"
                       id="<?php echo OPTINLY_SLUG ?>app_secret_key"
                       placeholder="Enter secret key" 
                       value="<?php echo esc_attr($app_secret_key) ?>">
                <p>
                    You need to enter the above secret key in your optinly dashboard
                </p>
            </td>
        </tr>
        </tbody>
    </table>
    <p class="submit">
        <input type="hidden" name="action" value="save_optinly_settings">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(OPTINLY_SLUG . '_save_settings') ?>">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
</form>