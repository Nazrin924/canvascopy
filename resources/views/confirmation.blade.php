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

              <p>Your course will be available momentarily.
                A confirmation email should be sent soon, and will include instructions on accessing Canvas.</p>
              <p>To log into your new course, point your browser to
                <a target="_blank" href="{{env('BB_URL')}}">{{env('BB_URL')}}</a>
                and login.
                You should see your course listed on the Dashboard.
              </p>

              <p>Information on Canvas basics is available at:
                <a target="_blank" href="{{env('BB_BASICS_URL')}}">{{env('BB_BASICS_URL')}}</a>
              </p>

              <p>To add users to your course, please see Canvas guides at:
                <a target="_blank" href="{{env('ADD_USERS_URL')}}">{{env('ADD_USERS_URL')}}</a>
              </p>


              <p>Please contact us at <a target="_blank" href="mailto:{{env('EMAIL_ADMIN')}}">{{env("EMAIL_ADMIN")}}</a>
                 or call (607) 255-9760 if you have any questions regarding this course request.

              </p>
              <p>Thank you,</p>
              <p>
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
