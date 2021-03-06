<?php
class main {
  public static function initDB() {
    if(!class_exists('sqliteDB')) {
      main::log('ERROR', 'sqliteDB class not loaded', true);
    }
    
    if(!is_file(wm_db_file)) {
      if(!fclose(fopen(wm_db_file, 'w'))) {
        main::log('ERROR', sprintf('can\' create db file %s', wm_db_file));
      }
    }
    
    $db = new sqliteDB(wm_db_file);
    $db_connect = $db->connect();
    if(!($db_connect['status'] == 'OK')) {
      main::log('ERROR', 'database connection failed', true);
    }
    
    $query = "CREATE TABLE IF NOT EXISTS 'sites' (
      `id`	INTEGER PRIMARY KEY AUTOINCREMENT,
      `hostname`	TEXT UNIQUE,
      `template_id`	INTEGER,
      `template_data`	TEXT,
      `pinged`	INTEGER DEFAULT '0'
    );";
    $db_query = $db->query($query);
    if($db_query['status'] != 'OK') {
      main::log('ERROR', $db_query['message'], true);
    }
    
    $query = "CREATE TABLE IF NOT EXISTS `templates` (
      `id`	INTEGER PRIMARY KEY AUTOINCREMENT,
      `name`	TEXT,
      `path`	TEXT
    );";
    $db_query = $db->query($query);
    if($db_query['status'] != 'OK') {
      main::log('ERROR', $db_query['message'], true);
    }
  }
  
  public static function loadLibs($libList) {
    foreach($libList as $lib) {
      $libFile = wm_lib_path.$lib;
      if(!is_file($libFile)) {
        main::log('ERROR', 'inexistent lib file: '.$lib, true);
      }
      
      require($libFile);
    }
  }
  
  public static function loadAct($act) {
    if(!$act) {
      return [false, 'act not defined'];
    }
    
    if(!class_exists('actContext')) {
      require(wm_acts_path.'actContext.php');
    }
    
    $actPath = wm_acts_path.$act.'.php';
    if(!is_file($actPath)) {
      return [false, 'undefined act module'];
    }
    
    require($actPath);
    
    if(!class_exists($act)) {
      return [false, $act.' class not defined'];
    }
    
    if(!method_exists($act, 'exec')) {
      return [false, 'exec method not defined for '.$act];
    }
    
    return [true, 'loaded act class file: '.$act];
  }
  
  public static function response($status, $message, $data = false,
    $cntType = 'text/plain') {
    return json_encode([
      'status' => $status,
      'message' => $message,
      'data' => $data,
      'content_type' => $cntType
    ]);
  }
  
  public static function log($type, $message, $exception = false) {
    if(!is_dir(wm_log_path)) {
      if(!mkdir(wm_log_path)) {
        throw new Exception('log path does not exist');
      }
    }
    
    $type = strtoupper($type);
    if(!(in_array($type, ['OK', 'ERROR']))) {
      throw new Exception('invalid log type');
    }
    
    $logMessage = sprintf('[%s] %s'."\n", date('Y m d H:i:s'), $message);
    $logFile = wm_log_path.$type.'.log';
    
    if(file_exists($logFile)) {
      if(filesize($logFile) > (5 * (pow(10, 6)))) {
        rename($logFile, $logFile.'_'.time());
      }
    }
    
    if(!file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX)) {
      throw new Exception('could not write to log file: '.$logFile);
    }
    
    if($exception) {
      throw new Exception($message);
    }
  }
}
?>
