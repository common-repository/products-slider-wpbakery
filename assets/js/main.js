jQuery('document').ready(function($){
    
    /* mail send */
    $('.elementinvader_addons_for_elementor_f').submit(function(e){
        e.preventDefault();
        var this_form = $(this);
        var $config = this_form.find('.config');
        var conf_link = $config.attr('data-url') || 0;
        var load_indicator = this_form.find('.ajax-indicator-masking');
        var box_alert = this_form.find('.elementinvader_addons_for_elementor_f_box_alert').html('');
        load_indicator.css('display', 'inline-block');
        
        var data = this_form.serializeArray();
        data.push({name: 'action', value: "elementinvader_addons_for_elementor_forms_send_form"});
            $.post(conf_link, data, 
                function(data){
                if(data.message)
                    box_alert.html(data.message)

                if(data.success)
                {
                    this_form.find('input:not([type="checkbox"]):not([name="element_id"]):not([type="radio"]):not([type="hidden"]),textarea').val('');
                } else {
                    
                }
            }).always(function(data) {
                load_indicator.css('display', 'none');
            });

        return false;
    });
    /* end mail send */
    
    /* Start menu dropdown */
    var _w = $(window);
    $('.elementinvader-addons-for-elementor .wl-nav-menu .menu-item-has-children').on('hover', function(event) {
        if($(this).parent().find('ul').length){
            event.preventDefault();
            event.stopPropagation();
            $(this).parent().siblings().removeClass('show-m');
            $(this).parent().toggleClass('show-m');
            if((parseInt($(window).width()) - ($(this).offset().left)-550) < 0 ) {
                $(this).parent().addClass('toleft');
            } else {
                $(this).parent().removeClass('toleft');
            }
        }
    });

    /* End menu dropdown */
    
    $('.elementinvader-addons-for-elementor  .wl-nav-menu .menu-item-has-children > a').on('click', function(e){
        e.preventDefault();
        $('.wl-nav-menu .menu-item-has-children').not($(this).parent()).removeClass('active');
        $(this).parent().toggleClass('active');
        
        
    })
        
    $("html").on("click", function(){
        $('.wl-nav-menu .menu-item-has-children').removeClass("active");
    });
    
    $('.elementinvader-addons-for-elementor .wl-nav-menu .menu-item-has-children > a').on("click", function(e) {
        e.stopPropagation();
    });
    
    $('.eli-menu .wl-menu-toggle,.wl_close-menu,.elementinvader-addons-for-elementor .wl_nav_mask').on('click', function(e){
        e.preventDefault();
        var menu_widg = $(this).closest('.elementor-widget-eli-menu');
        menu_widg.toggleClass('wl_nav_show');
    })
    
    /* End menu dropdown */

})