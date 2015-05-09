var notifier = new notifier();

$(document).ready(function () {
    // Categories
    $('.edit-category-btn').on('click', function () {
        var $btn = $(this);
        var categoryId = $btn.attr('id').replace('edit-category-btn', '');
        $.ajax({
            method: 'POST',
            url: '/photo-album/admin/editCategoryForm/' + categoryId
        }).success(function (data) {
            $btn.hide();
            var editCategoryFiledId = '#edit-category-field' + categoryId;
            var $editCategoryForm = data;
            var $editCategoryFiled = $(editCategoryFiledId);
            $editCategoryFiled.html($editCategoryForm);
        })
    });


    $('.delete-category-btn').on('click', function () {
        var $btn = $(this);
        var categoryId = $btn.attr('id').replace('delete-category-btn', '');
        notifier.showMessage('Do want to delete the category', 'confirm')
            .then(function () {
                location.reload();
                location.reload();
                $.ajax({
                    method: 'POST',
                    url: '/photo-album/admin/deleteCategory/' + categoryId
                }).then(function () {
                    location.reload();
                });
            });
    });

    // Albums
    $('.edit-album-btn').on('click', function () {
        var $btn = $(this);
        var albumId = $btn.attr('id').replace('edit-album-btn', '');
        $.ajax({
            method: 'POST',
            url: '/photo-album/admin/editAlbumForm/' + albumId
        }).success(function (data) {
            $btn.hide();
            var editAlbumFiledId = '#edit-album-field' + albumId;
            var $editAlbumForm = data;
            var $editAlbumFiled = $(editAlbumFiledId);
            $editAlbumFiled.html($editAlbumForm);
        })
    });


    $('.delete-album-btn').on('click', function () {
        var $btn = $(this);
        var albumId = $btn.attr('id').replace('delete-album-btn', '');
        notifier.showMessage('Do want to delete the album', 'confirm')
            .then(function () {
                location.reload();
                location.reload();
                $.ajax({
                    method: 'POST',
                    url: '/photo-album/admin/deleteAlbum/' + albumId
                }).then(function () {
                    location.reload();
                });
            });
    });

    // Photos
    $('.delete-photo-btn').on('click', function () {
        var $btn = $(this);
        var photoId = $btn.attr('id').replace('delete-photo-btn', '');
        notifier.showMessage('Do want to delete the photo', 'confirm')
            .then(function () {
                $.ajax({
                    method: 'POST',
                    url: '/photo-album/admin/deletePhoto/' + photoId
                }).then(function () {
                    location.reload();
                });
            });
    });
});
