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
	<div class="account-dashboard-wrapper max-w-4xl mx-auto my-12 px-4 animate-fade-in">
		<!-- Main Container -->
		<div class="bg-white dark:bg-surface-glass rounded-[32px] shadow-2xl overflow-hidden border border-honey-100/50 dark:border-white/5 backdrop-blur-xl">
			
			<!-- Hero Header -->
			<div class="relative pt-12 pb-8 px-8 text-center overflow-hidden">
				<!-- Decorative Background Glow -->
				<div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-64 bg-honey-200/20 dark:bg-honey-500/10 rounded-full blur-3xl opacity-50 -z-10"></div>
				
				<div class="relative inline-block group mb-6">
					<div class="absolute inset-0 bg-honey-400 rounded-full blur opacity-20 group-hover:opacity-40 transition-opacity animate-pulse"></div>
					<div class="relative w-24 h-24 bg-honey-600 text-white rounded-full flex items-center justify-center text-3xl font-bold shadow-xl border-4 border-white dark:border-gray-800">
						<?php echo strtoupper( substr( $current_user->display_name, 0, 1 ) ); ?>
					</div>
				</div>
				
				<h2 class="font-serif text-4xl text-gray-800 dark:text-honey-50 mb-2 font-bold tracking-tight">
					Welcome back, <span class="text-honey-600 dark:text-honey-400"><?php echo esc_html( $current_user->display_name ); ?></span>
				</h2>
				<p class="text-gray-500 dark:text-gray-400 text-sm font-medium tracking-wide"><?php echo esc_html( $current_user->user_email ); ?></p>
			</div>

			<!-- Navigation Tabs -->
			<div class="flex justify-center px-8 pb-10">
				<div class="inline-flex p-1.5 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/10">
					<a href="?view=overview" class="flex items-center gap-2 px-6 py-3 rounded-xl transition-all duration-300 <?php echo $view === 'overview' ? 'bg-white dark:bg-honey-500 text-honey-700 dark:text-white shadow-md font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-honey-600 dark:hover:text-honey-400'; ?>">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
						<span>Overview</span>
					</a>
					<a href="?view=orders" class="flex items-center gap-2 px-6 py-3 rounded-xl transition-all duration-300 <?php echo $view === 'orders' ? 'bg-white dark:bg-honey-500 text-honey-700 dark:text-white shadow-md font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-honey-600 dark:hover:text-honey-400'; ?>">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
						<span>Orders</span>
					</a>
					<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="flex items-center gap-2 px-6 py-3 rounded-xl text-red-500/80 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300 font-medium">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
						<span>Logout</span>
					</a>
				</div>
			</div>

			<!-- Dynamic Content -->
			<div class="px-8 pb-12">
				<?php if ( $view === 'overview' ) : ?>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-slide-up">
						<a href="?view=orders" class="group relative block bg-gray-50 dark:bg-white/5 rounded-3xl p-8 hover:bg-honey-50 dark:hover:bg-honey-900/20 transition-all duration-500 border border-transparent hover:border-honey-100 dark:hover:border-honey-500/30 overflow-hidden">
							<!-- Decorative Icon Background -->
							<div class="absolute -right-6 -bottom-6 w-32 h-32 text-honey-600 transition-colors opacity-5 dark:opacity-[0.03] group-hover:opacity-10">
								<svg fill="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
							</div>
							
							<div class="relative">
								<div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center shadow-sm mb-4 group-hover:scale-110 transition-transform duration-500">
									<svg class="w-6 h-6 text-honey-600 dark:text-honey-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
								</div>
								<h3 class="text-gray-600 dark:text-gray-300 text-xs font-bold uppercase tracking-[0.1em] mb-2"><span class="text-honey-600 dark:text-honey-400">T</span>otal <span class="text-honey-600 dark:text-honey-400">O</span>rders</h3>
								<div class="flex items-baseline gap-2">
									<span class="text-4xl font-bold text-gray-900 dark:text-honey-50"><?php echo $order_count; ?></span>
									<span class="text-honey-600 dark:text-honey-400 text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity">View All →</span>
								</div>
							</div>
						</a>

						<div class="group relative bg-gray-50 dark:bg-white/5 rounded-3xl p-8 transition-all duration-500 border border-transparent overflow-hidden">
							<!-- Decorative Icon Background -->
							<div class="absolute -right-6 -bottom-6 w-32 h-32 text-honey-600 opacity-5 dark:opacity-[0.03]">
								<svg fill="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
							</div>

							<div class="relative">
								<div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center shadow-sm mb-4">
									<svg class="w-6 h-6 text-honey-600 dark:text-honey-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
								</div>
								<h3 class="text-gray-600 dark:text-gray-300 text-xs font-bold uppercase tracking-[0.1em] mb-2"><span class="text-honey-600 dark:text-honey-400">M</span>ember <span class="text-honey-600 dark:text-honey-400">S</span>ince</h3>
								<span class="text-2xl font-bold text-gray-900 dark:text-honey-50"><?php echo date( 'F Y', strtotime( $current_user->user_registered ) ); ?></span>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $view === 'orders' ) : ?>
					<div class="animate-slide-up">
						<div class="flex items-center justify-between mb-8">
							<h3 class="font-serif text-2xl font-bold text-gray-800 dark:text-honey-50">Recent History</h3>
							<div class="h-px flex-1 bg-gray-100 dark:bg-white/10 mx-6"></div>
						</div>
						
						<?php if ( empty( $orders ) ) : ?>
							<div class="text-center py-20 bg-gray-50/50 dark:bg-white/5 rounded-[2rem] border border-dashed border-gray-200 dark:border-white/10">
								<div class="bee-scene mb-8">
									<div class="bee text-6xl animate-float">🐝</div>
									<div class="flowers text-3xl mt-4 opacity-70">🌻 🌼 🌷</div>
								</div>
								<h4 class="text-2xl font-bold text-gray-800 dark:text-honey-50 mb-3 font-serif">Empty Hive!</h4>
								<p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">It looks like you haven't placed any orders yet. Let's find something sweet for you.</p>
								<a href="<?php echo esc_url( home_url( '/shop' ) ); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-honey-500 to-honey-600 text-white px-10 py-4 rounded-2xl font-bold hover:scale-105 transition-all shadow-xl shadow-honey-500/20 active:scale-95">
									<span>Start Shopping</span>
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
								</a>
							</div>
						<?php else : ?>
							<div class="overflow-x-auto">
								<table class="w-full text-left">
									<thead>
										<tr class="text-gray-400 dark:text-gray-500 text-[10px] uppercase font-bold tracking-[0.2em]">
											<th class="pb-6 w-24">Order #</th>
											<th class="pb-6">Date</th>
											<th class="pb-6">Status</th>
											<th class="pb-6 text-right">Total</th>
										</tr>
									</thead>
									<tbody class="divide-y divide-gray-100 dark:divide-white/5">
										<?php foreach ( $orders as $order ) : 
											$total = get_post_meta( $order->ID, '_order_total', true );
											$status = $order->post_status === 'lead' ? 'Processing' : ucfirst($order->post_status);
											$status_class = $status === 'Processing' ? 'text-honey-700 bg-honey-100 dark:text-honey-300 dark:bg-honey-900/40' : 'text-gray-500 bg-gray-100 dark:text-gray-400 dark:bg-white/10';
										?>
										<tr class="group hover:bg-gray-50/80 dark:hover:bg-white/5 transition-all duration-300">
											<td class="py-6 font-bold text-gray-900 dark:text-honey-100">#<?php echo $order->ID; ?></td>
											<td class="py-6 text-gray-600 dark:text-gray-400 font-medium">
												<div class="flex flex-col">
													<span><?php echo get_the_date( 'M j, Y', $order ); ?></span>
													<span class="text-[10px] opacity-60"><?php echo get_the_date( 'g:i A', $order ); ?></span>
												</div>
											</td>
											<td class="py-6">
												<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $status_class; ?>">
													<?php echo $status; ?>
												</span>
											</td>
											<td class="py-6 text-right font-bold text-gray-900 dark:text-honey-50 text-lg">
												$<?php echo number_format( $total / 100, 2 ); ?>
											</td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		
		<!-- Footer Note -->
		<div class="text-center mt-8 text-gray-400 dark:text-gray-500 text-sm">
			<p>Need help? Contact our <a href="/contact" class="text-honey-600 dark:text-honey-400 hover:underline">Support Bee</a></p>
		</div>
	</div>
	
	<style>
		@keyframes fade-in {
			from { opacity: 0; transform: translateY(10px); }
			to { opacity: 1; transform: translateY(0); }
		}
		@keyframes slide-up {
			from { opacity: 0; transform: translateY(20px); }
			to { opacity: 1; transform: translateY(0); }
		}
		@keyframes float {
			0%, 100% { transform: translateY(0) rotate(0); }
			50% { transform: translateY(-15px) rotate(5deg); }
		}
		.animate-fade-in { animation: fade-in 0.8s ease-out forwards; }
		.animate-slide-up { animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
		.animate-float { animation: float 3s ease-in-out infinite; }
		
		.account-dashboard-wrapper {
			perspective: 1000px;
		}
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
		<div class="max-w-md mx-auto bg-white dark:bg-surface-glass rounded-3xl shadow-2xl overflow-hidden border border-honey-100 dark:border-white/10 relative">
			<!-- Decorative Top Border -->
			<div class="h-2 bg-gradient-to-r from-honey-400 via-yellow-400 to-honey-500"></div>

			<!-- Tabs -->
			<div class="flex text-center bg-honey-50/30 dark:bg-honey-900/10 border-b border-honey-100 dark:border-white/10">
				<a href="?action=login" class="flex-1 py-5 text-[11px] font-bold uppercase tracking-[0.2em] transition-all <?php echo $action === 'login' ? 'text-honey-700 dark:text-honey-400 bg-white dark:bg-white/5 shadow-sm' : 'text-gray-400 dark:text-gray-500 hover:text-honey-600 dark:hover:text-honey-400 hover:bg-white/50 dark:hover:bg-white/10'; ?>">
					Sign In
				</a>
				<a href="?action=register" class="flex-1 py-5 text-[11px] font-bold uppercase tracking-[0.2em] transition-all <?php echo $action === 'register' ? 'text-honey-700 dark:text-honey-400 bg-white dark:bg-white/5 shadow-sm' : 'text-gray-400 dark:text-gray-500 hover:text-honey-600 dark:hover:text-honey-400 hover:bg-white/50 dark:hover:bg-white/10'; ?>">
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
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-sm text-gray-400 hover:text-honey-600 transition-colors">Forgot your password?</a>
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
			box-sizing: border-box;
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
			box-sizing: border-box;
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
