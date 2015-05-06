<main>
    <form class="form-horizontal col-lg-6 col-lg-offset-3" method="post"
          action="/photo-album/albums/newAlbum">
        <fieldset>
            <legend>New album</legend>
            <label for="album-name" class="col-lg-2 control-label">Album name</label>
            <div class="col-lg-10 form-group">
                <input type="text" class="form-control" id="album-name" name="albumName" placeholder="Album name">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="isPublic"> Public album
                    </label>
                </div>
            </div>
            <label for="categories" class="col-lg-2 control-label">Category</label>
            <div class="col-lg-10 form-group">
                <select class="form-control" id="categories" name="categoryId">
                    <?php foreach($this->categories as $category): ?>
                        <option value="<?php echo $category['id']?>">
                            <?php $this->renderText($category['name']);?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="col-lg-10 col-lg-offset-2">
                <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                <button type="reset" class="btn btn-default">Cancel</button>
            </div>
        </fieldset>
    </form>
</main>