/* global jQuery, ajaxurl, wdr_data */
jQuery(document).ready(function ($) {
    let response_container = $('#optinly-api-response-container');
    let loader_icon = $('#optinly-loader-container');
    let disconnect_btn = $('#' + optinly_admin_data.slug + '_disconnect_app_btn');
    let connect_btn = $('#' + optinly_admin_data.slug + '_validate_app_id_btn');
    let optinly_wpNonce = $.parseHTML(optinly_admin_data.nonce);
    let OriginDetail = window.location.host == "localhost" ? `${window.location.origin}/wordpress/wp-json/optinly/v1/`: `${window.location.origin}/wp-json/optinly/v1/`

    /**
     * When user clicks connect or re-connect button
     */
    $(document).on('click', '#' + optinly_admin_data.slug + '_validate_app_id_btn', function () {
        let app_id = $('#' + optinly_admin_data.slug + '_app_id');
        $.ajax({
            url:OriginDetail+`connectApi` ,
            type: 'POST',
            dataType: "json",
            async: true,
            headers:{
                "app-id": app_id.val(),
                "x-wb-nonce": optinly_wpNonce
            },

            beforeSend: function () {
                manageBeforeAjaxCall();
            },
            success: function (response) {
                localStorage.setItem("opt-app-id",app_id.val());
                manageConnectResponse(response);
            },
            error: function () {
                manageError();
            }
        });
    });

    /**
     * When user clicks disconnect button
     */
    $(document).on('click', '#' + optinly_admin_data.slug + '_disconnect_app_btn', function () {
        $.ajax({
            url: OriginDetail+`disconnect`,
            type: 'POST',
            dataType: "json",
            async: true,
            headers:{
                "app-id": localStorage.getItem('opt-app-id'),
                "x-wb-nonce": optinly_wpNonce
            },
            beforeSend: function () {

                manageBeforeAjaxCall();
            },
            success: function (response) {
                manageDisConnectResponse(response);
            },
            error: function () {
                manageError();
            }
        });
    });
    $(document).on('submit', '#optinly-settings-form', function (e) {
        e.preventDefault();
        let button = $(this).find('#submit');
        $.ajax({
            url: OriginDetail+`settings`,
            type: 'POST',
            dataType: "json",
            async: true,
            headers:{
                "x-wb-nonce": optinly_wpNonce,
                "app-id": localStorage.getItem('opt-app-id'),
                "app-secret-key":e.target.app_secret_key,
            },
            // data: $(this).serialize(),
            beforeSend: function () {
                button.val('Saving');
                button.attr('disabled',true);
            },
            success: function (response) {
                if(response.success){
                    button.val('Save changes');
                    button.attr('disabled',false);
                    alert(response.data);
                }
            },
            error: function () {
                button.val('Save changes');
                button.attr('disabled',false);
            }
        });
    });

    /**
     * Manage response from the connection controller
     * @param response
     */
    function manageConnectResponse(response) {
        hideLoader();
        if (response.success) {
            disconnect_btn.show();
            connect_btn.html(optinly_admin_data.reconnect_btn_txt);
            let message = '<span style="color:green;">' + response.data + '</span>';
            response_container.html(message);
        } else {
            let message = '<span style="color:red;">' + response.data + '</span>';
            response_container.html(message);
        }
    }

    /**
     * Manage response from the dis-connection controller
     * @param response
     */
    function manageDisConnectResponse(response) {
        hideLoader();
        if (response.success) {
            disconnect_btn.hide();
            connect_btn.html(optinly_admin_data.connect_btn_txt);
            let message = '<span style="color:green;">' + response.data + '</span>';
            response_container.html(message);
        } else {
            let message = '<span style="color:red;">' + response.data + '</span>';
            response_container.html(message);
        }
    }

    /**
     * Manage app on error
     */
    function manageError() {
        hideLoader();
        let message = '<span style="color:red;">Sorry can\'t find error</span>';
        response_container.html(message);
    }

    /**
     * Things to be done before ajax call
     */
    function manageBeforeAjaxCall() {
        showLoader();
        disconnect_btn.hide();
        response_container.html('');
    }

    /**
     * Hide the loader
     */
    function hideLoader() {
        loader_icon.hide();
    }

    /**
     * Hide the loader
     */
    function showLoader() {
        loader_icon.css('display', 'inline-block');
    }
});