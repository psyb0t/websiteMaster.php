<?php
header('Content Type: text/plain');

define('wm_absolute_path', dirname(__FILE__).'/');
define('wm_lib_path', wm_absolute_path.'lib/');
define('wm_acts_path', wm_absolute_path.'acts/');
define('wm_templates_path', wm_absolute_path.'templates/');
define('wm_log_path', wm_absolute_path.'log/');
define('wm_db_file', wm_absolute_path.'db.sqlite');

require(wm_absolute_path.'main.php');
require(wm_absolute_path.'template.php');
require(wm_absolute_path.'utils.php');

$inputData = trim(file_get_contents('php://input'));
if(!$inputData) {
  main::log(
    'ERROR', sprintf(
      '[%s] no input data provided', $_SERVER['REMOTE_ADDR']
    ), true
  );
}

$inputData = json_decode($inputData, true);
if(!($inputData && is_array($inputData))) {
  main::log('ERROR', sprintf(
    '[%s] invalid input data', $_SERVER['REMOTE_ADDR']
  ), true);
}

if(!utils::keysOk($inputData, ['act', 'data'])) {
  main::log('ERROR', sprintf(
    '[%s] required keys not set', $_SERVER['REMOTE_ADDR']
  ), true);
}

$act = $inputData['act'];
$data = $inputData['data'];

main::loadLibs([
  'sqliteDB/sqliteDB.class.php'
]);

main::initDB();

list($laBool, $laMsg) = main::loadAct($act);
if(!$laBool) {
  main::log('ERROR', $lsMsg, true);
}

$actOb = new $act($data);
print_r($actOb->exec());
?>
