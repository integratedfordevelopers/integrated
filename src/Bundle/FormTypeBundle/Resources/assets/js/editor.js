import tinymce from 'tinymce';

import 'tinymce/themes/modern';

import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/hr';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/pagebreak';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/wordcount';
import 'tinymce/plugins/visualchars';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/nonbreaking';
import 'tinymce/plugins/table';
import 'tinymce/plugins/contextmenu';
import 'tinymce/plugins/directionality';
import 'tinymce/plugins/template';
import 'tinymce/plugins/paste';
import 'tinymce/plugins/wordcount';
import 'tinymce/plugins/autoresize';
import 'tinymce/plugins/code';

$('.integrated_tinymce').each(function(key, elem){
    let element = $(elem);

    tinymce.init({
        target: elem,
        theme: "modern",
        plugins: [
            ["advlist autolink link lists charmap hr anchor pagebreak"],
            ["searchreplace wordcount visualchars fullscreen nonbreaking"],
            ["table contextmenu directionality template paste wordcount autoresize code"]
        ],
        external_plugins: {
            "integratedBrowser": "/bundles/integratedformtype/js/tinymce-plugins/integrated-browser/plugin.js",
            "integratedColumn": "/bundles/integratedformtype/js/tinymce-plugins/integrated-column/plugin.js"
        },
        add_unload_trigger: false,
        schema: "html5",
        menubar: false,
        toolbar: "styleselect | bold italic underline | bullist numlist | link integratedImage integratedVideo integratedColumn image media print preview fullpage table | charmap pagebreak | pastetext searchreplace | code fullscreen",
        statusbar: true,
        statusbar_size: "small",
        width: "100%",
        height: "0",
        autoresize_min_height: 300,
        autoresize_max_height: (window.innerHeight-64),
        browser_spellcheck : true,
        autoresize_bottom_margin: "0px",
        convert_urls: false,
        content_css: element.data('content_css'),
        integrated_browser_media_types_url: element.data('integrated_browser_media_types_url'),
        integrated_browser_search_url: element.data('integrated_browser_search_url'),
        integrated_browser_file_url: element.data('integrated_browser_file_url'),
        integrated_browser_file_resize_url: element.data('integrated_browser_file_resize_url'),
        document_base_url : element.data('document_base_url'),
        style_formats: [
            {title: 'Paragraph', format: 'p'},
            {title: 'Heading 2', block: 'h2' },
            {title: 'Heading 3', block: 'h3' },
            {title: 'Heading 4', block: 'h4' },
            {title: 'Heading 5', block: 'h5' },
            {title: 'Preformatted (fixed font)', block: 'pre' },
            {title: 'Superscript', icon: "superscript", inline: 'sup'},
            {title: 'Subscript', icon: "subscript", inline: 'sub'}
            // {% for style_format in content_styles.style_formats %}
            //     {% if loop.first %},{% endif %}
            //     {{ style_format|json_encode|raw }}
            //     {% if not loop.last %},{% endif %}
            // {% endfor %}
        ]
    });
});
