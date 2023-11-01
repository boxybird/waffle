<?php

if (isset($_POST['waffle_cache_flush']) && wp_verify_nonce($_POST['_wpnonce'], 'waffle_cache_flush')) {
    waffle_cache()->flush();

    echo '<div class="notice notice-success is-dismissible" style="margin-left: 0;"><p>Cache flushed!</p></div>';
}
?>

<div class="wrap">
    <h3>Waffle Options</h3>

    <form method="POST">
        <input type="hidden" name="waffle_cache_flush" value="1">
        <input type="submit" value="Flush Cache">
        <?php wp_nonce_field('waffle_cache_flush'); ?>
    </form>
</div>