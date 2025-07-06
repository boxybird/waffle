<?php

namespace BoxyBird\Waffle;

use WP_Error;
use WP_User;

class Guard
{
    /**
     * The current user.
     *
     */
    protected ?WP_User $user = null;

    public function __construct()
    {
        $this->user = wp_get_current_user();
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check(): bool
    {
        return is_user_logged_in();
    }

    /**
     * Determine if the current user is a guest.
     */
    public function guest(): bool
    {
        return !is_user_logged_in();
    }

    /**
     * Get the currently authenticated user.
     *
     */
    public function user(): ?array
    {
        if (!$this->check()) {
            return null;
        }

        return (array) $this->user->data;
    }

    /**
     * Get the ID for the currently authenticated user.
     */
    public function id(): ?int
    {
        if (!$this->check()) {
            return null;
        }

        return $this->user->ID;
    }

    /**
     * Validate a user's credentials.
     */
    public function validate(array $credentials = []): WP_Error|WP_User
    {
        return wp_authenticate_username_password($this->user, $credentials['username'], $credentials['password']);
    }
}
