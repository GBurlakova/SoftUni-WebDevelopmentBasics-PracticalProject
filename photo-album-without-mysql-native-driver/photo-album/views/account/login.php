<main>
    <div class="well bs-component col-lg-6 col-lg-offset-3">
        <form action="/photo-album/account/login"  method="post" class="form-horizontal">
            <fieldset>
                <legend>Login</legend>
                <label for="username" class="col-lg-2 control-label">Username</label>
                <div class="form-group col-lg-10">
                    <input type="text" name="username" class="form-control" id="username" placeholder="Username">
                    <?php if(isset($this->emptyFields['username'])): ?>
                    <div class=" label label-danger">Please enter your username</div>
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
            <button type="submit" class="btn btn-primary">Login</button>
            <button class="btn btn-default">
                <a href="/photo-album/account/register">Register</a>
            </button>
        </form>
    </div>
</main>