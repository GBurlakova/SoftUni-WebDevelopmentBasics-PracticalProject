var notifier = new notifier();

$(document).ready(function () {
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
        }).error(function () {
        });
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
});
