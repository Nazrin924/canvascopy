<!DOCTYPE html>
<html lang="en" class="no-js">

	<body>

		<p>Dear {{$firstName}}</p>
		<p>Your new Canvas user account has been created with the following details:</p>

		<ul>
			<li>User-ID: {{str_replace("@cumed", "", $netID)}}</li>
			<li>First Name: {{$firstName}}</li>
			<li>Last Name: {{$lastName}}</li>
			<li>Email: {{$email}}</li>
		</ul>
		
		<p>If you have not already done so, you must configure Two-Step authentication (DUO) for your Cornell NetID. 
		You will need to do so prior to log in to Canvas@Cornell. Please see <a href="https://it.cornell.edu/twostep/get-started-two-step-login-quick-guide">Get Started with Two-Step Login: Quick Guide</a></p>

		<p>To access Canvas@Cornell, visit <a href="{{env('BB_URL')}}">{{env('BB_URL')}}</a>
			 and log in with your Cornell credentials.</p>

		<p>Instructions on using Canvas@Cornell are at:
			<a href="{{env('CVS_HELP_URL')}}">{{env('CVS_HELP_URL')}}</a>
		</p>

		<p>Please contact us at <a href="mailto:{{env('EMAIL_ADMIN')}}">{{env('EMAIL_ADMIN')}}</a>
			 if you experience any difficulties.</p>

		<p>Thank you, </p>
		<p>{{env('DISPLAY_NAME')}}</p>
	</body>
</html>
