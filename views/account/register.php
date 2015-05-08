<main>
    <div class="well bs-component col-lg-6 col-lg-offset-3">
        <form action="/photo-album/account/register"  method="post" class="form-horizontal">
            <fieldset>
                <legend>Register</legend>
                <label for="first-name" class="col-lg-2 control-label">First name</label>
                <div class="form-group col-lg-10">
                    <input type="text" name="firstName"
                           class="form-control" id="first-name"
                           placeholder="First name"
                           value="<?php if(isset($this->filledFields['firstName'])) { echo $this->filledFields['firstName']; }?>">
                    <?php if(isset($this->emptyFields['firstName'])): ?>
                    <div class="label label-danger">Please enter your first name</div>
                    <?php endif; ?>
                </div>
                <label for="last-name" class="col-lg-2 control-label">Last name</label>
                <div class="form-group col-lg-10">
                    <input type="text"
                           name="lastName"
                           class="form-control" id="last-name"
                           placeholder="Last name"
                           value="<?php if(isset($this->filledFields['lastName'])) { echo $this->filledFields['lastName']; }?>">
                    <?php if(isset($this->emptyFields['lastName'])): ?>
                    <div class=" label label-danger">Please enter your last name</div>
                    <?php endif; ?>
                </div>
                <label for="username" class="col-lg-2 control-label">Username</label>
                <div class="form-group col-lg-10">
                    <input type="text"
                           name="username" class="form-control"
                           id="username"
                           placeholder="Username"
                           value="<?php if(isset($this->filledFields['username'])) { echo $this->filledFields['username']; }?>">
                    <?php if(isset($this->emptyFields['username'])): ?>
                    <div class=" label label-danger">Please enter your username</div>
                    <?php endif; ?>
                    <?php if(isset($this->registerErrors['usernameTaken'])): ?>
                        <div class=" label label-danger">Username is already taken</div>
                    <?php endif; ?>
                </div>
                <label for="inputPassword" class="col-lg-2 control-label">Password</label>
                <div class="form-group col-lg-10">
                    <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
                    <?php if(isset($this->emptyFields['password'])): ?>
                    <div class=" label label-danger">Please enter your password</div>
                    <?php endif; ?>
                </div>
            </fieldset>
            <button type="submit" class="btn btn-primary">Register</button>
            <button class="btn btn-default">
                <a href="/photo-album/account/login">Login</a>
            </button>
        </form>
    </div>
</main>