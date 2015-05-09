<form action="/photo-album/allAlbums/comment"  method="post" class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <div class="col-lg-12">
                <textarea class="form-control" rows="3" name="comment"></textarea>
                <input type="hidden" name="albumId" value="<?php $this->renderText($this->albumId); ?>"/>
            </div>
        </div>
    </fieldset>
    <button type="submit" class="btn-sm btn-primary">Comment</button>
    <button class="btn-sm btn-default">
        <a href="/photo-album/allAlbums">Cancel</a>
    </button>
</form>