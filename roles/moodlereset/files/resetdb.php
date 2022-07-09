<?php
shell_exec("sudo -u www-data /usr/bin/php /var/www/moodle/admin/cli/purge_caches.php && mysql -u rpi64box -pRpi64box4$ moodle < /home/rpi64box/moodle-courses.sql && echo 'Database Reset Complete'");
?>