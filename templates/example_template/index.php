<?php
$templateName = $_data['template']['name'];
$thisPath = $_data['template']['path'];

$pReqURL = parse_url($_data['request']['url']);
$baseURL = sprintf('%s://%s/', $pReqURL['scheme'], $pReqURL['host']);

$siteTitle = $_data['template']['data']['general']['site_title'];
$robots = $_data['template']['data']['general']['robots'];
$contentData = $_data['template']['data']['content'];

$pageFile = 'homepage.php';
$metaDescription = $contentData['homepage']['meta_description'];
$metaKeywords = $contentData['homepage']['meta_keywords'];

if(array_key_exists('page', $_data['template']['url_data'])) {
  $pageURLName = $_data['template']['url_data']['page'];
  $pageFile = $pageURLName.'.php';
  
  $metaDescription = $contentData[$pageURLName]['meta_description'];
  $metaKeywords = $contentData[$pageURLName]['meta_keywords'];
}

$pageFile = $thisPath.'pages/'.$pageFile;

if(!is_file($pageFile)) {
  $pageFile = '404.php';
}

require($thisPath.'pages/page_base.php');
?>