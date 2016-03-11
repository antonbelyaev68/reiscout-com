(function ($) {

Drupal.RealEstast = Drupal.RealEstast || {};

Drupal.behaviors.actionRealEstast = {
  attach: function (context) {
    if($('.btn-btt').length) {
      $('.btn-btt').smoothScroll({speed: 600});
    }
    Drupal.RealEstast.placeholder($("#search-block-form input[name='search_block_form']"), 'Keywords');
    $('select[name="field_business_type_tid"]').children('[value="All"]').html('Business Type');
    $('select[name="field_location_tid"]').children('[value="All"]').html('Location');
    $('select[name="field_property_type_tid"]').children('[value="All"]').html('Property Type');

    Drupal.RealEstast.placeholder($('input[name="field_lot_area_value"]'), 'Area From');
    Drupal.RealEstast.placeholder($('input[name="field_price_value"]'), 'Min Price');
    Drupal.RealEstast.placeholder($('input[name="field_price_value_1"]'), 'Max Price');

    $('select[name="field_bedrooms_tid"]').children('[value="All"]').html('Bedrooms');
    $('select[name="field_bathrooms_tid"]').children('[value="All"]').html('Bathrooms');
    $('.region-content-action .views-exposed-form .views-submit-button input').val("Apply");

    $(".view a.accordion-toggle[href='#collapse0']").trigger("click");
    Drupal.RealEstast.flexsliderWithNav();
    Drupal.RealEstast.animationOnScroll();
    Drupal.RealEstast.createdDate();
  }
};

Drupal.RealEstast.flexsliderWithNav = function () {
  var mainSlider = $("#content .node .flexslider:first");
  if (mainSlider.length) {
    var flexsliderContent = mainSlider.html();
    var mainSliderId =  $("#content .flexslider:first").attr('id');

    var navigationId = mainSliderId + "-navigation";
    var navigation = $("<div class='flexslider'><ul class='slides'></ul></div>").html(flexsliderContent);
    navigation.attr("id", navigationId);
    mainSlider.after(navigation);

    $("#" + navigationId).flexslider({
      animation: "slide",
      controlNav: false,
      directionNav: false,
      animationLoop: true,
      slideshow: false,
      itemWidth: 190,
      maxItems: 4,
      asNavFor: "#" + mainSliderId
    });

    $("#" + mainSliderId).flexslider({
      slideshowSpeed: 3000,
      controlNav: false,
      sync: "#" + navigationId
    });
  }
}

Drupal.RealEstast.animationOnScroll = function() {
  if($(window).width() > 991) {
    var waypointClass = '.animation';

    var bodyClass = $("body").attr("class");
    var match = bodyClass.match(/animation-([a-zA-Z])+/g);
    var animationClass = "none";

    if (match !== null) {
      animationClass = match[0].replace("animation-", "");
    }

    if (animationClass !== "none") {
      var delayTime;

      $(waypointClass).css({opacity: '0'});

      $(waypointClass).waypoint(function() {
        delayTime += 100;
        $(this).delay(delayTime).queue(function(next){
          $(this).toggleClass('animated');
          $(this).toggleClass(animationClass);
          delayTime = 0;
          next();
        });
      },
      {
        offset: '80%',
        triggerOnce: true
      });
    }
  }
}

Drupal.RealEstast.placeholder = function(element, string) {
  if($(element).val() === "") {
    $(element).val(string);
  }
  $(element).focus(function() {
    if($(this).val() === string) {
      $(this).val("");
    }
  }).blur(function() {
    if($(this).val() === "") {
      $(this).val(string);
    }
  });
  $(element).closest('form').submit(function() {
    if($(element).val() === string) {
      $(element).val("");
    }    
  });
}

Drupal.RealEstast.createdDate = function() {
  $('.created-date').each(function(){
    var date = $('.field-content', this);
    if (date.length) {
      date = date.text().replace(',', '');
      var day = date.substr(0, 2);
      var month = date.substr(2, date.length);
      date = '<span class="day">' + day.trim() + '</span>';
      date += '<span class="month">' + month.trim() + '</span>';
      $(this).html(date);
    }
  });
}

})(jQuery);
