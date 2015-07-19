<?php
class render extends actContext {
  public function renderPage() {
    if(!utils::keysOk($this->data, ['url'])) {
      return $this->response(
        'ERROR', 'required keys not set'
      );
    }
    
    $url = $this->data['url'];
    if(!utils::validUrl($url)) {
      return $this->response(
        'ERROR', 'invalid URL: '.$url
      );
    }
    
    $pUrl = parse_url($url);
    $hostData = $this->hostData($pUrl['host']);
    if(!$hostData) {
      return $this->response(
        'ERROR', 'could not get host data for '.$pUrl['host']
      );
    }
    
    $hostData['request']['url'] = $url;
    list($ltBool, $ltData) = template::loadTemplate($hostData);
    
    if(!$ltBool) {
      return $this->response(
        'ERROR', $ltData
      );
    }
    
    list($content, $content_type) = $ltData;
    
    return $this->response(
      'OK', sprintf('render successful [%s]', $url),
      base64_encode($content), $content_type
    );
  }
  
  private function hostData($hostname) {
    $db = new sqliteDB(wm_db_file);
    $db_connect = $db->connect();
    if(!($db_connect['status'] == 'OK')) {
      return $this->response(
        'ERROR', 'database connection failed'
      );
    }
    
    $query = "SELECT a.id as site_id, a.hostname, a.template_data,
    b.id as template_id, b.name as template_name, b.path as template_path
    FROM sites a
    LEFT JOIN templates b
    ON b.id = a.template_id
    WHERE hostname = '".$hostname."'";
    $db_query = $db->query($query);
    
    if($db_query['status'] != 'OK') {
      return false;
    }
    
    $rows = utils::dbObject($db_query['data']);
    if(count($rows) == 0) {
      return false;
    }
    
    $data = $rows[0];
    if(!$data['template_data'] = json_decode($data['template_data'], true)) {
      return false;
    }
    
    return [
      'request' => [
        'site_id' => $data['site_id'],
        'hostname' => $data['hostname'],
      ],
      'template' => [
        'id' => $data['template_id'],
        'path' => wm_templates_path.$data['template_path'],
        'name' => $data['template_name'],
        'data' => $data['template_data']
      ]
    ];
  }
  
  private static function response($status, $message, $data = false,
    $cntType = 'text/plain') {
    $message = sprintf('[%s] %s', 'ACT::render', $message);
    main::log($status, $message);
    return main::response(
      $status, $message, $data, $cntType
    );
  }
  
  public function exec() {
    return $this->renderPage();
  }
}
?>
