
<p>We heard that you lost your password. Sorry about that!<p>
<p>But don’t worry! You can use the following link to reset your password:</p>
<br>
<a href="{{ route('reset_password', [ $user_slack, $password_reset_token]) }}">Reset Password</a>
<br>
<p>To get a new password reset link, visit <a href="{{ route('forgot_password') }}">Forgot Password</a></p>
<br>
Thanks