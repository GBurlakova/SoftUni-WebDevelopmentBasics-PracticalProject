<main>
    <form class="form-horizontal col-lg-6 col-lg-offset-3" method="post"
          action="/photo-album/userAlbums/upload"
          enctype="multipart/form-data">
        <fieldset>
            <legend>Upload photo</legend>
            <label for="photo" class="col-lg-2 control-label">Photo</label>
            <div class="col-lg-10 form-group">
                <input type="file" name="photo" value="Select"/>
                <?php if(isset($this->erros['emptyPhoto'])): ?>
                    <div class=" label label-danger">Please select a photo</div>
                <?php endif; ?>
                <?php if(isset($this->erros['notAllowedPhotoSize'])): ?>
                    <div class=" label label-danger">Please select a photo less than 2MB</div>
                <?php endif; ?>
                <?php if(isset($this->erros['notAllowedPhotoType'])): ?>
                    <div class=" label label-danger">Please select a photo with type jpeg</div>
                <?php endif; ?>
            </div>
            <label for="albums" class="col-lg-2 control-label">Album</label>
            <div class="col-lg-10 form-group">
                <select class="form-control" id="albums" name="albumId">
                    <?php foreach($this->albums as $album): ?>
                        <option value="<?php $this->renderText($album['id']); ?>">
                            <?php $this->renderText($album['name']);?>
                        </option>
                    <?php endforeach;?>
                </select>
                <?php if(isset($this->erros['emptyAlbumId'])): ?>
                    <div class=" label label-danger">Please select an album for the photo</div>
                <?php endif; ?>
            </div>
            <div class="col-lg-10 col-lg-offset-2">
                <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                <a href="/photo-album/userAlbums" type="reset" class="btn btn-default">Cancel</a>
            </div>
        </fieldset>
    </form>
</main>