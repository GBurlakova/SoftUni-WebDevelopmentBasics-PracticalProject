<main>
    <div class="well bs-component col-lg-4 col-lg-offset-4">
        <form action="/account/register"  method="post" class="form-horizontal">
            <fieldset>
                <legend>Register</legend>
                <div class="form-group">
                    <label for="username" class="col-lg-2 control-label">Username</label>
                    <div class="col-lg-10">
                        <input type="text" name="username" class="form-control" id="username" placeholder="Username">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="col-lg-2 control-label">Password</label>
                    <div class="col-lg-10">
                        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
                    </div>
                </div>
            </fieldset>
            <button type="submit" class="btn btn-primary">Register</button>
            <button class="btn btn-default">
                <a href="/account/login">Login</a>
            </button>
        </form>
    </div>
</main>