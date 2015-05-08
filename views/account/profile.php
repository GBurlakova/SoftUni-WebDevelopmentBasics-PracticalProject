<main>
    <div class="well bs-component col-lg-6 col-lg-offset-3">
        <form class="form-horizontal">
            <fieldset>
                <legend>Profile</legend>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Username</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" readonly value="<?php $this->renderText($this->profile['username']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">First name</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" readonly value="<?php $this->renderText($this->profile['first_name']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Last name</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" readonly value="<?php $this->renderText($this->profile['last_name']); ?>">
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</main>