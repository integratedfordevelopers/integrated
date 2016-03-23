$(function() {

    var setPositionBySelected = function ($modal) {
        var $addButton = $('.add-comment-button', $tinyBody);
        var addButtonOffset = $addButton.offset();

        $modal.css({
            'z-index': 999,
            position: 'absolute',
            left: addButtonOffset.left - ($modal.width() / 2) + ($addButton.width() / 2),
            top: addButtonOffset.top - $modal.height() + ($addButton.height() * 2)
        });
    };

    var postComment = function (e) {
        e.preventDefault();

        var contentId = $('#integrated_content_id').val();
        $.ajax({
            type: 'POST',
            data: $(this).serialize(),
            url: Routing.generate('integrated_comment_new', {content: contentId}),
            success: function (response) {
                var $parent = $tinyMCE.getContainer().parentNode;

                var $modal = $(response);
                $parent.appendChild($modal.get(0));
                setPositionBySelected($modal);
                removeCommentButton();

                $('form', $modal).bind('submit', postComment);
            }
        });
    };

    var showModalComment = function () {
        var contentId = $('#integrated_content_id').val();
        $.ajax({
            type: 'GET',
            url: Routing.generate('integrated_comment_new', {content: contentId}),
            success: function (response) {
                var $parent = $tinyMCE.getContainer().parentNode;

                var $modal = $(response);
                $parent.appendChild($modal.get(0));
                setPositionBySelected($modal);
                removeCommentButton();

                $('form', $modal).bind('submit', postComment);
            }
        });
    };

    var removeModalComment = function () {
        $('.comment-holder', $tinyBody).remove();
    };

    var showCommentButton = function() {
        var $parent = $('.comment-text-selected', $tinyBody);
        var $div = $('<div class="add-comment-button hold">Add a Comment</div>');
        $div.bind('click', showModalComment);
        $parent.append($div);
    };

    var removeCommentButton = function() {
        $('.add-comment-button', $tinyBody).remove();
    };

    var stripSelectSpan = function() {
        removeCommentButton();

        $('.comment-text-selected', $tinyBody).each(function() {
            $(this).replaceWith($(this).html());
        });
    };

    var commentCheckSelect = function (e) {
        if ($(e.target).hasClass('hold')) {
            return;
        }

        stripSelectSpan();
        removeModalComment();

        var selectContent = $tinyMCE.selection.getContent();
        if (selectContent == '') {
            return;
        }

        $tinyMCE.selection.setContent('<span class="comment-text-selected current">' + selectContent + '</span>');
        showCommentButton();
    };

    if (tinymce !== undefined) {
        setTimeout(function() {
            $tinyMCE = tinyMCE.activeEditor;
            $tinyBody = $tinyMCE.getBody();
            $tinyMCE.on('click', commentCheckSelect);
            $tinyMCE.on('keypress', commentCheckSelect);

            $tinyMCE.dom.loadCSS("/bundles/integratedcomment/css/comments.css");
        }, 2000);
    }
});