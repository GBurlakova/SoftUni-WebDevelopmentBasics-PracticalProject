<form action="/photo-album/admin/editRole"  method="post" class="form-horizontal">
    <div class="form-group col-lg-6 text-center">
        <input type="checkbox" name="isAdmin"
               class="form-control checkbox checkbox-medium"
               <?php if($this->profile['role'] == ADMIN_ROLE){ echo 'checked'; } ?>>
        <input type="hidden" name="username"
               class="form-control"
               value="<?php $this->renderText($this->profile['username']); ?>">
    </div>
    <button type="submit" class="btn btn-info col-lg-3">Save changes</button>
    <a class="btn btn-default col-lg-3" href="/photo-album/admin/users">Cancel</a>
</form>