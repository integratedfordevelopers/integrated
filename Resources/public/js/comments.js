$(function() {

    var setCenterOfObject = function($block, $parent) {
        var parentOffset = $parent.offset();
        var centerParent = parentOffset.left + ($parent.width() / 2);

        var leftPosBlock = centerParent - ($block.width() / 2);

        var topPosBlock = parentOffset.top - $block.height();

        $block.css({
            'z-index': 999,
            position: 'absolute',
            left: leftPosBlock,
            top: topPosBlock
        });
    };

    var showAddedComment = function($parent) {
        var commentId = $parent.data('id');
        $.ajax({
            type: 'GET',
            url: Routing.generate('integrated_comment_get', {comment: commentId}),
            success: function (response) {
                var $modal = $(response);
                removeCommentButton();

                $tinyContainer.append($modal);
                setCenterOfObject($modal, $parent);

                $('form', $modal).bind('submit', postComment);
            }
        });
    };

    var postComment = function (e) {
        e.preventDefault();

        var contentId = $('#integrated_content_id').val();
        $.ajax({
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            url: Routing.generate('integrated_comment_new', {content: contentId}),
            success: function (response) {
                removeModalComment();

                var $parent = $('.comment-text-selected', $tinyBody);
                $parent.removeClass('comment-text-selected').addClass('comment-added').attr('data-id', response.id);
            }
        });
    };

    var showModalComment = function () {
        var contentId = $('#integrated_content_id').val();
        $.ajax({
            type: 'GET',
            url: Routing.generate('integrated_comment_new', {content: contentId}),
            success: function (response) {
                var $parent = $('.comment-text-selected', $tinyBody);
                var $modal = $(response);
                removeCommentButton();

                $tinyContainer.append($modal);
                setCenterOfObject($modal, $parent);

                $('form', $modal).bind('submit', postComment);
            }
        });
    };

    var removeModalComment = function () {
        $('.comment-holder', $tinyContainer).remove();
    };

    var showCommentButton = function() {
        var $parent = $('.comment-text-selected', $tinyBody);
        var $div = $('<div class="add-comment-button hold">Add a Comment</div>');
        $div.bind('click', showModalComment);

        $tinyContainer.append($div);
        setCenterOfObject($div, $parent);
    };

    var removeCommentButton = function() {
        $('.add-comment-button', $tinyContainer).remove();
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

        if ($(e.target).hasClass('comment-added') && selectContent == '') {
            showAddedComment($(e.target));
            return;
        } else if (selectContent == '') {
            return;
        }

        $tinyMCE.selection.setContent('<span class="comment-text-selected">' + selectContent + '</span>');
        showCommentButton();
    };

    if (tinymce !== undefined) {
        setTimeout(function() {
            $tinyMCE = tinyMCE.activeEditor;
            $tinyBody = $tinyMCE.getBody();
            $tinyContainer = $($tinyMCE.getContainer().parentNode);

            $tinyMCE.on('click', commentCheckSelect);
            $tinyMCE.on('keypress', commentCheckSelect);

            $tinyMCE.dom.loadCSS("/bundles/integratedcomment/css/comments.css");
        }, 2000);
    }
});