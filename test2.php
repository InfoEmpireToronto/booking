<?php
$payload = json_decode(file_get_contents('php://input')); 
$out['res'] = $payload->firstName;
echo json_encode($out);
// echo "yep..";
?>