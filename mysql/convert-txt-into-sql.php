<?php

$reading = fopen('path/to/file', 'r');
$writing = fopen('temp-file.tmp', 'w');

$replaced = false;

while (!feof($reading)) {

  // get the current line and extract the data
  $line = fgets($reading);  

  // in this example we know the line data pattern, so we get the desired segments/pieces
  $content = explode(";", $line);
  $email = $content[0];
  $new_id = preg_replace( "/\r|\n/", "", $content[1] );

  // example of change in line using original line data to create sql statement line
  $line_sql = "update wp_usermeta set meta_value = '$new_id' where meta_key = 'tyk_user_id' and user_id = (select ID from wp_users where user_email = '$email');\n";
  fputs($writing, $line_sql);
  $replaced = true;
  
}
fclose($reading); fclose($writing);
// might as well not overwrite the file if we didn't replace anything
if ($replaced) 
{
  rename('temp-file.tmp', 'path/to/output-file.sql');
} else {
  unlink('temp-file.tmp');
}