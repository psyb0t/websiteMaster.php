<?php
class template {
  public static function loadTemplate($hostData) {
    if(!$hostData) {
      return [false, 'hostData not defined'];
    }
    
    if(!utils::keysOk($hostData, ['request', 'template'])) {
      return [false, 'required hostData keys not set'];
    }
    
    if(!utils::keysOk($hostData['request'], ['hostname', 'url'])) {
      return [false, 'required request keys not set'];
    }
    
    if(!utils::keysOk($hostData['template'], ['path', 'data'])) {
      return [false, 'required template keys not set'];
    }
    
    $tmplPath = rtrim($hostData['template']['path'], '/').'/';
    if(!is_dir($tmplPath)) {
      return [false, sprintf(
        'template path does not exist [%s]', $tmplPath
      )];
    }
    
    $tmplLoader = $tmplPath.'index.php';
    if(!is_file($tmplLoader)) {
      return [false, 'template loader file not found'];
    }
    
    $tmplConfigFile = $tmplPath.'config.php';
    if(!is_file($tmplConfigFile)) {
      return [false, 'template config file not found'];
    }
    
    require($tmplConfigFile);
    
    if(!function_exists('wm_templateConfig')) {
      return [false, 'wm_templateConfig function missing from config file'];
    }
    
    $parsedReqUrl = parse_url($hostData['request']['url']);
    $reqPath = ltrim($parsedReqUrl['path'], '/');
    $hostData['template']['url_data'] = $reqPath;
    
    $possibleFile = $tmplPath.$reqPath;
    if(is_file($possibleFile)) {
      $content = file_get_contents($possibleFile);
      $type = utils::fileMimeType($possibleFile);
      
      return [true, [$content, $type]];
    }
    
    $tmplConfig = wm_templateConfig();
    if($tmplConfig) {
      if(!is_array($tmplConfig)) {
        return [false, 'invalid config type'];
      }
      
      if(array_key_exists('required_data', $tmplConfig)) {
        $reqData = $tmplConfig['required_data'];
        $tmplData = $hostData['template']['data'];
        
        if(!$tmplData) {
          return [false, 'template data not defined(properly)'];
        }
        
        if(!template::checkRequiredData($reqData, $tmplData)) {
          return [false, 'required template data not set'];
        }
      }
      
      if(array_key_exists('rewrite_rules', $tmplConfig)) {
        $rwRules = $tmplConfig['rewrite_rules'];
        if(count($rwRules) > 0) {
          $hostData['template']['url_data'] = template::rwRulesBreakdown(
            $parsedReqUrl, $rwRules
          );
        }
      }
    }
    
    $_data = $hostData;
    ob_start();
    require($tmplLoader);
    $templateResult = ob_get_contents();
    ob_end_clean();
    
    return [true, [$templateResult, 'text/html']];
  }
  
  /*
   * Check config required_data rules
   */
  private static function checkRequiredData($config, $tmplData) {
    foreach(array_keys($config) as $key) {
      if(!array_key_exists($key, $tmplData)) {
        return false;
      }
      
      if(is_array($config[$key])) {
        if(!template::checkRequiredData($config[$key], $tmplData[$key])) {
          return false;
        }
        
        continue;
      }
      
      if(!$tmplData[$key]) {
        return false;
      }
    }
    
    return true;
  }
  
  /*
   * Break URL structure to object data
   */
  private static function rwRulesBreakdown($parsedUrl, $rules) {
    $urlPath = trim($parsedUrl['path'], '/');
    
    $urlData = [];
    if(!$urlPath) {
      return $urlData;
    }
    
    $urlArgs = explode('/', $urlPath);
    for($i = 0; $i < count($rules); $i++) {
      if($i >= count($urlArgs)) {
        break;
      }
      
      $cRule = $rules[$i];
      if(!is_array($cRule)) {
        main::log(
          'ERROR', sprintf('rewrite rule %d should be an object', $i), true
        );
      }
      
      if(count($cRule) != 1) {
        main::log(
          'ERROR', 'rewrite rule object should have one element', true
        );
      }
      
      $argName = array_keys($cRule)[0];
      $argPattern = $cRule[$argName];
      
      $cUrlArg = $urlArgs[$i];
      preg_match($argPattern, $cUrlArg, $match);
      if(!$match) {
        main::log(
          'ERROR', sprintf(
            'URL argument %d "%s" does not match the pattern %s',
            $i + 1, $cUrlArg, $argPattern
          ), true
        );
      }
      
      $urlData[$argName] = $cUrlArg;
    }
    
    return $urlData;
  }
}
?>
