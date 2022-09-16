<form method="POST">
    <input type="hidden" name="waffle_cache_flush" value="1">
    <input type="submit" value="Flush Cache">
    <?php wp_nonce_field('waffle_cache_flush'); ?>
</form>