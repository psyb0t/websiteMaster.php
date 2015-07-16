<!DOCTYPE html>
<html>
  <head>
    <title><?php print_r($_data['general']['site_title']); ?></title>
    <link rel="stylesheet" href="<?php print_r($baseURL); ?>public/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php print_r($baseURL); ?>public/bootstrap/css/bootstrap-theme.min.css" />
    <script type="text/javascript" src="<?php print_r($baseURL); ?>public/jquery.js"></script>
    <script type="text/javascript" src="<?php print_r($baseURL); ?>public/bootstrap/js/bootstrap.min.js"></script>
    
    <meta name="description" content="<?php print_r($metaDescription); ?>" />
    <meta name="keywords" content="<?php print_r($metaKeywords); ?>" />
    <meta name="robots" content="<?php print_r($robots); ?>" />
  </head>
  <body>
    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <a class="navbar-brand" href="<?php print_r($baseURL); ?>"><?php print_r($siteTitle); ?></a>
      </div>
    </nav>
    <?php require($pageFile); ?>
    <div class="container-fluid">
      <div class="navbar navbar-default">
        <div class="container-fluid">
          <div class="pull-left">
            <p>Served by websiteMaster.php</p>
          </div>
          <div class="pull-right">
            <p>Template name: <?php print_r($templateName); ?></p>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </body>
</html>
