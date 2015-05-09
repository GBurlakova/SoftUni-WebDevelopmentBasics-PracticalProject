<div class="panel-heading">
    <h3 class="panel-title">Edit album</h3>
</div>
<div class="panel-body" style="min-height: 150px; max-height: 150px; overflow-y: auto;">
    <form action="/photo-album/admin/editAlbum"  method="post" class="form-horizontal">
        <fieldset>
            <div class="form-group">
                <div class="col-lg-12">
                    <input type="text" class="col-lg-12" name="albumName" value="<?php $this->renderText($this->albumName); ?>"/>
                    <input type="hidden" class="col-lg-12" name="albumId" value="<?php $this->renderText($this->albumId); ?>"/>
                </div>
            </div>
        </fieldset>
        <button type="submit" class="btn-sm btn-primary">Save changes</button>
        <button class="btn-sm btn-default">
            <a href="/photo-album/admin">Cancel</a>
        </button>
    </form>
</div>