(function(){tinymce.create("tinymce.plugins.wpLink",{init:function(a,b){a.addCommand("WP_Link",function(){a.windowManager.open({file:tinymce.baseURL+"/wp-mce-link.php",width:320,height:340,inline:1},{plugin_url:b})});a.addButton("link",{title:a.getLang("advanced.link_desc"),cmd:"WP_Link"});a.addShortcut("alt+shift+a",a.getLang("advanced.link_desc"),"WP_Link");a.onNodeChange.add(function(d,c,e){c.setActive("wplink",e.nodeName=="A")})},getInfo:function(){return{longname:"WordPress Link Dialog",author:"WordPress",authorurl:"http://wordpress.org",infourl:"",version:"1.0"}}});tinymce.PluginManager.add("wplink",tinymce.plugins.wpLink)})();