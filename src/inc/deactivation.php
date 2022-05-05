<?php

// Delete database table check transient
delete_transient('waffle_sessions_table_exists');

// Delete the session database table
if (waffle_db()->schema()->hasTable('waffle_sessions')) {
    waffle_db()->schema()->drop('waffle_sessions');
}
