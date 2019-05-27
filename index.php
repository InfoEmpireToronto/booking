<?php
require('basicsite/init.php');
if(!$loggedIn)
  header('Location: login.php');
else
  header('Location: appointments.php');
?>
