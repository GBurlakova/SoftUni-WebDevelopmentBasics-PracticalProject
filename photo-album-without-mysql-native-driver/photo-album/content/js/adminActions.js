var notifier = new notifier();

$(document).ready(function () {
    // Categories actions
    $('.edit-category-btn').on('click', function () {
        var $btn = $(this);
        var categoryId = $btn.attr('id').replace('edit-category-btn', '');
        $.ajax({
            method: 'POST',
            url: '/photo-album/admin/editCategoryForm/' + categoryId
        }).success(function (data) {
            $btn.hide();
            var editCategoryFieldId = '#edit-category-field' + categoryId;
            var editCategoryForm = data;
            var $editCategoryField = $(editCategoryFieldId);
            $editCategoryField.html(editCategoryForm);
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

    // Albums actions
    $('.edit-album-btn').on('click', function () {
        var $btn = $(this);
        var albumId = $btn.attr('id').replace('edit-album-btn', '');
        $.ajax({
            method: 'POST',
            url: '/photo-album/admin/editAlbumForm/' + albumId
        }).success(function (data) {
            $btn.hide();
            var editAlbumFieldId = '#edit-album-field' + albumId;
            var editAlbumForm = data;
            var $editAlbumField = $(editAlbumFieldId);
            $editAlbumField.html(editAlbumForm);
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

    // Photos actions
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

    // Album comments actions
    $('.edit-album-comment-btn').on('click', function () {
        var $btn = $(this);
        var commentId = $btn.attr('id').replace('edit-album-comment-btn', '');
        $.ajax({
            method: 'POST',
            url: '/photo-album/admin/editAlbumCommentForm/' + commentId
        }).success(function (data) {
            $btn.hide();
            var editAlbumCommentFieldId = '#edit-album-comment-field' + commentId;
            var editAlbumCommentForm = data;
            var $editAlbumCommentField = $(editAlbumCommentFieldId);
            $editAlbumCommentField.html(editAlbumCommentForm);
        })
    });

    $('.delete-album-comment-btn').on('click', function () {
        var $btn = $(this);
        var commentId = $btn.attr('id').replace('delete-album-comment-btn', '');
        notifier.showMessage('Do want to delete the comment', 'confirm')
            .then(function () {
                location.reload();
                $.ajax({
                    method: 'POST',
                    url: '/photo-album/admin/deleteAlbumComment/' + commentId
                }).then(function () {
                    location.reload();
                    location.reload();
                });
            });
    });

    // Photo comments actions
    $('.edit-photo-comment-btn').on('click', function () {
        var $btn = $(this);
        var commentId = $btn.attr('id').replace('edit-photo-comment-btn', '');
        $.ajax({
            method: 'POST',
            url: '/photo-album/admin/editPhotoCommentForm/' + commentId
        }).success(function (data) {
            $btn.hide();
            var editPhotoCommentFieldId = '#edit-photo-comment-field' + commentId;
            var editPhotoCommentForm = data;
            var $editPhotoCommentField = $(editPhotoCommentFieldId);
            $editPhotoCommentField.html(editPhotoCommentForm);
        })
    });

    $('.delete-photo-comment-btn').on('click', function () {
        var $btn = $(this);
        var commentId = $btn.attr('id').replace('delete-photo-comment-btn', '');
        notifier.showMessage('Do want to delete the comment', 'confirm')
            .then(function () {
                $.ajax({
                    method: 'POST',
                    url: '/photo-album/admin/deletePhotoComment/' + commentId
                }).then(function () {
                    location.reload();
                    location.reload();
                });
            });
    });

    // User profile actions
    $('.edit-user-username-btn').on('click', function () {
        var $btn = $(this);
        var username = $btn.attr('id').replace('edit-user-username-btn-', '');
        $.ajax({
            method: 'POST',
            url: '/photo-album/admin/editUsernameForm/' + username
        }).then(function (data) {
            $btn.hide();
            var editUsernameFiledId = '#edit-user-username-field';
            var editUsernameForm = data;
            var $editUsernameField = $(editUsernameFiledId);
            $editUsernameField.html(editUsernameForm);
        });
    });

    $('.edit-user-first-name-btn').on('click', function () {
        var $btn = $(this);
        var username = $btn.attr('id').replace('edit-user-first-name-btn-', '');
        $.ajax({
            method: 'POST',
            url: '/photo-album/admin/editFirstNameForm/' + username
        }).then(function (data) {
            $btn.hide();
            var editUserFirstNameFiledId = '#edit-user-first-name-field';
            var $editUserFirstNameForm = data;
            var $editUserFirstNameField = $(editUserFirstNameFiledId);
            $editUserFirstNameField.html($editUserFirstNameForm);
        });
    });

    $('.edit-user-last-name-btn').on('click', function () {
        var $btn = $(this);
        var username = $btn.attr('id').replace('edit-user-last-name-btn-', '');
        $.ajax({
            method: 'POST',
            url: '/photo-album/admin/editLastNameForm/' + username
        }).then(function (data) {
            $btn.hide();
            var editUserLastNameFieldId = '#edit-user-last-name-field';
            var editUserLastNameForm = data;
            var $editUserLastNameField = $(editUserLastNameFieldId);
            $editUserLastNameField.html(editUserLastNameForm);
        });
    });

    $('.edit-user-role-btn').on('click', function () {
        var $btn = $(this);
        var username = $btn.attr('id').replace('edit-user-role-btn-', '');
        $.ajax({
            method: 'POST',
            url: '/photo-album/admin/editRoleForm/' + username
        }).then(function (data) {
            $btn.hide();
            var editUserRoleFiledId = '#edit-user-role-field';
            var editUserRoleForm = data;
            var $editUserRoleField = $(editUserRoleFiledId);
            $editUserRoleField.html(editUserRoleForm);
        });
    });
});
