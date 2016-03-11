<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
  "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" version="XHTML+RDFa 1.0" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>>

<head profile="<?php print $grddl_profile; ?>">
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>
  <!-- META FOR IOS & HANDHELD -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <meta name="HandheldFriendly" content="true" />
  <meta name="apple-touch-fullscreen" content="YES" />
  <!-- //META FOR IOS & HANDHELD -->  
  <?php print $styles; ?>
  <?php print $scripts; ?>
</head>
<body class="<?php print $classes; ?>" <?php print $attributes;?>>
  <div id="skip-link">
    <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
  </div>
  <?php print $page_top; ?>
  <div id="page" class="page-default"> <a name="Top" id="Top"></a>
    <div id="main-wrapper" class="wrapper">
      <div class="container">
        <div class="row clearfix"> 
          <div id="main-content" class="span<?php print $regions_width['content']?>">
            <div class="grid-inner clearfix">
              <?php print $content;?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php print $page_bottom; ?>
</body>
</html>
