<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="login-heading" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="login-heading">Login with PGLife</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="login-form" class="form" role="form" method="post" action="api/login_submit.php">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <label for="login-email">Email</label>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-user" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="email" class="form-control" id="login-email" name="email" placeholder="Email" autocomplete="email" required>
                    </div>

                    <label for="login-password">Password</label>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" id="login-password" name="password" placeholder="Password" autocomplete="current-password" minlength="6" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-primary">Login</button>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <span>
                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#signup-modal">Click here</a>
                    to register a new account
                </span>
            </div>
        </div>
    </div>
</div>
