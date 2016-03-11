<?php

/**
 * @file
 * Default simple view template to display a list of rows.
 *
 * @ingroup views_templates
 */
?>
<?php if (!empty($title)): ?>
  <h3><?php print $title; ?></h3>
<?php endif; ?>
<div class="metro-layout horizontal">
  <div class="content clearfix">  
    <div class="views-metro-container items">
      <?php foreach ($rows as $id => $row): ?>
        <div class="box views-metro-item row-<?php print $id;?>">
          <div class="item-inner">
            <?php print $row; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

