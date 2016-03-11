<article id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <?php //print $user_picture; ?>

  <?php if ($real_estast_media_field): ?>
    <?php print($real_estast_media_field); ?>
  <?php endif; ?>
  
  <div class="node-content">
    <?php if ($title && !$page): ?>
    <?php print render($title_prefix); ?>
      <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
    <?php print render($title_suffix); ?>
    <?php endif; ?>

    <div class="created-date">
      <?php
        print t('<span class="day">@day</span><span class="month">@month @year</span>',
          array('@day' => date('d', $created), '@month' => date('M', $created), '@year' => date('Y', $created), )
        );
      ?>        
    </div>

    <?php if ($display_submitted): ?>
      <div class="submitted">
        <?php
          print t('By !username in !content_type', array(
            '!username' => $name,
            '!content_type' => ucfirst($type),
            )
          );
        ?>
      </div>
    <?php endif; ?>

    <?php if($comment_links): ?>
      <div class="comment-links">
        <?php print render($comment_links);?>
      </div>
    <?php endif;?>

    <div class="content"<?php print $content_attributes; ?>>
      <?php
        // We hide the comments and links now so that we can render them later.
        hide($content['comments']);
        hide($content['links']);
        print render($content);
      ?>
    </div>

    <?php print render($content['links']); ?>

    <?php print render($content['comments']); ?>
  </div>
</article>
