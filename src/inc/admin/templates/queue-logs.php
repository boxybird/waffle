<form method="POST">
    <input type="hidden" name="waffle_queue_logs_delete" value="1">
    <input type="submit" value="Delete All Logs (<?= $logs_count; ?>)">
    <?php wp_nonce_field('waffle_queue_logs_delete'); ?>
</form>

<table class="widefat striped" style="margin-top: 1rem;">
  <tr>
    <th>Latest <?= $latest; ?> Exceptions</th>
    <th>Queue</th>
    <th>Failed</th>
  </tr>
  <?php foreach ($logs as $log): ?>
    <tr>
      <td><?= $log->exception; ?></td>
      <td><?= $log->queue; ?></td>
      <td><?= $log->failed_at; ?></td>
    </tr>
  <?php endforeach; ?>
</table>