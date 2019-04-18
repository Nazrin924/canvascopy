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

		<p>To log into your new course, visit <a href="{{env('BB_URL')}}">{{env('BB_URL')}}</a>
			You should see your course listed on the Dashboard.</p>

		<p>Information on Canvas basics is available at:</p>

		<a href="{{env('BB_BASICS_URL')}}">{{env('BB_BASICS_URL')}}</a>

		<p>To add users to your course, please see Canvas guides at: </p>

		<a href="{{env('ADD_USERS_URL')}}">{{env('ADD_USERS_URL')}}</a>

		<p>Please contact us at <a href="mailto:{{env('EMAIL_ADMIN')}}">{{env('EMAIL_ADMIN')}}</a> or call (607) 255-9760 if you have any questions regarding this course request.
		</p>

		<p>Thank you, </p>
		<p>{{env('DISPLAY_NAME')}}</p>
	</div></body>
</html>
