<?php
$webapp = file_get_contents("/var/www/webapp/index.html");
$webapp = str_replace("=static/", "=/static/", $webapp);
$webapp = str_replace("{{title}}", "Fazendo as Malas", $webapp);
echo $webapp;