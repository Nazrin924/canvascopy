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
            @if($success)

              <p>Your new Canvas Course Site is being created with the following details:</p>

              <ul>
                <li><strong>Course-ID: {{$courseID}}</strong></li>
                <li><strong>Course Name: {{$courseName}}</strong></li>
              </ul>

              <p>Please note that this process may take a few hours.
                A confirmation email should be sent soon, and will include instructions on accessing Canvas.</p>
              <p>To log into your new course, point your browser to
                <a target="_blank" href="{{env('BB_URL')}}">{{env('BB_URL')}}</a>
                and login.
                You should see your course listed on My Courses module in the section "Courses where you are: Instructor"
              </p>

              <p>Information on Canvas basics is available at:
                <a target="_blank" href="{{env('BB_BASICS_URL')}}">{{env('BB_BASICS_URL')}}</a>
              </p>

              <p>We recommend that you start with the "Course Site Setup":
                <a target="_blank" href="{{env('SITE_SETUP_URL')}}">{{env('SITE_SETUP_URL')}}</a>
              </p>

              <p>If you need to add another instructor to the course:</p>
              <ol>
                <li>In the Control Panel, click Users and Groups and then click Users.</li>
                <li>Click Add Enrollments at the top left.</li>
                <li>Type User’s Cornell NetID in the “Username” field</li>
                <li>Select “Instructor” from the Role pull-down menu</li>
                <li>Click Submit</li>
             </ol>

              <p>Please contact us at <a target="_blank" href="mailto:{{env('EMAIL_ADMIN')}}">{{env("EMAIL_ADMIN")}}</a>
                 or call (607) 255-9760 if you experience any difficulties or require assistance.
              </p>
              <p>Thank you,
                {{env('DISPLAY_NAME')}}
              </p>

            <a href="./">Back to home page</a>
          @endif
          @include('includes/bottomNav')
        </article>
      </main>

    </div>
  </section>

  @include('includes/footer')

</body>
</html>
