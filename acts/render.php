<?php
class render extends actContext {
  private $db = false;
  
  public function renderPage() {
    if(!utils::keysOk($this->data, ['url'])) {
      $message = 'required keys not set';
      main::log('ERROR', $message);
      return main::response('ERROR', $message);
    }
    
    $url = $this->data['url'];
    if(!utils::validUrl($url)) {
      $message = 'invalid url';
      main::log('ERROR', $message);
      return main::response('ERROR', $message);
    }
    
    $pUrl = parse_url($url);
    
    $this->db = new sqliteDB(wm_db_file);
    $db_connect = $this->db->connect();
    if(!($db_connect['status'] == 'OK')) {
      $message = 'database connection failed';
      main::log('ERROR', $message);
      return main::response('ERROR', $message);
    }
    
    $hostData = $this->hostData($pUrl['host']);
    if(!$hostData) {
      $message = 'could not get host data';
      main::log('ERROR', $message);
      return main::response('ERROR', $message);
    }
    
    $hostData['request']['url'] = $url;
    
    list($ltBool, $ltData) = template::loadTemplate($hostData);
    if(!$ltBool) {
      main::log('ERROR', $ltData);
      return main::respose('ERROR', $ltData);
    }
    
    list($content, $content_type) = $ltData;
    main::log('OK', sprintf('render successful [%s]', $url));
    return main::response(
      'OK', 'successfully returned base64 encoded data',
      base64_encode($content), $content_type
    );
  }
  
  private function hostData($hostname) {
    $query = "SELECT a.id as site_id, a.hostname, a.template_data,
    b.id as template_id, b.name as template_name, b.path as template_path
    FROM sites a
    LEFT JOIN templates b
    ON b.id = a.template_id
    WHERE hostname = '".$hostname."'";
    $db_query = $this->db->query($query);
    
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
  
  public function exec() {
    return $this->renderPage();
  }
}
?>
