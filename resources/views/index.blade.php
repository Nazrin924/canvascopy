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
					<article class='text-center'>
						<h2 >Welcome, <span style="color: blue">{{$fName." ".$lName}} ({{$netID}})</span></h2>

						<p>If you are not {{$fName." ".$lName}}, please exit the browser and re-authenticate under your own credentials</p>

						@if($canCreateSite || $canCreateUser)

							<form class="form-basic"><fieldset class="submit">
								@if($canCreateUser)
									<input type="submit" value="Create a Canvas@Cornell user account"
										onclick="location = 'createAccount'; return false;"></a>
									@if($canCreateSite)<br /> @endif
								@endif
								@if($canCreateSite)

								<input type="submit" value="Create a Canvas course site"
									onclick="location = 'createCourse'; return false;"></a>
								@endif
							</fieldset></form>

						@endif
						<p>Information about using Canvas@Cornell at Cornell can be found at: <a target="_blank" href='{{env('CVS_HELP_URL')}}'>{{env('CVS_HELP_URL')}}</a></p>

						@if(isset($isDebugger) && $isDebugger) @include("includes/testerMenu") @endif

						{{--var_dump(session()->all())--}}
					</article>
				</main>
			</div>
		</section>

		@include('includes/footer')
	</body>
</html>
