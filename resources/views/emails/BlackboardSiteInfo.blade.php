<!DOCTYPE html>
<html>
	<body><div>
		<p>Dear {{$firstName}},</p>

		<p>Your new Canvas Course Site has been created with the following details:
		</p>

		<ul>
			<li>Course ID: {{$courseID}}</li>
			<li>Course Name: {{$courseName}}</li>
		</ul>

		<p>To log into your new course, visit <a href="{{env('BB_URL')}}">{{env('BB_URL')}}.</a>
			You should see your course listed on My Courses module in the section "Courses where you are: Instructor"</p>

		<p>Information on Canvas basics is available at:</p>

		<a href="{{env('BB_BASICS_URL')}}">{{env('BB_BASICS_URL')}}</a>

		<p>We recommend that you start with the &quot;Course Site Setup&quot;:</p>

		<a href="{{env('SITE_SETUP_URL')}}">{{env('SITE_SETUP_URL')}}</a>

		<p>If you need to add another instructor to the course:</p>

		<ol>
			<li>In the Control Panel, click Users and Groups and then click Users.</li>
			<li>Click Add Enrollments at the top left</li>
			<li>Type User's Cornell NetID in the "Username" field - Do not click the "Browse" button</li>
			<li>Select course role from the "Role" pull-down menu</li>
			<li>Click Submit</li>
		</ol>

		<p>Please contact us at <a href="mailto:{{env('EMAIL_ADMIN')}}">{{env('EMAIL_ADMIN')}}</a> or call (607) 255-9760 if you experience any difficulties or require assistance.
		</p>

		<p>Thank you, </p>
		<p>{{env('DISPLAY_NAME')}}</p>
	</div></body>
</html>
