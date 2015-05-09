<main>
    <div class="page-header text-center">
        <h2>Users</h2>
    </div>
    <div id="user-info">
        <div class="well bs-component col-lg-4 col-lg-offset-4 text-center">
            <form action="/photo-album/admin/userProfile"  method="post" class="form-horizontal">
                <fieldset class="col-lg-8 col-lg-offset-2 more-margin text-center">
                    <input type="text" name="username" class="form-control" placeholder="Enter username">
                </fieldset>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <button class="btn btn-default">
                        <a href="/photo-album/admin">Cancel</a>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>