<?php

namespace BoxyBird\Waffle;

class Guard
{
    /**
     * The current user.
     *
     */
    protected $user;

    public function __construct()
    {
        $this->user = wp_get_current_user();
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return is_user_logged_in();
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !is_user_logged_in();
    }

    /**
     * Get the currently authenticated user.
     *
     */
    public function user()
    {
        if (!$this->check()) {
            return null;
        }

        return (array) $this->user->data;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id()
    {
        if (!$this->check()) {
            return null;
        }

        return $this->user->ID;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return wp_authenticate_username_password($this->user, $credentials['username'], $credentials['password']);
    }
}
