<?php $this->load('common/header_all'); ?>

<main class="reset-shell">
    <div class="reset-card">
        <h1>Reset Your Password</h1>
        <p>Choose a strong new password for your account.</p>

        <?php if (!empty($error)) { ?>
            <div class="reset-alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <a href="<?php echo url('login/forgot'); ?>" class="btn-reset-submit" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">Request New Reset Link</a>
        <?php } else { ?>
            <form id="js-reset-password-form" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars(iget('token', ''), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="reset-field">
                    <label for="rp-password">New Password *</label>
                    <input type="password" id="rp-password" name="password" placeholder="At least 6 characters" required minlength="6">
                </div>
                <div class="reset-field">
                    <label for="rp-confirm">Confirm New Password *</label>
                    <input type="password" id="rp-confirm" name="confirm_password" placeholder="Re-enter your new password" required minlength="6">
                </div>
                <button type="submit" class="btn-reset-submit" id="js-reset-submit">Update Password</button>
            </form>
        <?php } ?>
    </div>
</main>
