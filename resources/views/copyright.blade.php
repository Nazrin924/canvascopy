<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
	@include('includes/head')
</head>
<body class="cu-red45 -cu-2014 -cu-seal -cu-seal-right -campaign fixed-width -full-width -sidebar -sidebar-right -sidebar-tint -sidebar-tint-edge -border-main -two-sidebars -twin-sidebars -three-column -nav-justified -nav-float -autosize-header">

	<div id="skipnav"><a href="#main" tabindex="1">Skip to main content</a></div>

	@include('includes/header')


	<section class="band" id="main-content">
		<div class="band stunt-double"><div class="container stunt-double"></div></div>

		<div class="container">

			<main id="main" tabindex="-1" class="aria-target border-box">
				<article>
					
					<h2>Cornell University Copyright Notification</h2>
					<p>The University expects that users of these services will in good
						faith post material in compliance with copyright laws. If you have
						questions about the use of any material, please send mail to
						<a target="_blank" href='mailto:copyright@cornell.edu'>copyright@cornell.edu</a>
					</p>
					<p class="panel fill accent-gold">To the best of my knowledge, I assert that all the
						copyrighted material that I will post on this site will be
						used in a manner consistent with copyright law. I either
						have permission to use the material or believe, after
						performing a "fair use" assessment, that it falls within
						the "fair use" exception to the requirement of getting permission.
					</p>
					<p style="color: blue"><strong><em>By clicking the button below, you are affirming your agreement to the above statement.
					</em></strong></p>
					<form action='copyright' class="form-basic" method='post'>
						<input type="hidden" name="copyrighted" value="yes"></input>
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<fieldset class="submit">
							
							<input type="submit" name="submit" value="Yes, I Agree To The Above Statement"></input>
						</fieldset>
					</form>
					
					@include('includes/bottomNav')
				</article>
			</main>
		</div>
	</section>

	@include('includes/footer')

</body> 
</html>
