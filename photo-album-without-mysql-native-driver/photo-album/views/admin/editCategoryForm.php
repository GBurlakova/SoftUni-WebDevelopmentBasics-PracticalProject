<form action="/photo-album/admin/editCategory"  method="post" class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <div class="col-lg-12">
                <input type="text" name="categoryName" value="<?php $this->renderText($this->categoryName); ?>"/>
                <input type="hidden" name="categoryId" value="<?php $this->renderText($this->categoryId); ?>"/>
            </div>
        </div>
    </fieldset>
    <button type="submit" class="btn-sm btn-primary">Save changes</button>
    <button class="btn-sm btn-default">
        <a href="/photo-album/admin/categories">Cancel</a>
    </button>
</form>