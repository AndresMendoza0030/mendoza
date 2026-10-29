<?php

return [
    // Separate server-side credentials. Never expose either value to a browser.
    'token' => env('WORKSPACE_INVENTORY_TOKEN'),
    'access_url' => env('WORKSPACE_ACCESS_URL'),
    'permission_token' => env('WORKSPACE_PERMISSION_TOKEN'),
    'access_client_id' => env('WORKSPACE_ACCESS_CLIENT_ID'),
    'access_client_secret' => env('WORKSPACE_ACCESS_CLIENT_SECRET'),
    'timeout' => 5,
    'connect_timeout' => 3,

    // Activate only after configuring the callback and granting existing users.
    // Required for revocation to cover direct Laravel sessions and Livewire.
    'enforce_web_access' => env('WORKSPACE_INVENTORY_ENFORCE_WEB_ACCESS', false),
];
