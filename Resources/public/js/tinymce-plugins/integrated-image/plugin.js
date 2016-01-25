tinymce.create('tinymce.plugins.IntegratedImage', {

    init : function(editor, url){

        var browseImage = function(){
            editor.windowManager.open({
                title   : 'Browse Images',
                url     : Routing.generate('integrated_content_content_browse_image'),
                width   : 800,
                height  : 600
            });
        }

        var menuConfig = {
            context : 'tools',
            text    : 'Add image',
            onclick : browseImage
        };

        var buttonConfig = {
            title   : 'Add image',
            class   : 'bold',
            onclick : browseImage,
            icon    : 'image'
        };

        editor.addButton('integratedImage', buttonConfig);
        editor.addMenuItem('integratedImage', menuConfig);
    }
});

tinymce.PluginManager.add('integratedImage', tinymce.plugins.IntegratedImage);