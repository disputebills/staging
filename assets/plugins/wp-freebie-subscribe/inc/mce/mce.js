/*-----------------------------------------------------------------------------------*/
/*	MCE Plugin for WordPress 3.9+ ( tinyMCE 4.0+ )
/*-----------------------------------------------------------------------------------*/
(function(){tinymce.PluginManager.add("freebiesubplugin",function(b,a){b.addButton("freebiesubplugin",{title:"Subscribe To Download",image:a+"/mce.png",onclick:function(){b.windowManager.open({file:a+"/mce_dialog.php",width:460,height:210,inline:1,resizable:0,scrollbars:0},{plugin_url:a})}})})})();