<div style="display: flex; align-items: center; justify-content: space-between;">
  <strong>Latest <?= $latest <= $logs_count ? $latest : $logs_count ; ?> Logs</strong>
  <form method="POST">
    <input type="hidden" name="waffle_queue_logs_delete" value="1">
    <input type="submit" value="Delete All Logs (<?= $logs_count; ?>)">
    <?php wp_nonce_field('waffle_queue_logs_delete'); ?>
  </form>
</div>

<table class="widefat fixed striped" style="margin-top: 1rem;">
  <tr>
    <th>Exception</th>
    <th>Payload</th>
    <th>Queue</th>
    <th>Failed</th>
  </tr>
  <?php foreach ($logs as $log): ?>
    <tr>
      <td><?= $log->exception; ?></td>
      <td><?= $log->payload; ?></td>
      <td><?= $log->queue; ?></td>
      <td><?= $log->failed_at; ?></td>
    </tr>
  <?php endforeach; ?>
</table>