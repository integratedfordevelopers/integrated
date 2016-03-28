$(function () {

    var $selectedParent = null;
    var $container = null;
    var flowOffset = {top:0,left:0};
    var postCommentCallback = null;
    var fieldName = null;

    var tinyCommentCheckSelect = function (e) {
        if ($(e.target).hasClass('hold')) {
            return;
        }

        var $tinyMCE = tinyMCE.activeEditor;

        removeControls();

        $container = $($tinyMCE.getContainer().parentNode);
        flowOffset = {top:0,left:0};

        var selectContent = $tinyMCE.selection.getContent();

        if ($(e.target).hasClass('comment-added') && selectContent == '') {
            showAddedComment($(e.target));
            return;
        } else if ($(e.rangeParent).hasClass('comment-added') && selectContent == '') {
            showAddedComment($(e.rangeParent));
            return;
        } else if (selectContent == '') {
            console.log('2');
            return;
        }

        $tinyMCE.selection.setContent('<span class="comment-text-selected">' + selectContent + '</span>');

        $selectedParent = $('.comment-text-selected', $tinyMCE.getBody());
        postCommentCallback = function (commentId) {
            $selectedParent.removeClass('comment-text-selected').addClass('comment-added').attr('data-comment-id', commentId);
        };
        fieldName = getFieldName($('textarea', $container).attr('name'));

        showCommentButton($selectedParent, $container);
    };

    var textCommentCheckSelect = function (e) {
        if (e.target.selectionStart != e.target.selectionEnd) {
            removeControls();

            $selectedParent = $(this);
            $container = $('body');
            flowOffset.top = -25;
            postCommentCallback = function (commentId) {
                if ($selectedParent.next('.added-comment-line').length == 0) {
                    createAddedCommentLine($selectedParent.attr('data-comment-id', commentId));
                }
            };
            fieldName = getFieldName($selectedParent.attr('name'));

            if ($(this).data('comment-id') !== undefined) {
                showAddedComment($(this));
            } else {
                showCommentButton($selectedParent, $container);
            }
        }
    };

    var removeControls = function() {
        stripSelectSpan();
        removeModalComment();
        removeCommentButton();
    };

    var stripSelectSpan = function() {
        $('.comment-text-selected', tinyMCE.activeEditor.getBody()).each(function() {
            $(this).replaceWith($(this).html());
        });
    };

    var showCommentButton = function($element, $container) {
        var $div = $('<div class="add-comment-button hold">Add a Comment</div>');
        $div.bind('click', showModalComment);

        $container.append($div);
        setCenterOfObject($div, $element);
    };

    var removeCommentButton = function() {
        $('.add-comment-button').remove();
    };

    var showModalComment = function () {
        var contentId = $('#integrated_content_id').val();
        $.ajax({
            type: 'GET',
            url: Routing.generate('integrated_comment_new', {content: contentId, field: fieldName}),
            success: function (response) {
                var $modal = $(response);
                removeCommentButton();

                $container.append($modal);
                setCenterOfObject($modal, $selectedParent);

                $('form', $modal).bind('submit', postComment);
            }
        });
    };

    var removeModalComment = function () {
        $('.comment-holder').remove();
    };

    var postComment = function (e) {
        e.preventDefault();

        var contentId = $('#integrated_content_id').val();
        $.ajax({
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            url: Routing.generate('integrated_comment_new', {content: contentId, field: fieldName}),
            success: function (response) {
                removeModalComment();
                postCommentCallback.call(this, response.id);
            }
        });
    };

    var showAddedComment = function($parent) {
        var commentId = $parent.data('comment-id');
        $.ajax({
            type: 'GET',
            url: Routing.generate('integrated_comment_get', {comment: commentId}),
            success: function (response) {
                var $modal = $(response);
                removeCommentButton();

                $container.append($modal);
                setCenterOfObject($modal, $parent);

                $('form', $modal).bind('submit', postComment);
            }
        });
    };

    var setCenterOfObject = function($block, $parent) {
        var parentOffset = $parent.offset();
        var centerParent = parentOffset.left + ($parent.width() / 2);

        var leftPosBlock = centerParent - ($block.width() / 2) + flowOffset.left;
        var topPosBlock = parentOffset.top - $block.height() + flowOffset.top;

        $block.css({
            'z-index': 999,
            position: 'absolute',
            left: leftPosBlock,
            top: topPosBlock
        });
    };

    var getFieldName = function(fullName) {
        return /\[(.+)\]$/.exec(fullName)[1];
    };

    var createAddedCommentLine = function($parent) {
        var commentId = $parent.data('comment-id');
        if (commentId) {
            $parent.after('<div class="added-comment-line" data-comment-id="'+commentId+'">&nbsp;</div>');
        }
    };

    var tinyMceInit = function () {
        if (tinyMCE.activeEditor == undefined) {
            return;
        }
        clearInterval(waitForTiny);

        tinyMCE.activeEditor.on('click', tinyCommentCheckSelect);
        tinyMCE.activeEditor.on('keypress', tinyCommentCheckSelect);
        tinyMCE.activeEditor.dom.loadCSS("/bundles/integratedcomment/css/comments.css");
    };
    var waitForTiny = setInterval(tinyMceInit, 100);

    $('input:text, textarea').bind('select', textCommentCheckSelect);
    $(document).on('click', '.added-comment-line', function () {
        $container = $('body');
        var $prev = $(this).prev();
        fieldName = getFieldName($prev.attr('name'));
        showAddedComment($prev);
    });

    $(document).on('click', '.comment-holder .integrate-icon-cancel', function() {
        removeControls();
    });

    $('[data-comment-id]').each(function() {
        createAddedCommentLine($(this));
    });
});