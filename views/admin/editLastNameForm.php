<form action="/photo-album/admin/editLastName"  method="post" class="form-horizontal">
    <div class="form-group col-lg-6">
        <input type="text" name="newLastName"
               class="form-control"
               value="<?php $this->renderText($this->profile['last_name']); ?>">
        <input type="hidden" name="currentLastName"
               class="form-control"
               value="<?php $this->renderText($this->profile['last_name']); ?>">
        <input type="hidden" name="username"
               class="form-control"
               value="<?php $this->renderText($this->profile['username']); ?>">
    </div>
    <button type="submit" class="btn btn-info col-lg-3">Save changes</button>
    <a class="btn btn-default col-lg-3" href="/photo-album/admin/users">Cancel</a>
</form>