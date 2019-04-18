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
					
					<h2>Confirm Request</h2>
					<p>The following request for a Canvas course site will be
						processed. If the following request information is correct,
						click <em>confirm</em> to submit your request.
						If you need to make changes, click <em>back</em> to edit your request.
					</p>
					<form action="confirmation" method="get" class="form-basic -form-compact -topline">
						<fieldset>
							<label for="txtCourseName">The name of your site</label>
							<div class="input">
								<input class="medium" type="text" id="txtCourseName" readonly name="txtCourseName" value="{{$courseName}}">
							</div>
							<label for="txtCourseID">Course-ID will appear as</label>
							<div class="input">
								<input class="medium" type="text" id="txtCourseID" readonly name="txtCourseID" value="{{$courseID}}">
							</div>
						</fieldset>

						<fieldset class="submit">
							<input type="submit" name="submit" value="Confirm">
							<input class="cancel" name="cancel" type="submit" value="Back">
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
