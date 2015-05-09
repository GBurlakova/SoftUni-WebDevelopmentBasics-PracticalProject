<form action="/photo-album/photos/comment"  method="post" class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <div class="col-lg-12">
                <textarea class="form-control" rows="3" name="comment"></textarea>
                <input type="hidden" name="photoId" value="<?php $this->renderText($this->photoId); ?>"/>
                <input type="hidden" name="albumId" value="<?php $this->renderText($this->albumId); ?>"/>
                <input type="hidden" name="controller" value="<?php $this->renderText($this->controller); ?>"/>
            </div>
        </div>
    </fieldset>
    <button type="submit" class="btn-sm btn-primary">Comment</button>
    <button class="btn-sm btn-default">
        <a href="/photo-album/<?php $this->renderText($this->controller); ?>/photos/<?php $this->renderText($this->albumId); ?>">Cancel</a>
    </button>
</form>