<?php
defined('ABSPATH') or die;
/**
 * @var $app_dashboard_url string
 * @var $is_app_connected string
 * @var $app_id string
 */
?>
<table class="form-table">
    <tbody>
    <tr>
        <th scope="row" class="app_id">
            <label for="<?php echo esc_attr( OPTINLY_SLUG ) ?>_app_id"><?php _e('Enter your App ID', OPTINLY_TD); ?></label>
        </th>
        <td class="forminp forminp-text">
            <input type="text" name="app_id" id="<?php echo esc_attr(OPTINLY_SLUG) ?>_app_id" class="regular-text" value="<?php echo esc_attr($app_id); ?>">
            <p class="optinly-field-description">
                <?php _e('Get your App-id ', OPTINLY_TD); ?>
                <a target="_blank"
                   href="<?php echo esc_url($app_dashboard_url); ?>"><?php _e('here', OPTINLY_TD); ?></a>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row">
        </th>
        <td>
            <div>
                <button type="button" class="button button-primary"
                        id="<?php echo esc_attr(OPTINLY_SLUG) ?>_validate_app_id_btn"><?php ($is_app_connected == 1) ? _e('Re-Connect', OPTINLY_TD) : _e('Connect', OPTINLY_TD); ?></button>
                <button type="button" class="button"
                        id="<?php echo esc_attr(OPTINLY_SLUG) ?>_disconnect_app_btn"
                        style="<?php echo ($is_app_connected == 0) ? esc_attr('display:none') : esc_attr(''); ?> "><?php _e('Dis-Connect', OPTINLY_TD); ?></button>
                <div id="optinly-loader-container">
                    <div class="optinly-load-spinner"></div>
                </div>
                <div id="optinly-api-response-container" class="optinly-api-response-container">
                    <?php
                    if (($is_app_connected == 1)) {
                        ?>
                        <span style="color: green"><?php _e('App successfully connected to Optinly!'); ?></span>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>