<?php
$pageContent = $contentData['homepage'];
$dynamicWord1 = $pageContent['dynamic_word1'];
$dynamicWord2 = $pageContent['dynamic_word2'];
?>
<div class="container-fluid">
  <h1>Welcome to the homepage</h1>
  <p>
    <?php if(!array_key_exists('pageArg', $_data['template']['url_data'])) { ?>
      You visited this page with no argument
    <?php } else { ?>
      <strong><?php print_r($_data['template']['url_data']['pageArg']); ?></strong> pageArg was provided on this page
    <?php } ?>
  </p>
  <p>Find out all about variable words like <strong><?php print_r($dynamicWord1); ?></strong></p>
  
  <h3>Another variable word: <?php print_r($dynamicWord2); ?></h3>
  
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h2 class="panel-title"><?php print_r($templateName); ?> has a button</h2>
    </div>
    <div class="panel-body">
      <a class="btn btn-warning btn-sm" href="<?php print_r($baseURL); ?>someUnknownPage/">Click here for 404 page</a>
    </div>
  </div>
  
  <h4>Template $_data array</h4>
  <code>
  <?php
    ob_start();
    print_r($_data);
    $txtData = ob_get_contents();
    ob_end_clean();
    print_r(str_replace(
      [' ', "\n"], ['&nbsp;', '<br />'], $txtData
    ));
  ?>
  </code>
</div>