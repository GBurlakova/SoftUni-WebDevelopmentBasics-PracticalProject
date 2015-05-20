$(document).ready(function () {
    $('.comment-btn').on('click', function () {
        var $btn = $(this);
        var id = $btn.attr('id').replace('comment-btn', '');
        var controller = window.location.pathname.split('/')[2];
        $.ajax({
            method: 'POST',
            url: '/photo-album/' + controller + '/commentForm/' + id
        }).success(function (data) {
            $btn.hide();
            var panelBodyId = '#panel-body' + id;
            var commentForm = data;
            var $panelBody = $(panelBodyId);
            $panelBody.html(commentForm);
        })
    });
});
