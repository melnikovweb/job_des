<?php

add_action('rest_api_init', function () {

    register_rest_route('api', 'commitment', array(
        'methods' => 'GET',
        'callback' => 'get_commitment',
        'permission_callback' => '__return_true',
    ));
});

function get_commitment(WP_REST_Request $request)
{
    $body = $request->get_query_params();

    $data = get_commitment_post($body);

    wp_send_json_success($data, 200);
}
