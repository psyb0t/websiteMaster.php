<?php
class ping extends actContext {
  public function pingUrl() {
    if(!utils::keysOk($this->data, ['url', 'title'])) {
      return $this->response(
        'ERROR', 'required keys not set'
      );
    }
    
    $url = $this->data['url'];
    if(!utils::validUrl($url)) {
      return $this->response(
        'ERROR', 'invalid url'
      );
    }
    
    $title = $this->data['title'];
    
    main::loadLibs([
      'httpRequest/httpRequest.class.php'
    ]);
    
    $pingomaticUrl = 'http://pingomatic.com/ping/'.
      '?title='.urlencode($title).
      '&blogurl='.urlencode($url).
      '&rssurl='.
      '&chk_weblogscom=on'.
      '&chk_blogs=on'.
      '&chk_feedburner=on'.
      '&chk_newsgator=on'.
      '&chk_myyahoo=on'.
      '&chk_pubsubcom=on'.
      '&chk_blogdigger=on'.
      '&chk_weblogalot=on'.
      '&chk_newsisfree=on'.
      '&chk_topicexchange=on'.
      '&chk_google=on'.
      '&chk_tailrank=on'.
      '&chk_skygrid=on'.
      '&chk_collecta=on'.
      '&chk_superfeedr=on'.
      '&chk_audioweblogs=on'.
      '&chk_rubhub=on'.
      '&chk_a2b=on'.
      '&chk_blogshares=on';
    
    $request = new httpRequest($pingomaticUrl);
    $request->setRandUserAgent();
    
    if(array_key_exists('proxy', $this->data)) {
      try {
        $request->setProxy($this->data['proxy']);
      } catch(Exception $e) {
        return $this->response(
          'ERROR', $e->getMessage()
        );
      }
    }
    
    $request = $request->exec();
    if(!$request['status'] == 'OK') {
      return $this->response(
        'ERROR', $request['message']
      );
    }
    
    if(strrpos($request['data'], 'Pinging complete!') === false) {
      return $this->response(
        'ERROR', 'pingomatic failed to ping '.$url
      );
    }
    
    return $this->response(
      'OK', 'successfully pinged '.$url
    );
  }
  
  private function response($status, $message) {
    $message = sprintf('[%s] %s', 'ACT::ping', $message);
    main::log($status, $message);
    return main::response(
      $status, $message, false, 'text/plain'
    );
  }
  
  public function exec() {
    return $this->pingUrl();
  }
}
?>
