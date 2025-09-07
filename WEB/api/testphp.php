<?php
// api/test_curl.php
header('Content-Type: text/plain');
$ch = curl_init('https://api.ipify.org?format=text');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
if($res === false){
  echo "cURL lỗi: " . curl_error($ch);
} else {
  echo "cURL OK, IP public của server: $res";
}
