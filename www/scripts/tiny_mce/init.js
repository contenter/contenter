function init(class_name) 
{
    var base_path = $('base').attr('href');
    
    tinyMCE.init
    ({
        mode : "specific_textareas",
        editor_selector : class_name, 
        
        width: '580px', 
        height: '300px', 
        
        theme : "advanced",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
        
        theme_advanced_disable : "styleselect", 
        theme_advanced_buttons1_add : "forecolor, backcolor", 
        
        content_css : 'css/tiny_mce.css',
        
        //plugins: "images", 
        //theme_advanced_buttons1: "images", 
        convert_urls: false, 
        relative_urls: false, 
        remove_script_host: true
    });
}