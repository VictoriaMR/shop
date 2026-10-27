<?php
$this->load('common/header_all');

$siteName = \App::get('domain', 'name', 'Shop');
$activeTab = isset($activeTab) ? $activeTab : 'signin';
$prefillEmail = isset($prefillEmail) ? (string)$prefillEmail : '';
$prefillCode = isset($prefillCode) ? (string)$prefillCode : '';
$initialMode = ($prefillCode !== '') ? 'code' : 'password';

if (empty($memberBenefits)) {
	$memberBenefits = [
		[
			'icon'  => 'gift',
			'title' => 'Welcome Member Perks',
			'desc'  => 'Unlock member-only launch prices and seasonal voucher drops',
		],
		[
			'icon'  => 'truck',
			'title' => 'Real-Time Order Tracking',
			'desc'  => 'Track every package from studio dispatch to doorstep delivery',
		],
		[
			'icon'  => 'heart',
			'title' => 'Synced Wishlist & Bag',
			'desc'  => 'Save your favorite furniture, lighting & apparel across devices',
		],
		[
			'icon'  => 'shield',
			'title' => '30-Day Buyer Protection',
			'desc'  => 'Fast, hassle-free returns and dedicated 24/7 order support',
		],
	];
}
?>

<main class="auth-main" id="main-content">
	<div class="container">
		<div class="auth-split-card">
			<!-- Left Column: Brand Value & Member Benefits -->
			<aside class="auth-brand-panel" aria-label="Member Benefits">
				<div>
					<span class="auth-brand-badge">
						<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
						<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?> Insider Club
					</span>
					<h1 class="auth-brand-title"> Curated Living, Tailored to You.</h1>
					<p class="auth-brand-sub">Sign in or create a free member account to unlock insider pricing, instant order updates, and seamless checkout across all your devices.</p>

					<div class="auth-benefits-list">
						<?php foreach ($memberBenefits as $benefit) { ?>
						<div class="auth-benefit-item">
							<div class="auth-benefit-icon" aria-hidden="true">
								<?php if ($benefit['icon'] === 'gift') { ?>
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
								<?php } elseif ($benefit['icon'] === 'truck') { ?>
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
								<?php } elseif ($benefit['icon'] === 'heart') { ?>
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
								<?php } else { ?>
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
								<?php } ?>
							</div>
							<div>
								<h2 class="auth-benefit-title"><?php echo htmlspecialchars($benefit['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
								<p class="auth-benefit-desc"><?php echo htmlspecialchars($benefit['desc'], ENT_QUOTES, 'UTF-8'); ?></p>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>

				<div class="auth-security-foot flex items-center">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
					<span>256-Bit SSL Encrypted Checkout &amp; Strict Privacy Protection</span>
				</div>
			</aside>

			<!-- Right Column: Interactive Auth Forms -->
			<section class="auth-form-panel" aria-label="Account Authentication">
				<!-- Top Tabs (Sign In / Create Account) -->
				<div class="auth-tabs<?php echo $activeTab === 'forgot' ? ' hidden' : ''; ?>" id="auth-tabs" role="tablist" aria-label="Sign In or Register">
					<button type="button" role="tab" class="auth-tab-btn<?php echo $activeTab === 'signin' ? ' active' : ''; ?>" data-auth-tab="signin" aria-selected="<?php echo $activeTab === 'signin' ? 'true' : 'false'; ?>" aria-controls="panel-signin">
						Sign In
					</button>
					<button type="button" role="tab" class="auth-tab-btn<?php echo $activeTab === 'register' ? ' active' : ''; ?>" data-auth-tab="register" aria-selected="<?php echo $activeTab === 'register' ? 'true' : 'false'; ?>" aria-controls="panel-register">
						Create Account
					</button>
				</div>

				<!-- Panel 1: Sign In -->
				<div id="panel-signin" role="tabpanel" class="<?php echo $activeTab !== 'signin' ? 'hidden' : ''; ?>">
					<div class="auth-panel-head">
						<h2 class="auth-panel-title">Welcome Back</h2>
						<p class="auth-panel-desc">Enter your email address and password to access your account.</p>
					</div>

					<form id="form-signin" novalidate>
						<input type="hidden" id="signin-login-mode" name="login_mode" value="<?php echo $initialMode; ?>">

						<div class="form-group">
							<div class="form-label-row flex justify-between items-center">
								<label class="form-label" for="signin-email">Email Address</label>
							</div>
							<div class="input-wrap">
								<input type="email" id="signin-email" name="email" class="form-input" value="<?php echo htmlspecialchars($prefillEmail, ENT_QUOTES, 'UTF-8'); ?>" placeholder="name@example.com" autocomplete="email" required>
							</div>
							<p class="field-error" role="alert"></p>
						</div>

						<!-- Password Login Group -->
						<div class="form-group<?php echo $initialMode !== 'password' ? ' hidden' : ''; ?>" id="signin-password-group">
							<div class="form-label-row flex justify-between items-center">
								<label class="form-label" for="signin-password">Password</label>
								<a href="<?php echo url('login/forgot'); ?>" class="form-aux-link" data-switch-view="forgot">Forgot password?</a>
							</div>
							<div class="input-wrap has-suffix">
								<input type="password" id="signin-password" name="password" class="form-input" placeholder="Enter your password" autocomplete="current-password">
								<button type="button" class="pwd-toggle-btn" aria-label="Show password">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
								</button>
							</div>
							<p class="field-error" role="alert"></p>
						</div>

						<!-- Email Verification Code Login Group -->
						<div class="form-group<?php echo $initialMode !== 'code' ? ' hidden' : ''; ?>" id="signin-code-group">
							<div class="form-label-row flex justify-between items-center">
								<label class="form-label" for="signin-verify-code">6-Digit Verification Code</label>
							</div>
							<div class="code-input-row">
								<input type="text" id="signin-verify-code" name="verify_code" class="form-input" value="<?php echo htmlspecialchars($prefillCode, ENT_QUOTES, 'UTF-8'); ?>" placeholder="6-digit code" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
								<button type="button" id="btn-send-code" class="btn btn-outline send-code-btn">Send Code</button>
							</div>
							<p class="field-error" role="alert"></p>
						</div>

						<div class="auth-mode-switch">
							<button type="button" id="signin-mode-toggle" class="mode-switch-btn flex items-center">
								<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
								<span id="signin-mode-toggle-text"><?php echo $initialMode === 'password' ? 'Sign in with Email Verification Code instead' : 'Sign in with Password instead'; ?></span>
							</button>
						</div>

						<button type="submit" id="btn-signin-submit" class="btn btn-primary btn-block">Sign In</button>
					</form>

					<div class="auth-switch-foot">
						New to <?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>?
						<a href="<?php echo url('login', ['tab' => 'register']); ?>" class="auth-switch-link" data-switch-view="register">Create a free account</a>
					</div>
				</div>

				<!-- Panel 2: Create Account (Register) -->
				<div id="panel-register" role="tabpanel" class="<?php echo $activeTab !== 'register' ? 'hidden' : ''; ?>">
					<div class="auth-panel-head">
						<h2 class="auth-panel-title">Create Your Account</h2>
						<p class="auth-panel-desc">Join in seconds for faster checkout and exclusive member rewards.</p>
					</div>

					<form id="form-register" novalidate>
						<div class="form-group">
							<div class="form-label-row flex justify-between items-center">
								<label class="form-label" for="register-email">Email Address</label>
							</div>
							<div class="input-wrap">
								<input type="email" id="register-email" name="email" class="form-input" value="<?php echo htmlspecialchars($prefillEmail, ENT_QUOTES, 'UTF-8'); ?>" placeholder="name@example.com" autocomplete="email" required>
							</div>
							<p class="field-error" role="alert"></p>
						</div>

						<div class="form-group">
							<div class="form-label-row flex justify-between items-center">
								<label class="form-label" for="register-password">Create Password</label>
							</div>
							<div class="input-wrap has-suffix">
								<input type="password" id="register-password" name="password" class="form-input" placeholder="At least 6 characters (letters or numbers)" autocomplete="new-password" required>
								<button type="button" class="pwd-toggle-btn" aria-label="Show password">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
								</button>
							</div>
							<p class="field-hint">Must contain at least 6 letters or numbers.</p>
							<p class="field-error" role="alert"></p>
						</div>

						<div class="form-group">
							<div class="form-label-row flex justify-between items-center">
								<label class="form-label" for="register-confirm-password">Confirm Password</label>
							</div>
							<div class="input-wrap has-suffix">
								<input type="password" id="register-confirm-password" name="confirm_password" class="form-input" placeholder="Re-enter your password" autocomplete="new-password" required>
								<button type="button" class="pwd-toggle-btn" aria-label="Show password">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
								</button>
							</div>
							<p class="field-error" role="alert"></p>
						</div>

						<p class="auth-terms-note">
							By creating an account, you agree to our <a href="<?php echo url('faq', ['f' => 19]); ?>">Terms of Service</a> and <a href="<?php echo url('faq', ['f' => 18]); ?>">Privacy Policy</a>.
						</p>

						<button type="submit" id="btn-register-submit" class="btn btn-primary btn-block">Create My Account</button>
					</form>

					<div class="auth-switch-foot">
						Already have an account?
						<a href="<?php echo url('login'); ?>" class="auth-switch-link" data-switch-view="signin">Sign In</a>
					</div>
				</div>

				<!-- Panel 3: Forgot Password -->
				<div id="panel-forgot" role="region" aria-labelledby="forgot-panel-title" class="<?php echo $activeTab !== 'forgot' ? 'hidden' : ''; ?>">
					<div class="auth-panel-head">
						<h2 class="auth-panel-title" id="forgot-panel-title">Reset Your Password</h2>
						<p class="auth-panel-desc">Enter the email address associated with your account and we'll send you a password reset link.</p>
					</div>

					<form id="form-forgot" novalidate>
						<div class="form-group">
							<div class="form-label-row flex justify-between items-center">
								<label class="form-label" for="forgot-email">Account Email Address</label>
							</div>
							<div class="input-wrap">
								<input type="email" id="forgot-email" name="email" class="form-input" value="<?php echo htmlspecialchars($prefillEmail, ENT_QUOTES, 'UTF-8'); ?>" placeholder="name@example.com" autocomplete="email" required>
							</div>
							<p class="field-error" role="alert"></p>
						</div>

						<button type="submit" id="btn-forgot-submit" class="btn btn-primary btn-block">Send Reset Link</button>
					</form>

					<div class="auth-switch-foot">
						Remembered your password?
						<a href="<?php echo url('login'); ?>" class="auth-switch-link" data-switch-view="signin">Back to Sign In</a>
					</div>
				</div>
			</section>
		</div>
	</div>
</main>
