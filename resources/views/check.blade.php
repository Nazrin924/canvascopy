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

					<h2>Welcome <span style="color: blue;">{{$name}}</span>.</h2>
						<p>This request form can be used to create courses, including for Research or Independent Study. All other course offerings are automatically created in Canvas each semester. </p>
						<p>Please login at <a target="_blank" href='{{env('BB_URL')}}'>canvas.cornell.edu</a> to verify if your course has already been created.
						<p>Canvas course sites requested through this tool must comply with Canvas@Cornell use policies [link -- <a target="_blank" href='{{env('SITE_CREATION_POLICIES_URL')}}'>{{env('SITE_CREATION_POLICIES_URL')}}</a> ]. 
						This tool can only be used to create course sites for Cornell Faculty, staff and students. If you have questions about the intended audience for your course site, please contact canvas@cornell.edu.
						</p>
						<p>Any course site requested using this tool will require manually enrolling students and other users. For instructions on creating a course with managed enrollment, visit
							<a target="_blank" href='{{env('SITE_CREATION_URL')}}'>{{env('SITE_CREATION_URL')}}</a>
						</p>
						<form class="form-basic"><fieldset class="submit">
							<input type="submit" value='Continue with Request'
								onclick='location="copyright"; return false;' />
							<input type="submit" value="Go Back"
								onclick='location="{{URL::route('index')}}"; return false;' />
						</fieldset></form>

					@include('includes/bottomNav')
				</article>
			</main>

		</div>
	</section>

	@include('includes/footer')

</body>
</html>
