<?php

function pi_autocomplete_shortcode()
{
    ob_start(); ?>

    <!-- Autocomplete HTML, CSS, and JavaScript -->
    <div class="pi-autocomplete">
        <input id="pi-search" type="text" placeholder="Enter your search">
        <div class="pi-autocomplete-items" id="pi-search-items"></div>
        <div class="loader" id="loader"></div>
    </div>
    <?php

    return ob_get_clean();
}

// Register the shortcode
add_shortcode('pi_search', 'pi_autocomplete_shortcode');


/**
 * enqueue_custom_scripts
 *
 * @return void
 */
function enqueue_pi_search_scripts()
{
    wp_enqueue_style(
        'search-styles',
        pi_find_asset('search.css'),
        [],
        '1.0.0'
    );

    wp_enqueue_script(
        'search-script',
        pi_find_asset('search.js'),
        [],
        '1.0.0',
        true
    );
    wp_localize_script(
        'search-script',
        'ajax_object',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pi_nonce')
        ]
    );
}
add_action('wp_enqueue_scripts', 'enqueue_pi_search_scripts');


/**
 * send_post_request_to_remote_server
 *
 * @return void
 */
function pi_get_posts_from_query()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pi_nonce')) {
        wp_send_json_error(['message' => "Invalid nonce"], 404);
    }

    // TODO: The API URL will need to handle local, dev and prod
    $api_url = 'http://host.docker.internal:8085/search';
    $request_data = [
        'query' => $_POST['query'] ?? null,
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
    if ($http_code !== 200) {
        wp_send_json_error(['message' => "Non-200 response: $http_code"]);
    }

    $body = wp_remote_retrieve_body($response);
    $body = json_decode($body, true);
    wp_send_json_success($body);
    wp_die();
}
add_action('wp_ajax_pi_search_posts', 'pi_get_posts_from_query');
add_action('wp_ajax_nopriv_pi_search_posts', 'pi_get_posts_from_query');

