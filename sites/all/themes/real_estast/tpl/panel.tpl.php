<?php if(!$empty_panel):?>
<div class="row">
  <?php foreach ($panels_list as $panel): ?>
    <?php if ($panel_width[$panel]) :?>
      <div class="<?php print $panel_classes[$panel];?>">
        <div class="grid-inner clearfix">
          <?php if ($panel_content = render($page[$panel])): ?>
            <?php print $panel_content; ?>
          <?php else:?>
            &nbsp;
          <?php endif;?>
        </div>
      </div>
    <?php endif;?>
  <?php endforeach;?>
</div>
<?php endif;?>