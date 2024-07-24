<?php

add_action('rest_api_init', function () {

    register_rest_route('api', 'chapter', array(
        'methods' => 'GET',
        'callback' => 'get_chapter',
        'permission_callback' => '__return_true',
    ));
});

function get_chapter(WP_REST_Request $request)
{
    $body = $request->get_query_params();

    $data = get_chapter_post($body);

    wp_send_json_success($data, 200);
}
