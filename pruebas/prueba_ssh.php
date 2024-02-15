<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$connection = ssh2_connect('192.168.100.75',22);
ssh2_auth_password($connection,'root','www.mch.1920..,');

$stream = ssh2_exec($connection,'./prueba.sh');

 stream_set_blocking($stream,true);
 $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
 echo stream_get_contents($stream_out);

?>