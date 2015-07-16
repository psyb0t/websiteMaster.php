<?php
class actContext {
  public $data = false;
  
  function __construct($data) {
    if(!$data) {
      main::log('ERROR', 'data not defined', true);
    }
    
    if(!is_array($data)) {
      main::log('ERROR', 'data is not an array', true);
    }
    
    $this->data = $data;
  }
}
?>