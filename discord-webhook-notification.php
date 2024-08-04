<?php
/*
Plugin Name: Discord Webhook Notification
Description: Sends a Discord notification when a new post is published.
Author: Siim Aarmaa
Version: 1.0
*/

function send_discord_notification($post_ID, $post) {
    // Ensure this code only runs once per post
    if (get_transient("discord_notification_sent_{$post_ID}")) {
        return;
    }

    // Replace with your Discord webhook URL
    $webhook_url = 'YOUR_DISCORD_WEBHOOK_URL';

    // Get post details
    $post_title = $post->post_title;
    $post_url = get_permalink($post_ID);
    $post_author = get_the_author_meta('display_name', $post->post_author);

    // Prepare the message
    $message = [
        "username" => "WordPress Bot", // Optional: Set a custom username for the bot
        "content" => "**New Post Published**\n\n**Title:** {$post_title}\n**Author:** {$post_author}\n**URL:** {$post_url}"
    ];

    // Encode the message as JSON
    $json_data = json_encode($message);

    // Use wp_remote_post to send the webhook
    $response = wp_remote_post($webhook_url, [
        'method'    => 'POST',
        'headers'   => [
            'Content-Type' => 'application/json',
        ],
        'body'      => $json_data,
    ]);

    // Check for errors
    if (is_wp_error($response)) {
        error_log('Discord webhook failed: ' . $response->get_error_message());
    } else {
        error_log('Discord webhook sent successfully.');
        // Set a transient to indicate the notification has been sent
        set_transient("discord_notification_sent_{$post_ID}", true, 12 * HOUR_IN_SECONDS);
    }
}

// Hook the function to the 'publish_post' action
add_action('publish_post', 'send_discord_notification', 10, 2);
?>
