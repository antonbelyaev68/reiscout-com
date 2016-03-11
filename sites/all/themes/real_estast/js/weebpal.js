(function ($) {

Drupal.WeebPal = Drupal.WeebPal || {};
Drupal.WeebPal.currentWidth = -1;
Drupal.WeebPal.currentType = -1;
Drupal.WeebPal.screens = [0, 767.5, 991.5, 989.5];
Drupal.WeebPal.mobileThreadHold = 991.5;
Drupal.WeebPal.clearMinHeight = function(element) {
  $(element).css('min-height', '0px');
}

Drupal.WeebPal.equalHeight = function() {
}



Drupal.WeebPal.equalHeightActions = function() {
  Drupal.WeebPal.equalHeight();
}

Drupal.WeebPal.onClickResetDefaultSettings = function() {
  var answer = confirm(Drupal.t('Are you sure you want to reset your theme settings to default theme settings?'))
  if (answer){
    $("input:hidden[name = light_use_default_settings]").attr("value", 1);
    return true;
  }

  return false;
}

Drupal.WeebPal.eventStopPropagation = function(event) {
  if (event.stopPropagation) {
    event.stopPropagation();
  }
  else if (window.event) {
    window.event.cancelBubble = true;
  }
}

Drupal.behaviors.actionWeebPal = {
  attach: function (context) {
    $("ul.menu > li > a.fa").each(function() {
      var icon = $('<i class="' + $(this).attr('class') + '"/>');
      if($(this).hasClass('keep-content')) {
        $(this).prepend('icon');
      }
      else {
        $(this).html("").append(icon);
      }
      var className = $(this).attr('class');
      var parts = className.split(" ");
      var classes = [];
      for(x in parts) {
        if(parts[x].indexOf('fa') !== 0) {
          classes.push(parts[x]);
        }
      }
      $(this).attr('class', classes.join(' '));
    });
    $(".change-skin-button").click(function() {
      parts = this.href.split("/");
      style = parts[parts.length - 1];
      $.cookie("weebpal_skin", style, {path: '/'});
      window.location.reload();
      return false;
    });
    
    $(window).scroll(function() {
      if($(window).scrollTop() > 200) {
        $('.btn-btt').show();
      }
      else {
        $('.btn-btt').hide();
      }
    });

    $(window).load(function() {
      Drupal.WeebPal.equalHeightActions();
    });
    
    if($("#block-search-form .search-icon").length == 0) {
      $("#block-search-form > .content").prepend('<span class="search-icon"> </span>');
    }

    $("#block-search-form .search-icon").click(function() {
      if($(this).closest('#block-search-form').hasClass('hover')) {
        $(this).closest('#block-search-form').removeClass('hover');
      }
      else {
        $(this).closest('#block-search-form').addClass('hover');
      }
    });

    $("#block-search-form").click(function(e) {
      Drupal.WeebPal.eventStopPropagation(e);
    });
    $('body').click(function() {
      if($('#block-search-form').hasClass('hover')) {
        $('#block-search-form').removeClass('hover');
      }
    });
    $(window).resize(function() {
      var width = $(window).innerWidth();
      if((width - Drupal.WeebPal.mobileThreadHold) * (Drupal.WeebPal.currentWidth - Drupal.WeebPal.mobileThreadHold) < 0) {
        if(width > Drupal.WeebPal.mobileThreadHold) {
          $("#main-menu-inner").css({width: ""});
        }
      }
      Drupal.WeebPal.currentWidth = width;
    });
      $('#change-skin').click(function(){
          $('#change-skin i').toggleClass('fa-spin');
          if($('#change_skin_menu_wrapper').hasClass('fly-out')){
              $('#change_skin_menu_wrapper').removeClass('fly-out');
          }
          else{
              $('#change_skin_menu_wrapper').addClass('fly-out');
          }
      });
  }
};
})(jQuery);
