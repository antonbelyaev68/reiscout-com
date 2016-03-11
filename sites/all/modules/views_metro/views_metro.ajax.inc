<?php
function views_metro_request() {
  $action = isset($_POST['action']) ? $_POST['action'] : NULL;
  switch($action) {
    case 'load':
      break;
    case 'save':
      break;
  }
  exit;
}
