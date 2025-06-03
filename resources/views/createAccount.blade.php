<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
	@include('includes/head')
</head>
<body class="cu-red45 -cu-2014 -cu-seal -cu-seal-right -campaign fixed-width -full-width -sidebar -sidebar-right -sidebar-tint -sidebar-tint-edge -border-main -two-sidebars -twin-sidebars -three-column -nav-justified -nav-float -autosize-header">

	<div id="skipnav"><a href="#main" tabindex="1">Skip to main content</a></div>

	@include('includes/header')


	<section class="band" id="main-content"><div class="band stunt-double"><div class="container stunt-double"></div></div><div class="container"><main id="main" tabindex="-1" class="aria-target border-box"><article>

		@if(!$success)
			<h2>Create Canvas@Cornell User Account</h2>
			<p>Please confirm the following information, and click
			<em>Submit</em> to request your account.
			<form action="createAccount" method="post" class="form-basic -form-compact -topline">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<fieldset class="label-align">
					<label for="txtNetID">NetID</label>
					<div class="input">
						<input class="small" type="text" id="txtNetID" @if(!session()->get('isTester')) readonly @endif name="txtNetID" value="{{$netID}}">
					</div>
					<label for="txtFirstName">First Name</label>
					<div class="input">
						<input class="small" type="text" id="txtFirstName" @if(!session()->get('isTester')) readonly @endif name="txtFirstName" value="{{$fName}}">
					</div>
					<label for="txtLastName">Last Name</label>
					<div class="input">
						<input type="text" class="small" id="txtLastName" @if(!session()->get('isTester')) readonly @endif name="txtLastName" value="{{$lName}}">
					</div>
					<label for="txtEmail">E-Mail</label>
					<div class="input">
						<input class="small" type="text" id="txtEmail" @if(!session()->get('isTester')) readonly @endif name="txtEmail" value="{{$email}}">
					</div>
					@if(!session()->get('isTester'))
						<input type="hidden" name="noChange" value="{{$netID}}">
            <input type="hidden" name="noChangeEmail" value="{{$email}}">
					@endif
				</fieldset>

				<fieldset class="submit">
					<input type="submit" name="submit" value="Submit"><input class="cancel" name="cancel" type="submit" value="Cancel">
				</fieldset>
			</form>
		@else
			<h2>Your request for a Canvas@Cornell account has been submitted.</h2>
			<p>You will be notified when your request has been processed, typically within 30 minutes. A confirmation email will be sent to: {{$email}}.
			</p>
			<p>If you have not already done so, you must configure Two-Step authentication (DUO) for your Cornell NetID. You will need to do so prior to log in to Canvas@Cornell. 
			Please see <a href="https://it.cornell.edu/twostep/get-started-two-step-login-quick-guide">Get Started with Two-Step Login: Quick Guide</a>
			</p>
			<p>To access Canvas@Cornell, visit <a target="_blank" href='{{env('BB_URL')}}'>{{env('BB_URL')}}</a>
				and log in with your Cornell credentials.
			</p>
			<form class="form-basic">
				<fieldset class="submit">
					<input type="submit" onclick="location='{{URL::route("index")}}'; return false;" value="Return to Request Form">
				</fieldset>
			</form>
		@endif

		@include('includes/bottomNav')

	</article></main></div></section>

	@include('includes/footer')

</body>
</html>
