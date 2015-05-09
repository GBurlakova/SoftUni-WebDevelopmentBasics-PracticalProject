<form action="/photo-album/admin/editAlbumComment"  method="post" class="form-horizontal">
    <fieldset class="more-margin">
        <input type="text" class="col-lg-12" name="commentText" value="<?php $this->renderText($this->comment['text']); ?>"/>
        <input type="hidden" name="commentId" value="<?php $this->renderText($this->comment['id']); ?>"/>
        <span>User: </span><span class="label label-info"><?php $this->renderText($this->comment['username']); ?></span>
        <span>Date: </span><span><?php $this->renderText(date_format(date_create($this->comment['date']), 'd/m/Y')); ?></span>
    </fieldset>
    <button type="submit" class="btn-sm btn-primary">Save changes</button>
    <button class="btn-sm btn-default">
        <a href="/photo-album/admin">Cancel</a>
    </button>
</form>