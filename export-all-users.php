<?php

/**
 * Plugin Name: Export All Users (Temporary Tool)
 * Plugin URI:  https://easeare.com/
 * Author:      Junayed
 * Description: A plugin that exposes all users + metadata publicly is very dangerous if it stays active — it’s basically handing out your database to anyone who knows the URL.
 * Version:     1.0
 */

add_action('rest_api_init', function () {
    register_rest_route('devplugin/v1', '/users', [
        'methods'             => 'GET',
        'callback'            => 'devplugin_get_all_users_with_meta',
        'permission_callback' => '__return_true', // No authentication required
    ]);
});

function devplugin_get_all_users_with_meta(WP_REST_Request $request) {
    $users = get_users([
        'fields' => 'all', // fetch full WP_User objects
        'number' => -1
    ]);

    $data = [];

    foreach ($users as $user) {
        $meta = get_user_meta($user->ID); // returns ALL meta keys and values

        $clean_meta = [];
        foreach ($meta as $key => $value) {
            $clean_meta[$key] = (count($value) === 1) ? $value[0] : $value;
        }

        $data[] = [
            'ID'           => $user->ID,
            'username'     => $user->user_login,
            'email'        => $user->user_email,
            'display_name' => $user->display_name,
            'roles'        => $user->roles,
            'meta'         => $clean_meta,
        ];
    }

    return $data;
}
