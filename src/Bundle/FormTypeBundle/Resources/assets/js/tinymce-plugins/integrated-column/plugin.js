tinymce.create('tinymce.plugins.IntegratedColumn', {

    init : function(editor, url){

        editor.contentCSS.push(url+'/css/bootstrap-grid.min.css');
        editor.contentCSS.push(url+'/css/style.css');
        editor.settings.body_class += ' container-fluid';

        var cssId = 'integrated-column-css';  // you could encode the css path itself to generate id..
        if (!document.getElementById(cssId))
        {
            var head  = document.getElementsByTagName('head')[0];
            var link  = document.createElement('link');
            link.id   = cssId;
            link.rel  = 'stylesheet';
            link.type = 'text/css';
            link.href = '/bundles/integratedformtype/js/tinymce-plugins/integrated-column/css/style-editor.css';
            link.media = 'all';
            head.appendChild(link);
        }

        function addBootstrapColumn(){
            var $rowTemplate    = '<div class="row row-text">{{column}}</div><br>';
            var $column         = '';

            for(var i = 0; i < arguments.length; i++){
                $column += '<div class="col-sm-'+arguments[i]+'"><p>&nbsp;</p></div>';
            }

            editor.insertContent($rowTemplate.replace('{{column}}', $column));
        }

        var menuConfig = {
            context : 'tools',
            text    : 'Add row with columns',
            onclick : addBootstrapColumn
        };

        var buttonConfig = {
            type    : 'menubutton',
            title   : 'Add row with columns',
            icon    : 'columns',
            menu    : [
                {
                    text : '50% | 50%',
                    onclick : function(){
                        addBootstrapColumn(6, 6);
                    }
                },{
                    text : '33% | 66%',
                    onclick : function(){
                        addBootstrapColumn(4, 8);
                    }
                },{
                    text : '66% | 33%',
                    onclick : function(){
                        addBootstrapColumn(8, 4);
                    }
                },{
                    text : '33% | 33% | 33%',
                    onclick : function(){
                        addBootstrapColumn(4, 4, 4);
                    }
                }
            ]
        };

        editor.addButton('integratedColumn', buttonConfig);
        editor.addMenuItem('integratedColumn', menuConfig);
    }
});


tinymce.PluginManager.add('integratedColumn', tinymce.plugins.IntegratedColumn);