<main>
    <form class="form-horizontal col-lg-6 col-lg-offset-3" method="post"
          action="/photo-album/userAlbums/newAlbum">
        <fieldset>
            <legend>New album</legend>
            <label for="album-name" class="col-lg-2 control-label">Album name</label>
            <div class="col-lg-10 form-group">
                <input type="text" class="form-control" id="album-name" name="albumName" placeholder="Album name">
                <?php if(isset($this->emptyFields['albumName'])): ?>
                <div class=" label label-danger">Please enter non empty name for the album</div>
                <?php endif; ?>
            </div>
            <label for="categories" class="col-lg-2 control-label">Category</label>
            <div class="col-lg-10 form-group">
                <select class="form-control" id="categories" name="categoryId">
                    <?php foreach($this->categories as $category): ?>
                        <option value="<?php $this->renderText($category['id']); ?>">
                            <?php $this->renderText($category['name']);?>
                        </option>
                    <?php endforeach;?>
                </select>
                <?php if(isset($this->emptyFields['categoryId'])): ?>
                    <div class=" label label-danger">Please select category for the album</div>
                <?php endif; ?>
            </div>
            <div class="col-lg-10 col-lg-offset-2">
                <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                <a href="/photo-album/userAlbums" class="btn btn-default">Cancel</a>
            </div>
        </fieldset>
    </form>
</main>