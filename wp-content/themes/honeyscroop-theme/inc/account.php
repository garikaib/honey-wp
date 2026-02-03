<?php
/**
 * Account Functionality
 *
 * Handles the [honeyscroop_account] shortcode, registration, and account dashboard.
 *
 * @package Honeyscroop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle User Registration.
 */
function honeyscroop_handle_registration() {
	if ( isset( $_POST['honeyscroop_register_nonce'] ) && wp_verify_nonce( $_POST['honeyscroop_register_nonce'], 'honeyscroop_register' ) ) {
		$username = sanitize_user( $_POST['username'] );
		$email    = sanitize_email( $_POST['email'] );
		$password = $_POST['password'];

		$errors = new WP_Error();

		if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
			$errors->add( 'field', 'All fields are required.' );
		}

		// Verify Turnstile
		if ( function_exists( 'honeyscroop_verify_turnstile' ) ) {
			$token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( $_POST['cf-turnstile-response'] ) : '';
			if ( empty( $token ) || ! honeyscroop_verify_turnstile( $token ) ) {
				$errors->add( 'turnstile', 'Security verification failed. Please complete the CAPTCHA.' );
			}
		}

		if ( username_exists( $username ) ) {
			$errors->add( 'username', 'Username already exists.' );
		}

		if ( email_exists( $email ) ) {
			$errors->add( 'email', 'Email already registered.' );
		}

		if ( empty( $errors->get_error_codes() ) ) {
			$user_id = wp_create_user( $username, $password, $email );
			if ( ! is_wp_error( $user_id ) ) {
				// Auto login
				wp_set_current_user( $user_id );
				wp_set_auth_cookie( $user_id );
				wp_safe_redirect( home_url( '/my-account/' ) );
				exit;
			} else {
				// Pass error to view? Simplified for now.
				wp_die( $user_id->get_error_message() );
			}
		} else {
			// Pass error to view?
			wp_die( $errors->get_error_message() );
		}
	}
}
add_action( 'init', 'honeyscroop_handle_registration' );

/**
 * Render the Account Shortcode.
 */
function honeyscroop_account_shortcode() {
	if ( is_user_logged_in() ) {
		return honeyscroop_render_dashboard();
	} else {
		return honeyscroop_render_auth_forms();
	}
}
add_shortcode( 'honeyscroop_account', 'honeyscroop_account_shortcode' );

/**
 * Render Dashboard (Logged In)
 */
function honeyscroop_render_dashboard() {
	$current_user = wp_get_current_user();
	$view = isset( $_GET['view'] ) ? $_GET['view'] : 'overview';

	// Fetch Orders for this user (by email)
	$args = array(
		'post_type'  => 'shop_order',
		'meta_key'   => '_order_customer_email',
		'meta_value' => $current_user->user_email,
		'post_status'=> 'any',
		'posts_per_page' => -1
	);
	$orders = get_posts( $args );
	$order_count = count( $orders );

	ob_start();
	?>
	<div class="account-container max-w-3xl mx-auto my-10 p-8 border border-gray-100 dark:border-white/10 rounded-2xl shadow-sm bg-surface">
		<!-- Header -->
		<div class="text-center mb-10">
			<div class="w-20 h-20 bg-amber-100 dark:bg-honey-900/30 text-amber-600 dark:text-honey-400 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4">
				<?php echo strtoupper( substr( $current_user->display_name, 0, 1 ) ); ?>
			</div>
			<h2 class="text-2xl font-bold text-gray-800 dark:text-honey-50">Welcome, <?php echo esc_html( $current_user->display_name ); ?>!</h2>
			<p class="text-gray-500 dark:text-gray-400"><?php echo esc_html( $current_user->user_email ); ?></p>
		</div>

		<!-- Navigation -->
		<div class="flex justify-center flex-wrap gap-2 mb-10">
			<a href="?view=overview" class="px-6 py-2.5 rounded-xl transition-all <?php echo $view === 'overview' ? 'bg-amber-100 dark:bg-honey-500 text-amber-700 dark:text-white font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5'; ?>">Overview</a>
			<a href="?view=orders" class="px-6 py-2.5 rounded-xl transition-all <?php echo $view === 'orders' ? 'bg-amber-100 dark:bg-honey-500 text-amber-700 dark:text-white font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5'; ?>">Order History</a>
			<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="px-6 py-2.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl font-medium transition-all">Logout</a>
		</div>

		<!-- Overview Tab -->
		<?php if ( $view === 'overview' ) : ?>
			<div class="grid grid-cols-2 gap-6">
				<a href="?view=orders" class="block bg-gray-50 dark:bg-white/5 hover:bg-amber-50 dark:hover:bg-honey-900/20 rounded-2xl p-8 text-center transition-all group cursor-pointer border border-transparent hover:border-amber-100 dark:hover:border-honey-500/30">
					<h3 class="text-gray-500 dark:text-gray-400 text-[11px] font-bold uppercase tracking-widest mb-3">My Orders</h3>
					<span class="text-4xl font-bold text-gray-800 dark:text-honey-50 group-hover:text-honey-600 dark:group-hover:text-honey-400 transition-colors"><?php echo $order_count; ?></span>
				</a>
				<div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-8 text-center border border-transparent">
					<h3 class="text-gray-500 dark:text-gray-400 text-[11px] font-bold uppercase tracking-widest mb-3">Member Since</h3>
					<span class="text-xl font-bold text-gray-800 dark:text-honey-50"><?php echo date( 'F Y', strtotime( $current_user->user_registered ) ); ?></span>
				</div>
			</div>
		<?php endif; ?>

		<!-- Orders Tab -->
		<?php if ( $view === 'orders' ) : ?>
			<div>
				<h3 class="text-2xl font-serif font-bold text-gray-800 dark:text-honey-50 mb-8 border-b dark:border-white/10 pb-4">Order History</h3>
				
				<?php if ( empty( $orders ) ) : ?>
					<!-- Empty State with Bee Animation -->
					<div class="text-center py-16">
						<div class="bee-scene mb-8">
							<div class="bee text-5xl animate-bounce">🐝</div>
							<div class="flowers text-3xl mt-4 opacity-70">🌻 🌼 🌷</div>
						</div>
						<h4 class="text-xl font-bold text-gray-800 dark:text-honey-50 mb-3">No orders placed yet</h4>
						<p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">The bees are waiting for your first request! Start your honey journey today.</p>
						<a href="<?php echo esc_url( home_url( '/shop' ) ); ?>" class="inline-block bg-honey-600 dark:bg-honey-500 text-white px-8 py-3 rounded-xl font-bold hover:bg-honey-700 dark:hover:bg-honey-600 transition-all shadow-lg shadow-honey-500/20">Start Shopping</a>
					</div>
				<?php else : ?>
					<div class="overflow-x-auto">
						<table class="w-full text-left">
							<thead>
								<tr class="border-b border-gray-100 dark:border-white/10">
									<th class="pb-4 font-bold text-gray-400 dark:text-gray-500 text-[11px] uppercase tracking-widest">Order #</th>
									<th class="pb-4 font-bold text-gray-400 dark:text-gray-500 text-[11px] uppercase tracking-widest">Date</th>
									<th class="pb-4 font-bold text-gray-400 dark:text-gray-500 text-[11px] uppercase tracking-widest">Status</th>
									<th class="pb-4 font-bold text-gray-400 dark:text-gray-500 text-[11px] uppercase tracking-widest text-right">Total</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-50 dark:divide-white/5">
								<?php foreach ( $orders as $order ) : 
									$total = get_post_meta( $order->ID, '_order_total', true );
									$status = $order->post_status === 'lead' ? 'Pending' : ucfirst($order->post_status);
									$status_color = $status === 'Pending' ? 'text-amber-600 bg-amber-50 dark:text-honey-300 dark:bg-honey-900/30' : 'text-gray-600 bg-gray-50 dark:text-gray-400 dark:bg-white/5';
								?>
								<tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
									<td class="py-5 font-bold text-gray-900 dark:text-honey-50"><?php echo $order->ID; ?></td>
									<td class="py-5 text-gray-600 dark:text-gray-400"><?php echo get_the_date( 'M j, Y', $order ); ?></td>
									<td class="py-5">
										<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $status_color; ?>"><?php echo $status; ?></span>
									</td>
									<td class="py-5 text-right font-bold text-gray-900 dark:text-honey-50">$<?php echo number_format( $total / 100, 2 ); ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	
	<style>
		@keyframes float {
			0%, 100% { transform: translateY(0); }
			50% { transform: translateY(-10px); }
		}
		.bee { display: inline-block; animation: float 2s ease-in-out infinite; }
	</style>
	<?php
	return ob_get_clean();
}

/**
 * Render Auth Forms (Logged Out)
 */
function honeyscroop_render_auth_forms() {
	$action = isset( $_GET['action'] ) ? $_GET['action'] : 'login';
	
	ob_start();
	?>
	<div class="auth-wrapper py-16 px-4">
		<div class="max-w-md mx-auto bg-white dark:bg-surface-glass rounded-3xl shadow-2xl overflow-hidden border border-amber-100 dark:border-white/10 relative">
			<!-- Decorative Top Border -->
			<div class="h-2 bg-gradient-to-r from-amber-400 via-yellow-400 to-amber-500"></div>

			<!-- Tabs -->
			<div class="flex text-center bg-amber-50/30 dark:bg-honey-900/10 border-b border-amber-100 dark:border-white/10">
				<a href="?action=login" class="flex-1 py-5 text-[11px] font-bold uppercase tracking-[0.2em] transition-all <?php echo $action === 'login' ? 'text-amber-700 dark:text-honey-400 bg-white dark:bg-white/5 shadow-sm' : 'text-gray-400 dark:text-gray-500 hover:text-amber-600 dark:hover:text-honey-400 hover:bg-white/50 dark:hover:bg-white/10'; ?>">
					Sign In
				</a>
				<a href="?action=register" class="flex-1 py-5 text-[11px] font-bold uppercase tracking-[0.2em] transition-all <?php echo $action === 'register' ? 'text-amber-700 dark:text-honey-400 bg-white dark:bg-white/5 shadow-sm' : 'text-gray-400 dark:text-gray-500 hover:text-amber-600 dark:hover:text-honey-400 hover:bg-white/50 dark:hover:bg-white/10'; ?>">
					Register
				</a>
			</div>

			<div class="p-8 md:p-10">
				
				<!-- Header Text -->
				<div class="text-center mb-10">
					<h3 class="font-serif text-3xl text-gray-800 dark:text-honey-50 mb-3">
						<?php echo $action === 'login' ? 'Welcome Back' : 'Join the Hive'; ?>
					</h3>
					<p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
						<?php echo $action === 'login' ? 'Enter your details to access your account.' : 'Create an account to track orders and checkout faster.'; ?>
					</p>
				</div>

				<?php if ( $action === 'register' ) : ?>
					<!-- Registration Form -->
					<form method="post" class="register-form">
						<?php wp_nonce_field( 'honeyscroop_register', 'honeyscroop_register_nonce' ); ?>
						
						<div class="form-group">
							<label>Username</label>
							<input type="text" name="username" required placeholder="johndoe">
						</div>

						<div class="form-group">
							<label>Email Address</label>
							<input type="email" name="email" required placeholder="john@example.com">
						</div>

						<div class="form-group">
							<label>Password</label>
							<input type="password" name="password" required placeholder="••••••••">
						</div>

						<?php echo honeyscroop_render_turnstile( 'register' ); ?>

						<button type="submit" class="register-btn">
							Create Account
						</button>
					</form>
				<?php else : ?>
					<!-- Styled WP Login Form -->
					<div class="wp-login-styled">
						<?php
						wp_login_form( array(
							'echo'           => true,
							'redirect'       => home_url( '/my-account/' ),
							'form_id'        => 'loginform',
							'label_username' => 'Username or Email',
							'label_password' => 'Password',
							'label_remember' => 'Keep me signed in',
							'label_log_in'   => 'Sign In',
							'id_submit'      => 'wp-submit-styled',
						) );
						?>
						<?php echo honeyscroop_render_turnstile( 'account-login' ); ?>
						
						<?php 
						if ( function_exists( 'honeyscroop_add_magic_link_form' ) ) {
							honeyscroop_add_magic_link_form(); 
						}
						?>

						<div class="mt-6 text-center">
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-sm text-gray-400 hover:text-amber-600 transition-colors">Forgot your password?</a>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		
		<!-- Trust Badges -->
		<div class="text-center mt-8 text-gray-400 text-sm flex justify-center gap-6">
			<span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Secure Login</span>
			<span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Fast Checkout</span>
		</div>
	</div>

	<style>
		/* Custom Styling for WP Login Form Output */
		.wp-login-styled form { display: flex; flex-direction: column; gap: 1.25rem; }
		.wp-login-styled p.login-username, .wp-login-styled p.login-password { margin-bottom: 0; }
		
		.wp-login-styled label { 
			display: block; 
			font-size: 0.75rem; 
			font-weight: 700; 
			text-transform: uppercase; 
			letter-spacing: 0.05em;
			color: #6B7280; 
			margin-bottom: 0.5rem; 
			margin-left: 0.25rem;
		}
		
		.wp-login-styled input[type="text"], 
		.wp-login-styled input[type="password"] { 
			width: 100%; 
			padding: 0.75rem 1.25rem; 
			background-color: #F9FAFB; 
			border: 1px solid #E5E7EB; 
			border-radius: 0.75rem; 
			font-size: 1rem;
			color: #1F2937;
			outline: none; 
			transition: all 0.2s; 
		}
		
		.wp-login-styled input[type="text"]:focus, 
		.wp-login-styled input[type="password"]:focus { 
			background-color: #fff;
			border-color: #FBBF24; 
			box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2); 
		}
		
		.wp-login-styled .login-remember { margin-top: -0.5rem; }
		.wp-login-styled .login-remember label { 
			text-transform: none; 
			font-weight: 500; 
			color: #6B7280; 
			display: flex; 
			align-items: center; 
			gap: 0.5rem; 
			cursor: pointer;
		}
		
		.wp-login-styled input[type="submit"] { 
			width: 100%; 
			background: linear-gradient(to right, #F59E0B, #EAB308); 
			color: white; 
			font-weight: 700; 
			padding: 1rem; 
			border-radius: 0.75rem; 
			border: none; 
			cursor: pointer; 
			transition: all 0.2s; 
			font-size: 1rem;
			box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);
			margin-top: 0.5rem;
		}
		
		.wp-login-styled input[type="submit"]:hover { 
			background: linear-gradient(to right, #D97706, #CA8A04); 
			transform: translateY(-1px);
			box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
		}
		
		.wp-login-styled input[type="submit"]:active {
			transform: scale(0.98);
		}

		/* Registration Form Styles */
		.register-form { display: flex; flex-direction: column; gap: 1.25rem; }
		.register-form .form-group { margin-bottom: 0; }
		
		.register-form label { 
			display: block; 
			font-size: 0.75rem; 
			font-weight: 700; 
			text-transform: uppercase; 
			letter-spacing: 0.05em;
			color: #6B7280; 
			margin-bottom: 0.5rem; 
			margin-left: 0.25rem;
		}
		
		.register-form input[type="text"], 
		.register-form input[type="email"],
		.register-form input[type="password"] { 
			width: 100%; 
			padding: 0.75rem 1.25rem; 
			background-color: #F9FAFB; 
			border: 1px solid #E5E7EB; 
			border-radius: 0.75rem; 
			font-size: 1rem;
			color: #1F2937;
			outline: none; 
			transition: all 0.2s; 
		}
		
		.register-form input[type="text"]:focus, 
		.register-form input[type="email"]:focus,
		.register-form input[type="password"]:focus { 
			background-color: #fff;
			border-color: #FBBF24; 
			box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2); 
		}

		.register-btn { 
			width: 100%; 
			background: linear-gradient(to right, #F59E0B, #EAB308); 
			color: white; 
			font-weight: 700; 
			padding: 1rem; 
			border-radius: 0.75rem; 
			border: none; 
			cursor: pointer; 
			transition: all 0.2s; 
			font-size: 1rem;
			box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);
			margin-top: 0.5rem;
		}
		
		.register-btn:hover { 
			background: linear-gradient(to right, #D97706, #CA8A04); 
			transform: translateY(-1px);
			box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
		}
		
		.register-btn:active {
			transform: scale(0.98);
		}

		/* Autofill Fix for All Forms */
		input:-webkit-autofill,
		input:-webkit-autofill:hover, 
		input:-webkit-autofill:focus, 
		input:-webkit-autofill:active {
			-webkit-box-shadow: 0 0 0 30px #F9FAFB inset !important;
			-webkit-text-fill-color: #1F2937 !important;
			transition: background-color 5000s ease-in-out 0s;
		}

		/* Dark Mode Overrides */
		html.dark .account-container {
			background: var(--bg-surface);
			border-color: rgba(255, 255, 255, 0.1);
		}
		html.dark .wp-login-styled input[type="text"], 
		html.dark .wp-login-styled input[type="password"],
		html.dark .register-form input[type="text"], 
		html.dark .register-form input[type="email"],
		html.dark .register-form input[type="password"] {
			background-color: rgba(255, 255, 255, 0.05);
			border-color: rgba(255, 255, 255, 0.1);
			color: #FFFBEB;
		}
		html.dark .wp-login-styled input[type="text"]:focus, 
		html.dark .wp-login-styled input[type="password"]:focus,
		html.dark .register-form input[type="text"]:focus, 
		html.dark .register-form input[type="email"]:focus,
		html.dark .register-form input[type="password"]:focus {
			border-color: var(--color-honey-500);
			background-color: rgba(255, 255, 255, 0.08);
		}
		html.dark .wp-login-styled label,
		html.dark .register-form label {
			color: #9CA3AF;
		}
		html.dark input:-webkit-autofill {
			-webkit-box-shadow: 0 0 0 30px #1A1A14 inset !important;
			-webkit-text-fill-color: #FFFBEB !important;
		}
	</style>
	<?php
	return ob_get_clean();
}
