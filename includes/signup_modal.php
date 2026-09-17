<div class="modal fade" id="signup-modal" tabindex="-1" role="dialog" aria-labelledby="signup-heading" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="signup-heading">Signup with PGLife</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="signup-form" class="form" role="form" method="post" action="api/signup_submit.php">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <label for="signup-name">Full Name</label>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-user" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="signup-name" name="full_name" placeholder="Full Name" autocomplete="name" maxlength="30" required>
                    </div>

                    <label for="signup-phone">Phone Number</label>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-phone-alt" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="signup-phone" name="phone" placeholder="Phone Number" autocomplete="tel" inputmode="tel" maxlength="10" minlength="10" required>
                    </div>

                    <label for="signup-email">Email</label>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="email" class="form-control" id="signup-email" name="email" placeholder="Email" autocomplete="email" required>
                    </div>

                    <label for="signup-password">Password</label>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" id="signup-password" name="password" placeholder="Password" autocomplete="new-password" minlength="6" required>
                    </div>

                    <label for="signup-college">College Name</label>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-university" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="signup-college" name="college_name" placeholder="College Name" maxlength="150" required>
                    </div>

                    <fieldset class="form-group gender-options">
                        <legend>I'm a</legend>
                        <label for="gender-male">
                            <input type="radio" id="gender-male" name="gender" value="male" /> Male
                        </label>
                        <label for="gender-female">
                            <input type="radio" id="gender-female" name="gender" value="female" /> Female
                        </label>
                    </fieldset>

                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-primary">Create Account</button>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <span>Already have an account?
                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#login-modal">Login</a>
                </span>
            </div>
        </div>
    </div>
</div>
