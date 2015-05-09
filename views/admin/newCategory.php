<main>
    <div class="well bs-component col-lg-6 col-lg-offset-3">
        <form action="/photo-album/admin/newCategory"  method="post" class="form-horizontal">
            <fieldset>
                <legend>Create category</legend>
                <label for="category-name" class="col-lg-3 control-label">Category name</label>
                <div class="form-group col-lg-9">
                    <input type="text" name="categoryName" class="form-control" id="category-name" placeholder="Category name">
                    <?php if(isset($this->emptyFields['categoryName'])): ?>
                        <div class=" label label-danger">Please enter non-empty category name</div>
                    <?php endif; ?>
                    <?php if(isset($this->createCategoryErrors['categoryNameTaken'])): ?>
                        <div class=" label label-danger">Category name is already taken</div>
                    <?php endif; ?>
                </div>
            </fieldset>
            <button type="submit" class="btn btn-primary">Create</button>
            <button class="btn btn-default">
                <a href="/photo-album/admin/categories">Cancel</a>
            </button>
        </form>
    </div>
</main>