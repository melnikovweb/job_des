<?php

defined('ABSPATH') || exit;

class SignatoryRole
{

    private static $role = 'signatory';
    private static $display_name = 'Signatory';

    public static function setup()
    {
        add_action('admin_init', [__CLASS__, 'createRole']);
        add_action('admin_init', [__CLASS__, 'clearCache']);
        add_action('pre_get_posts', [__CLASS__, 'querySetOnlyAuthor'], PHP_INT_MAX);
        add_action('admin_menu', [__CLASS__, 'accessControl'], 10);
    }

    public static function createRole()
    {
        if (!RoleUtils::roleExists(self::$role)) {
            self::addNewRole();
        }
    }

    private static function addNewRole()
    {
        $editor = RoleUtils::getWpRolesObject()->get_role('subscriber');

        // Add a new role with editor caps
        $new_role = RoleUtils::getWpRolesObject()->add_role(
            self::$role,
            self::$display_name,
            $editor->capabilities
        );

        // Additional capabilities which this role should have
        $additionalCapabilities = [
            'read',
            'publish',
            'delete',
            'delete_private',
            'delete_published',
            'edit',
            'edit_private',
            'edit_published',
            'read_private',
        ];

        foreach ($additionalCapabilities as $cap) {
            $new_role->add_cap("{$cap}_signatoryReports");
        }
    }

    public static function accessControl()
    {
        $user = wp_get_current_user();

	    if ($user->roles[0] === self::$role) {
            $access_capabilities = [
                'index.php',
            ];
    
            global $pagenow;
    
            if (in_array($pagenow, $access_capabilities)) {
                if (function_exists('admin_url')) {
                    wp_redirect(admin_url('/edit.php?post_type=signatory-report'));
                }
            }

            remove_menu_page('index.php');
        }
    }

    public static function clearCache()
    {
        // deletes the default cache for normal Post. (1)
        $cache_key = _count_posts_cache_key('signatory-report', 'readable');

        wp_cache_delete($cache_key, 'counts');
    }

    public static function querySetOnlyAuthor($wp_query)
    {
        if (!is_admin()) {
            return;
        }

        $allowed_types = ['signatory-report'];
        $current_type  = get_query_var('post_type', 'post');

        if (in_array($current_type, $allowed_types, true)) {
            $post_type_object = get_post_type_object($current_type);

            if (!current_user_can($post_type_object->cap->edit_others_posts)) {
                $wp_query->set('author', get_current_user_id());

                add_filter('wp_count_posts', [__CLASS__, 'fixCountOrders'], PHP_INT_MAX, 3);
            }
        }
    }

    public static function fixCountOrders($counts, $type, $perm)
    {
        global $wpdb;

        if (!post_type_exists($type)) {
            return new stdClass();
        }

        $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";

        $post_type_object = get_post_type_object($type);

        // adds condition to respect `$perm`. (3)
        if ($perm === 'readable' && is_user_logged_in()) {
            if (!current_user_can($post_type_object->cap->read_private_posts)) {
                $query .= $wpdb->prepare(
                    " AND (post_status != 'private' OR ( post_author = %d AND post_status = 'private' ))",
                    get_current_user_id()
                );
            }
        }

        // limits only author's own posts. (6)
        if (is_admin() && !current_user_can($post_type_object->cap->edit_others_posts)) {
            $query .= $wpdb->prepare(' AND post_author = %d', get_current_user_id());
        }

        $query .= ' GROUP BY post_status';

        $results = (array) $wpdb->get_results($wpdb->prepare($query, $type), ARRAY_A);
        $counts  = array_fill_keys(get_post_stati(), 0);

        foreach ($results as $row) {
            $counts[$row['post_status']] = $row['num_posts'];
        }

        $counts    = (object) $counts;
        $cache_key = _count_posts_cache_key($type, 'readable');

        wp_cache_set($cache_key, $counts, 'counts');

        return $counts;
    }
}

SignatoryRole::setup();
