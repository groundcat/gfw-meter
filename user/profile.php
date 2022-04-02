<?php 
include 'templates/header.php';

$currentUser = app('current_user');
?>

<div class="row">
    <?php
        $sidebarActive = 'profile';
        require 'templates/sidebar.php';
    ?>

    <div class="col-md-9 col-lg-10">

        <?php if (! $currentUser->is_admin) : ?>
            <div class="alert alert-warning">
                <strong><?= trans('note') ?>! </strong>
                <?= trans('to_change_email_username') ?>
            </div>
        <?php endif; ?>

        <!-- start: Change email form -->
        <div class="card">
            <div class="card-header">
                Notification recipient email
            </div>
            <div class="card-body">
                <!-- Form submit POST to self -->
                <form>
                    <!-- Form input-->
                    <div class="form-group">
                        <label>
                            Email
                            <small>that receives notifications</small>
                        </label>
                        <input name="domain" class="form-control" placeholder="you@example.com" value="<?php echo app('current_user')->email;?>" disabled>

                        <input type="hidden" name="<?= ASCsrf::getTokenName() ?>" value="<?= ASCsrf::getToken() ?>">

                    </div>
                </form>
            </div>
        </div>
        <!-- end: Change email form -->

        <br>


        <div class="card">
            <div class="card-header">
                <?= trans('change_password') ?>
            </div>
            <div class="card-body">
                <form id="change-password-form">
                    <!-- Password input-->
                    <div class="form-group">
                        <label>
                            <?= trans('old_password') ?>
                        </label>
                        <input name="old_password" type="password" class="form-control">
                    </div>

                    <!-- Password input-->
                    <div class="form-group">
                        <label>
                            <?= trans('new_password') ?>
                        </label>
                        <input name="new_password" type="password" class="form-control">
                    </div>

                    <!-- Password input-->
                    <div class="form-group">
                        <label>
                            <?= trans('confirm_new_password') ?>
                        </label>
                        <input name="new_password_confirmation" type="password" class="form-control">
                    </div>

                    <button id="change_password" type="submit" class="btn btn-primary">
                        <?= trans('update') ?>
                    </button>
                </form>
            </div>
        </div>

        <br>

        <div class="card">
            <div class="card-header">
                Delete Account and Purge Data (funtionality not implemented yet)
            </div>
            <div class="card-body">
                <form>
                    <button id="change_password" type="submit" class="btn btn-danger" disabled>
                        Delete Account
                    </button>
                </form>
            </div>
        </div>

        
    </div>
</div>


    <script src="assets/js/vendor/sha512.js"></script>
    <?php include 'templates/footer.php'; ?>
    <script src="assets/js/app/profile.js"></script>
  </body>
</html>
