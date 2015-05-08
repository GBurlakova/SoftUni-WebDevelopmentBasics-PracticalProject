<main>
    <div class="well bs-component col-lg-6 col-lg-offset-3">
        <form class="form-horizontal" method="post" action="/photo-album/account/editProfile">
            <fieldset>
                <legend>Profile</legend>
                <label class="col-lg-2 control-label">First name</label>
                <div class="form-group col-lg-10" >
                    <input type="text"
                           class="form-control"
                           value="<?php $this->renderText($this->profile['first_name']); ?>"
                            name="firstName">
                    <?php if(isset($this->emptyFields['firstName'])): ?>
                        <div class="label label-danger">Please enter your first name</div>
                    <?php endif; ?>
                </div>

                <label class="col-lg-2 control-label">Last name</label>
                <div class="form-group col-lg-10">
                    <input type="text"
                           class="form-control"
                           value="<?php $this->renderText($this->profile['last_name']); ?>"
                           name="lastName"/>
                    <?php if(isset($this->emptyFields['lastName'])): ?>
                        <div class=" label label-danger">Please enter your last name</div>
                    <?php endif; ?>
                </div>

                <label class="col-lg-2 control-label">Username</label>
                <div class="form-group col-lg-10">
                    <input type="text"
                           class="form-control"
                           value="<?php $this->renderText($this->profile['username']); ?>"
                           name="username">
                    <?php if(isset($this->emptyFields['username'])): ?>
                        <div class=" label label-danger">Please enter your username</div>
                    <?php endif; ?>
                    <?php if(isset($this->registerErrors['usernameTaken'])): ?>
                        <div class=" label label-danger">Username is already taken</div>
                    <?php endif; ?>
                </div>

                <label class="col-lg-2 control-label">Password</label>
                <div class="form-group col-lg-10">
                    <input type="text"
                           class="form-control"
                           name="password"/>
                    <?php if(isset($this->emptyFields['password'])): ?>
                        <div class=" label label-danger">Please enter your password</div>
                    <?php endif; ?>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Save changes</button>
                <button class="btn btn-default">
                    <a href="/photo-album/userALbums">Cancel</a>
                </button>
            </fieldset>
        </form>
    </div>
</main>