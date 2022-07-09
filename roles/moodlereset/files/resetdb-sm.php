<?php
shell_exec("mysql -u rpi64box -pRpi64box4$ -e 'RESET QUERY CACHE;'");
shell_exec("mysql -u rpi64box -pRpi64box4$ moodle < /home/rpi64box/moodle-testplan-sm.sql && echo 'Database Reset Complete'");
?>