<main>
    <div class="well bs-component col-lg-6 col-lg-offset-3" id="edit-user-profile-field">
        <div class="form-horizontal">
            <fieldset>
                <legend>Profile</legend>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Username</label>
                    <div class="col-lg-10" id="edit-user-username-field">
                        <input type="text" class="form-control" readonly value="<?php $this->renderText($this->profile['username']); ?>">
                    </div>
                    <div class="col-lg-10 col-lg-offset-2 margin">
                        <a class="btn-sm btn-primary edit-user-username-btn" id="edit-user-username-btn-<?php $this->renderText($this->profile['username']); ?>">Edit username</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">First name</label>
                    <div class="col-lg-10" id="edit-user-first-name-field">
                        <input type="text" class="form-control" readonly value="<?php $this->renderText($this->profile['first_name']); ?>">
                    </div>
                    <div class="col-lg-10 col-lg-offset-2 margin">
                        <a class="btn-sm btn-primary edit-user-first-name-btn" id="edit-user-first-name-btn-<?php $this->renderText($this->profile['username']); ?>">Edit first name</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Last name</label>
                    <div class="col-lg-10" id="edit-user-last-name-field">
                        <input type="text" class="form-control" readonly value="<?php $this->renderText($this->profile['last_name']); ?>">
                    </div>
                    <div class="col-lg-10 col-lg-offset-2 margin">
                        <a class="btn-sm btn-primary edit-user-last-name-btn" id="edit-user-last-name-btn-<?php $this->renderText($this->profile['username']); ?>">Edit last name</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Administrator</label>
                    <div class="col-lg-10" id="edit-user-role-field">
                        <input type="text" class="form-control" readonly
                               value="<?php if($this->profile['role']){ $this->renderText(ucfirst($this->profile['role'])); } else{ echo 'Not defined'; } ?>">
                    </div>
                    <div class="col-lg-10 col-lg-offset-2 margin">
                        <a class="btn-sm btn-primary edit-user-role-btn" id="edit-user-role-btn-<?php $this->renderText($this->profile['username']); ?>">Edit role</a>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</main>