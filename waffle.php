<?php

/**
 * Plugin Name:       Waffle
 * Plugin URI:        #
 * Description:       Tools built using Illuminate components (https://github.com/illuminate). Inspired by https://github.com/mattstauffer/Torch.
 * Version:           0.2.5
 * Author:            Andrew Rhyand
 * Author URI:        andrewrhyand.com
 * License:           MIT License
 * License URI:       https://opensource.org/licenses/MIT
 */

require_once __DIR__ . '/src/inc/setup.php';

register_deactivation_hook(__FILE__, function () {
    require_once __DIR__ . '/src/inc/deactivation.php';
});
