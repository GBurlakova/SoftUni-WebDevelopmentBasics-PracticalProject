$(document).ready(function () {
    $('.comment-photo-btn').on('click', function () {
        var $btn = $(this);
        var photoId = $btn.attr('id').replace('comment-photo-btn', '');
        var controller = window.location.pathname.split('/')[2];
        var albumId = window.location.pathname.split('/')[4];
        $.ajax({
            method: 'GET',
            url: '/photo-album/photos/commentForm?photoId=' + photoId + '&albumId=' + albumId + '&controller=' + controller
        }).success(function (data) {
            $btn.hide();
            var panelBodyId = '#panel-photo-body' + photoId;
            var commentForm = data;
            var $panelBody = $(panelBodyId);
            $panelBody.html(commentForm);
        }).error(function () {
        });
    });
});