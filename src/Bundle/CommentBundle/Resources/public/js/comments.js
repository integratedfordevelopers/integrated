$(function () {

    /**
     * Show add comment button
     * @param {string} fieldName
     * @param {object} position
     * @param {jQuery} $parent
     * @param {jQuery} $container
     */
    var showCommentButton = function(fieldName, position, $parent, $container) {
        var $div = $('<div class="add-comment-button hold">Add a Comment</div>');

        $div.mousedown(function(e) {
            newComment(fieldName, position, $parent, $container);
        });

        removeControls();

        $container.append($div);

        positionElement($div, position);
    };

    /**
     * @param {string} fieldName
     * @param {object} position
     * @param {jQuery} $parent
     * @param {jQuery} $container
     */
    var newComment = function (fieldName, position, $parent, $container) {
        $.ajax({
            type: 'GET',
            url: integrated_comment_urls.new.replace('__content__', $('.content-form').data('content-id')).replace('__field__', fieldName),
            success: function(response) {
                showModalComment(response,position, $parent,  $container);
            }
        });
    };

    /**
     * @param {string} commentId
     * @param {object} position
     * @param {jQuery} $parent
     * @param {jQuery} $container
     */
    var showAddedComment = function(commentId, position, $parent, $container) {
        $.ajax({
            type: 'GET',
            url: integrated_comment_urls.get.replace('__comment__', commentId),
            success: function(response) {
                showModalComment(response, position, $parent, $container);
            }
        });
    };

    /**
     * @param {string} response
     * @param {object} position
     * @param {jQuery} $parent
     * @param {jQuery} $container
     */
    var showModalComment = function(response, position, $parent, $container) {
        removeControls();

        var $modal = $(response);
        $container.append($modal);

        positionElement($modal, position);

        $('form', $modal).bind('submit', function(e){
            e.preventDefault();

            postComment($(this), $parent);
        });
    };

    /**
     * @param {jQuery} $form
     * @param {jQuery} $parent
     */
    var postComment = function ($form, $parent) {
        $.ajax({
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            url: $form.attr("action"),
            success: function (response) {
                removeControls();

                if ($parent) {
                    if (!$parent.data('comment-id')) {
                        $parent.data('comment-id', response.id);
                    }

                    createAddedCommentLine($parent);
                } else {
                    //apply integrated_comment format, adds span tag with data-comment-id
                    tinymce.activeEditor.formatter.apply('integrated_comment', {value : response.id});
                }
            }
        });
    };

    /**
     * Add comment icon in the label of an integrated field
     * @param $parent
     */
    var createAddedCommentLine = function($parent) {
        var commentId = $parent.data('comment-id');
        var $label = $parent.closest('.form-group').find('label');

        //remove existing
        $('.added-comment-line[data-comment-id="' + commentId + '"]').remove();

        if (commentId) {
            var comment = $('<div>').addClass('added-comment-line')
                .attr('data-comment-id', commentId).data('comment-id', commentId)
                .data('parent', $parent)
                .append($('<span>').addClass('glyphicon glyphicon-comment'));

            if ($label) {
                //if input has label place it inside label
                $label.append(comment)
            } else {
                //else place it after input
                $parent.after(comment);
            }
        }
    };

    /**
     * @param {jQuery} $element
     * @returns {{top: *, left: *}}
     */
    var calculatePosition = function ($element) {
        var offset = $element.offset();

        return {top: (offset.top + $element.outerHeight()), left: offset.left};
    };

    /**
     * positions element with provided position
     *
     * @param {jQuery} $element
     * @param {object} position
     */
    var positionElement = function ($element, position) {
        $element.css({
            'z-index': 999,
            position: 'absolute',
            left: position.left,
            top: position.top
        });
    };

    /**
     * Removes all buttons and modals related to comment bundle
     */
    var removeControls = function() {
        $('.comment-holder, .add-comment-button').remove();
    };

    /**
     * @param {string} fullName
     * @returns {string}
     */
    var getFieldName = function(fullName) {
        return /\[(.+)\]$/.exec(fullName)[1];
    };

    function removeCommentButton() {
        $('.add-comment-button').remove();
    }

    //
    //
    // Event listeners
    //
    //

    /**
     * Eventlistener for selecting input and textarea fields
     */
    $('input:text, textarea').bind('select', function (e) {
        if (e.target.selectionStart != e.target.selectionEnd) {
            var position = calculatePosition($(this));
            var $container =  $('body');

            if ($(this).data('comment-id') !== undefined) {
                showAddedComment($(this).data('comment-id'), position, $(this), $container);
            } else {
                showCommentButton(getFieldName($(this).attr('name')), position, $(this), $container);
            }
        }
    }).mousedown(function() {
        removeCommentButton();
    }).focusout(function() {
        removeCommentButton();
    });

    /**
     * Add a commentIcon for all existing comments
     */
    $('[data-comment-id]').each(function() {
        createAddedCommentLine($(this));
    });

    /**
     * Check if someone clicks on a comment icon, if so show the comment modal
     */
    $(document).on('click', '.added-comment-line', function () {
        showAddedComment($(this).data('comment-id'), calculatePosition($(this)), $(this).data('parent'), $('body'));
    });

    /**
     * Check if delete button is clicked in modal
     */
    $(document).on('click', '.comment-holder .delete-comment', function (e) {
        e.preventDefault();

        $.get($(this).attr('href'), function (data) {
            removeControls();

            $('.added-comment-line[data-comment-id="' + data.id + '"]').remove();
            if (tinymce.activeEditor != undefined) {
                $('.integrated-comment[data-comment-id="' + data.id + '"]', tinymce.activeEditor.getDoc()).contents().unwrap();
            }

            $('[data-comment-id="' + data.id + '"]').removeAttr('data-comment-id').removeData('comment-id');
        });
        return false;
    });

    /**
     * Remove comment modal
     */
    $(document).on('click', '.comment-holder .integrate-icon-cancel', function(e) {
        e.preventDefault();

        removeControls();

        return false;
    });

    //
    //
    // tinymce custom part
    //
    //

    var tinyCommentCheckSelect = function (e) {
        if ($(e.target).hasClass('hold')) {
            return;
        }

        removeControls();

        var editor = tinymce.activeEditor;
        var selectionContent = editor.selection.getContent();
        var $container = $(editor.getContainer().parentNode);
        var position = tinymcePosition();

        if ($(e.target).hasClass('integrated-comment') && selectionContent == '') {
            showAddedComment($(e.target).data('comment-id'), position, null, $container);

            return;
        } else if ($(e.rangeParent).hasClass('integrated-comment') && selectionContent == '') {
            showAddedComment($(e.rangeParent).data('comment-id'), position, null, $container);

            return;
        } else if (selectionContent == '') {
            return;
        }

        var fieldName = getFieldName($(editor.getElement()).attr('name'));

        showCommentButton(fieldName, position, null, $container);
    };

    /**
     * @returns {{left: *, top: *}}
     */
    var tinymcePosition = function () {
        var rectangle = tinymce.activeEditor.selection.getSel().getRangeAt(0).getBoundingClientRect();
        var $container = $(tinymce.activeEditor.getContainer().parentNode);
        var toolbarHeight = $('.mce-toolbar-grp', $container).outerHeight();

        //bottom position + toolbarheight + 10 pixel margin
        return {left: rectangle.left, top: (rectangle.bottom + toolbarHeight + 10)};
    };

    /**
     * Add event listeners to tinymce after tinymce is loaded
     */
    var tinymceInit = function () {
        if (typeof tinymce == 'undefined' || tinymce.activeEditor == undefined || tinymce.activeEditor.formatter == undefined) {
            return;
        }
        clearInterval(waitForTiny);

        tinymce.activeEditor.formatter.register('integrated_comment', {inline : 'span', 'classes' : 'integrated-comment', attributes: {'data-comment-id' : '%value'}});
        tinymce.activeEditor.on('click', tinyCommentCheckSelect);
        tinymce.activeEditor.on('keypress', tinyCommentCheckSelect);
        tinymce.activeEditor.on('focusout', removeCommentButton);
        tinymce.activeEditor.dom.loadCSS("/bundles/integratedcomment/css/comments.css");
    };

    var waitForTiny = setInterval(tinymceInit, 100);
});
