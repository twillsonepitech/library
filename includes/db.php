<?php

DEFINE('DB_USER', 'root');
DEFINE('DB_PASSWORD', '');
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_NAME', 'library');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function debug_to_console($data)
{
  $output = $data;

  if (is_array($output)) {
    foreach ($output as $key => $value) {
      echo "<script>console.log('Debug Objects: " . $key . " => " . $value . "' );</script>";
    }
  }
  if (is_array($output)) {
    $output = implode(',', $output);
  }
  echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function validate_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
