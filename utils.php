<?php
class utils {
  /*
   * Check object for a list of keys
   */
  public static function keysOk($obj, $keyArr) {
    $badCall = (!$obj || !$keyArr || !is_array($obj) ||
      !is_array($keyArr) || count($keyArr) == 0);
    
    if($badCall) {
      return false;
    }
    
    foreach($keyArr as $key) {
      if(!array_key_exists($key, $obj)) {
        return false;
      }
    }
    
    return true;
  }
  
  /*
   * Check url is valid and is seo friendly
   */
  public static function validUrl($url) {
    $regexpr = '/^(http|https):\/\/([a-z0-9\/.\-=_?!#]+)(?<!\.php)$/i';
    preg_match($regexpr, $url, $match);
    if(!$match) {
      return false;
    }
    
    if(strrpos($url, '../') !== false) {
      return false;
    }
    
    return true;
  }
  
  /*
   * Get database entry object
   */
  public static function dbObject($dbRes) {
    $rows = [];
    foreach($dbRes as $row) {
      foreach(array_keys($row) as $key) {
        if(is_numeric($key)) {
          unset($row[$key]);
        }
      }
      
      $rows[] = $row;
    }
    
    return $rows;
  }
  
  /*
   * Get mime-type of a file
   */
  public static function fileMimeType($filename) {
    if(!is_file($filename)) {
      return false;
    }
    
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    switch($ext) {
      case 'css':
        return 'text/css';
      case 'js':
        return 'text/javascript';
      default:
        return shell_exec('/usr/bin/env file -b --mime-type '.$filename);
    }
  }
}
?>
