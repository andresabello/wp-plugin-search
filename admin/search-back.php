<?php
/**
 * Add admin functionality here, e.g., the button to send data to the REST API.
 * Register the admin menu page
 * 
 * @return void
 */
function pi_search_admin_menu()
{
    add_options_page(
        'Pi Search',
        'Search Settings',
        'manage_options',
        'pi-search-settings',
        'pi_search_settings'
    );
}

add_action('admin_menu', 'pi_search_admin_menu');

/**
 * Define the options page content.
 *
 * @return void
 */
function pi_search_settings()
{
    // TODO: Allow client to choose the post types.
    ?>
    <div class="wrap">
        <h2>Pi Search Settings</h2>
        <div class="notice notice-info">
            <p>Click the button to collect Post Title, Post Content, Post Excerpt and Featured Image from all posts.
                <br>Including Posts, Pages and Custom Post Types.
                <br><br><strong>Coming soon - The ability to choose the post types you want to collect.</strong>
            </p>
        </div>
        <form>
            <input type="button" class="button button-primary" value="Add Posts to Search" id="pi-ingest-button">
        </form>
    </div>
    <?php
}

/**
 * send_post_request_to_remote_server
 *
 * @return void
 */
function send_post_request_to_remote_server()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pi_nonce')) {
        wp_send_json_error(['message' => "Invalid nonce"], 404);
    }

    // Dev URL
    // $api_url = 'http://host.docker.internal:8085/ingest';

    // Production URL
    $api_url = 'http://search.piboutique.com/ingest';

    $request_data = [
        'website' => home_url(),
    ];

    $request_args = [
        'body' => $request_data,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ];

    $response = wp_remote_get($api_url, $request_args);

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        wp_send_json_error(['message' => "Error: $error_message"]);
        wp_die();
    }

    $http_code = wp_remote_retrieve_response_code($response);
    if ($http_code === 200) {
        $body = wp_remote_retrieve_body($response);
        wp_send_json_success(['message' => "Success: $body"]);
        wp_die();
    }

    wp_send_json_error(['message' => "Non-200 response: $http_code"]);
}
add_action('wp_ajax_send_data_to_search_service', 'send_post_request_to_remote_server');
add_action('wp_ajax_nopriv_send_data_to_search_service', 'send_post_request_to_remote_server');


/**
 * enqueue_custom_scripts
 *
 * @return void
 */
function enqueue_custom_scripts()
{
    wp_enqueue_script(
        'ingest-script',
        pi_find_asset('admin.js'),
        [],
        '1.0.0',
        true
    );
    wp_localize_script(
        'ingest-script',
        'ajax_object',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce("pi_nonce")
        ]
    );
}
add_action('admin_enqueue_scripts', 'enqueue_custom_scripts');