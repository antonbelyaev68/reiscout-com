
  <div id="page" <?php print $page_css; ?>>
    <?php if(isset($page['show_skins_menu']) && $page['show_skins_menu']):?>
      <?php print $page['show_skins_menu'];?>
    <?php endif;?>
      
    <?php if($headline = render($page['headline'])): ?>
      <section id="headline" class="headline section">
        <div class="container">
          <?php print $headline; ?>
        </div>
      </section>
    <?php endif;?>
    <header id="header" class="header section navbar navbar-default">
      <div class="container">
        <?php if ($logo): ?>
          <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
            <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
          </a>
        <?php endif; ?>

        <?php if ($site_name || $site_slogan): ?>
          <div id="name-and-slogan">
            <?php if ($site_name): ?>
              <?php if ($title): ?>
                <div id="site-name"><strong>
                  <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
                </strong></div>
              <?php else: /* Use h1 when the content title is empty */ ?>
                <h1 id="site-name">
                  <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
                </h1>
              <?php endif; ?>
            <?php endif; ?>

            <?php if ($site_slogan): ?>
              <div id="site-slogan"><?php print $site_slogan; ?></div>
            <?php endif; ?>
          </div> <!-- /#name-and-slogan -->
        <?php endif; ?>

        <?php print render($page['header']); ?>

        <?php if($main_menu = render($page['main_menu'])): ?>
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-menu-inner">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <nav class="collapse navbar-collapse width" id="main-menu-inner">
            <?php print $main_menu; ?>
          </nav>
        <?php endif;?>
      </div>
    </header>

    <?php if ($title): ?>
      <section id="title-wrapper" class="section">
        <div class="container">
          <?php print render($title_prefix); ?>
          <?php if ($title): ?><h1 class="title" id="page-title"><?php print $title; ?></h1><?php endif; ?>
          <?php print render($title_suffix); ?>
        </div>
      </section>
    <?php endif;?>  
      
    <?php if ($slideshow = render($page['slideshow'])): ?>
      <section id="slideshow" class="slideshow section">
        <div class="container-fluid">
          <?php print $slideshow;?>
        </div>
      </section>
    <?php endif;?>

    <?php if ($messages): ?>
      <section id="messages" class="message section">
        <div class="container">
          <?php print $messages; ?>
        </div>
      </section>
    <?php endif;?>

    <?php if($panel_first = render($page['panel_first'])): ?>
      <section id="panel-first" class="panel-first section">
        <div class="container">
          <?php print $panel_first;?>
        </div>
      </section>
    <?php endif; ?>

    <?php if($panel_highlighted = render($page['panel_highlighted'])): ?>
      <section id="panel-highlighted" class="panel-highlighted section">
        <div class="container">
          <?php print $panel_highlighted;?>
        </div>
      </section>
    <?php endif; ?>

    <section id="main" class="main section">
      <div class="container">
        <div class="row">
          <div class="col-md-<?php print $regions_width['content']?>">
            <?php if ($page['featured']): ?><div id="featured"><?php print render($page['featured']); ?></div><?php endif; ?>
            <?php if ($page['highlighted']): ?><div id="highlighted"><?php print render($page['highlighted']); ?></div><?php endif; ?>
            <div id="content" class="column">
              <div class="section">
                <a id="main-content"></a>
                <?php if ($tabs): ?><div class="tabs"><?php print render($tabs); ?></div><?php endif; ?>
                <?php print render($page['help']); ?>
                <?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>
                <?php if($page['content_action']): print render($page['content_action']); endif;?>
                <?php print render($page['content']); ?>
                                  
              </div>
            </div>
          </div>
          <?php if ($regions_width['sidebar_first']): ?>
            <aside id="sidebar-first" class="sidebar col-md-<?php print $regions_width['sidebar_first']?>">
              <div class="section">
                <?php print render($page['sidebar_first']); ?>
              </div>
            </aside>
          <?php endif; ?>
          <?php if ($page['sidebar_second']): ?>
            <aside id="sidebar-second" class="sidebar col-md-<?php print $regions_width['sidebar_second']?>">
              <div class="section">
                <?php print render($page['sidebar_second']); ?>
              </div>
            </aside>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <?php if($map = render($page['map'])): ?>
      <section id="map" class="section">
        <?php print $map;?>
      </section>
    <?php endif; ?>

    <?php if($panel_second = render($page['panel_second'])): ?>
      <section id="panel-second" class="section">
        <div class="container">
          <?php print $panel_second;?>
        </div>
      </section>
    <?php endif; ?>

    <?php if($panel_third = render($page['panel_third'])): ?>
      <section id="panel-third" class="section">
        <div class="container">
          <?php print $panel_third;?>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($breadcrumb): ?>
      <section id="breadcrumb" class="section">
        <div class="container">
          <?php print $breadcrumb; ?>
        </div>
      </section>
    <?php endif; ?>

    <?php if($panel_footer = render($page['panel_footer'])): ?>
      <section id="panel-footer" class="section">
        <div class="container">
          <?php print $panel_footer;?>
        </div>
      </section>
    <?php endif; ?>

    <?php if($footer = render($page['footer'])): ?>
      <footer id="footer" class="section">
        <div class="container">
          <?php print $footer; ?>
          <!--?php print $feed_icons; ?-->
        </div>
      </footer>
    <?php endif;?>
    <a title="<?php print t('Back to Top')?>" class="btn-btt" href="#Top"></a>
  </div>
