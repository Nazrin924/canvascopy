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
					@if(count($errors) > 0)
					<div class="panel fill accent-red dialog dialog-error" role="dialog" aria-labelledby="form-dialog-title" aria-describedby="form-dialog-description">
						<h4 id="form-dialog-title" class="-puretext"><span class="fa"></span>Error</h4>
						<ol id="form-dialog-description" class="list-menu vertical">
							<li>The form below has one or more errors.</li>
						</ol>
					</div>
					@endif


						<h2>Create Canvas Course Site</h2>
						<p>This tool can be used to request non-academic Canvas sites.</p>
						<p>Please supply the following information and click <em>Submit</em></p>

						<form action="{{URL::route('courseInfoPost')}}" method="post" class="form-basic -form-compact -topline">

							<input type="hidden" name="_token" value="{{ csrf_token() }}">

							<fieldset>

								<label class="required @if($errors->has('txtCourseName')) error @endif" for="txtCourseName">Canvas Course Name</label>
								<div class="input @if($errors->has('txtCourseName')) error @endif">
									<input @if($errors->has('txtCourseName')) aria-invalid="true" @endif type="text" class="medium" aria-required="true" id="txtCourseName" name="txtCourseName" value="{{Session::has('txtCourseName')? Session::get('txtCourseName'):old('txtCourseName')}}">
									<div class="form-message">
										@if($errors->has('txtCourseName'))
                      {{$errors->first('txtCourseName')}}
										@else
                      (e.g. Research Study: Health Communication)
                    @endif</div>
									</div>
									
								<label class="required @if($errors->has('txtCourseID')) error @endif" for="txtCourseID">Canvas Course Short Name</label>
								<div class="input @if($errors->has('txtCourseID')) error @endif">
									<input type="text" @if($errors->has('txtCourseID')) aria-invalid="true" @endif class="medium" aria-required="true" id="txtCourseID" name="txtCourseID" value="{{Session::has('txtCourseID')? Session::get('txtCourseID'):old('txtCourseID')}}">
									<div class="form-message">
										@if($errors->has('txtCourseID'))
                      {{$errors->first('txtCourseID')}}
										@elseif(isset($error))
                      {{$error}}
										@else
                      (e.g. Research Communication Spring 2024)
										@endif
									</div>
								</div>									

								<label class="required @if($errors->has('txtLastName')) error @endif" for="txtLastName">Instructor's Last Name</label>
								<div class="input @if($errors->has('txtLastName')) error @endif">
									<input @if($errors->has('txtLastName')) aria-invalid="true" @endif aria-required="true" class="medium" type="text" id="txtLastName" name="txtLastName" value="{{Session::has('txtLastName')? Session::get('txtLastName'):old('txtLastName')}}">
									<div class="form-message">
										@if($errors->has('txtLastName'))
                      {{$errors->first('txtLastName')}}
                    @else
                     
                    @endif
									</div>
								</div>


							</fieldset>

							<fieldset class="submit">
								<input type="submit" name="submit" value="Submit">
								<button type='button' onclick='location="{{URL::route('index')}}"'>Cancel</button>
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
