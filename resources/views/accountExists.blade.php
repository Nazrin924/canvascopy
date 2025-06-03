<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		@include('includes/head')
	</head>
	<body class="cu-red45 -cu-2014 -cu-seal -cu-seal-right -campaign fixed-width -full-width -sidebar -sidebar-right -sidebar-tint -sidebar-tint-edge -border-main -two-sidebars -twin-sidebars -three-column -nav-justified -nav-float -autosize-header">

		<div id="skipnav"><a href="#main" tabindex="1">Skip to main content</a></div>

		@include('includes/header')


		<section class="band" id="main-content">
			<!--<div class="band stunt-double"><div class="container stunt-double"></div></div>-->

			<div class="container">

				<main id="main" tabindex="-1" class="aria-target border-box">
					<article>

						<h2 >Error</h2>

						<p>You cannot create a Canvas user as you already have an account.</p>
						<p>You may log in at <a href="{{env('BB_URL')}}">{{env('BB_SHORT_URL')}}</a></p>
						<p></p>


						@include('includes/bottomNav')
					</article>
				</main>
			</div>
		</section>

		@include('includes/footer')
	</body>
</html>
