<form action="/photo-album/photos/comment"  method="post" class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <div class="col-lg-12">
                <textarea class="form-control" rows="3" name="comment"></textarea>
                <input type="hidden" name="photoId" value="<?php echo $this->photoId; ?>"/>
                <input type="hidden" name="albumId" value="<?php echo $this->albumId; ?>"/>
                <input type="hidden" name="controller" value="<?php echo $this->controller; ?>"/>
            </div>
        </div>
    </fieldset>
    <button type="submit" class="btn-sm btn-primary">Comment</button>
    <button class="btn-sm btn-default">
        <a href="/photo-album/<?php echo $this->controller; ?>/photos/<?php echo $this->albumId; ?>">Cancel</a>
    </button>
</form>