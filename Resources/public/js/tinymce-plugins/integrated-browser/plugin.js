tinymce.create('tinymce.plugins.integratedBrowser', {
    init : function(editor, url){
        var image = function() {
            editor.windowManager.open({
                title   : 'Browse images',
                url     : url+'/browse.html',
                width   : 800,
                height  : 600
            }, {
                mode: 'image'
            });
        };
        var video = function() {
            editor.windowManager.open({
                title: 'Browse video',
                url: url + '/browse.html',
                width: 800,
                height: 600
            }, {
                mode: 'video'
            });
        };

        editor.addButton(
            'integratedImage',
            {
                title   : 'Add image',
                class   : 'bold',
                onclick : image,
                icon    : 'image'
            }
        );
        editor.addButton(
            'integratedVideo',
            {
                title   : 'Add video',
                class   : 'bold',
                onclick : video,
                icon    : 'media'
            }
        );

        editor.addMenuItem(
            'integratedImage',
            {
                text    : 'Add image',
                context : 'tools',
                onclick : image
            }
        );
        editor.addMenuItem(
            'integratedVideo',
            {
                text    : 'Add video',
                context : 'tools',
                onclick : video
            }
        );
    }
});

tinymce.PluginManager.add('integratedBrowser', tinymce.plugins.integratedBrowser);
