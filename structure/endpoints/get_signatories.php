<?php

add_action('rest_api_init', function () {

    register_rest_route('api', 'signatories', array(
        'methods' => 'GET',
        'callback' => 'get_signatories',
        'permission_callback' => '__return_true',
    ));
});

function get_signatories(WP_REST_Request $request)
{
    $body = $request->get_query_params();

    $data = get_signatories_posts($body);

    wp_send_json_success($data, 200);
}
