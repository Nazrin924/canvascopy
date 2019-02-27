                    -------------------------------
                    --  BBTools App Information  --
                    --                           --
                    --     by: Adam Gleisner     --
                    -------------------------------


Table of Contents

1. App Overview
2. Views
3. Controllers/Middleware
4. Helpers/Queues

-------------------------------------------------------------------------------
-------------------------------------------------------------------------------

                        -----------------------
                        --  1. App Overview  --
                        -----------------------

  This app was designed to allow non-faculty Cornell Blackboard users (i.e.
  grad students, staff, etc.) to create Blackboard user accounts and courses.
  The app allows all users whose NetID (or WeillID) is not currently associated
  with a Blackboard account to create a new account on Cornell's Blackboard.
  In addition, it allows any users who are not undergraduate students to create
  a course on Blackboard, which will be visible to all Blackboard users.

  -----------------------------------------------------------------------------
  -----------------------------------------------------------------------------

                            ----------------
                            --  2. Views  --
                            ----------------

  The views (or pages) for this app are stored in the folder resources/views.
  There are several sub-categories within the views folder:

    -> emails - These contain Blade templates used to send emails on the success
       or failure of account and course creation. They are pretty simple, and
       rely on data from the queued email tasks described in section 4.

    -> errors - These are mostly ignored, I believe.

    -> includes - These are files that are included in most pages, such as the
       header and footer. Most of the code is based on a basic Cornell template.

    -> vendor - This is pretty much empty.

  In addition, there are the main view files. Each one represents a different
  page that the user could be presented with. For instance, accountError will
  be presented if there is an error in creating the user's account (for
  instance, an invalid NetID). One notable distinction to make is that between
  confirm and confirmation. The confirm page is presented to the user after they
  have filled out the course creation form, but before the request is submitted.
  It allows the user to check that the courseID is acceptable before submitting.
  The confirmation page is then displayed when they submit the request,
  detailing the next steps they should expect in the course creation process.

  The general workflow of pages for account creation and course creation
  are as follows:

  (-> = goes to page, -x> = goes to page on error, [] = optional)

  index -> createAccount [-x> createAccount]
       [-x> accountError]

  index -> check -> copyright -> info [-x> info] -> confirm -> confirmation
       [-x> courseError]                                   [-x> badCourseID]

  -----------------------------------------------------------------------------
  -----------------------------------------------------------------------------

                     ---------------------------------
                     --  3. Controllers/Middleware  --
                     ---------------------------------

  Both the controllers and middleware for this application are stored in the
  app/Http folder, under Controllers and Middleware respectively. The
  controllers are used as follows:

    -> AccountController - This is used for all stages of the account-creation
       process. Its only two methods are used for showing the account creation
       page and creating the actual account, respectively. It also verifies the
       account creation form and submits the queue request
       (which is discussed in section 4).

    -> CourseController - Controls all stages of the course-creation process.
       This includes showing the initial check and copyright pages, as well
       as displaying, verifying, confirming, and submitting the form with
       the new course information. In the confirmation stage, this controller
       will also check to see if a course with the given ID already exists, and
       warn the user if that is the case.

    -> debugController - This was used more in the development stages of the
       application, and has no production usage at all. However, we keep it
       around as a valuable tool in case the code starts acting up. To use it,
       just put the code you want into one of the testSomething methods, then
       visit the URL which is specified for that method in the routes.php file.

    -> LogController - Used to display logs to admins who have the right to
       view them. It simply displays a list of all logs that Laravel has in the
       storage folder (usually the last 5 days the app has been used), and
       pulls up the information in one of those logs upon clicking it.

    -> TesterController - Though this file is called "TesterController", it is
       actually used to control the debuggers. This is a group of people
       specified in a testers.json file in the storage directory who are able
       to modify the buttons on the index page. It is a small privilege, but
       can be useful in debugging situations. This is *not* used for the
       official testers - those are controlled in the middleware and helpers.

    -> WelcomeController - This controller simply gathers some session variables
       and displays the index page of the application.

  In addition to controllers, there are also middleware to provide
  basic authentication services. The ones we wrote are listed below:

    -> TestMiddleware - This middleware is used to check whether we are in the
       testing or production environment. It filters requests to the debug
       pages, which are only used in testing, such that if this app is in the
       production environment, these requests will not go through.

    -> VerifyUserRole - This middleware simply checks, and stores in the
       session, various aspects of a user's role. It checks whether they are a
       debugger, and thus have the ability to change the buttons on the index
       page; it checks whether they are in the testing AD group, allowing them
       to edit the account creation form to create a fake account; and it
       checks whether the current user can create courses or accounts. As
       described in section 1, this is based on whether the user isn't an
       undergraduate student and whether they don't already have a Blackboard
       account.

  There are also other middleware that I think Laravel uses on the backend -
  when in doubt, don't delete anything from this folder.

  -----------------------------------------------------------------------------
  -----------------------------------------------------------------------------

                         -------------------------
                         --  4. Helpers/Queues  --
                         -------------------------

  The helpers and queues are the real meat of the application, providing the
  interface with the Blackboard framework as well as the email functionality.
  Helpers are stored in app/Helpers; queues are stored in app/Jobs.
  First, the helpers are described below:

    -> BbPhp (and anything in the services folder) - These are part of an
       open-source framework from St. Edwards University in Austin, TX that
       can be found at this GitHub link:

       https://github.com/stedwards/Blackboard-Web-Services-PHP-Library

       This library essentially creates SOAP requests to Blackboard for
       arbitrary WS queries. The files in the services folder represent the
       different classes on Blackboard that this framework is equipped to
       interact with. This library also parses the results of a query and
       returns the result in a neatly formatted manner. The only coding
       interaction we've had with this library is to go into the BbPhp doCall
       method and log the requests and results in order to get better
       debugging information.

    -> Blackboard - This is a helper originally written by Rich and modified
       pretty heavily afterwards. It provides the interface to all of the
       Blackboard WS calls that we make - saveCourse, getCourse, saveUser,
       getUser, etc. Each of the functions works by calling the appropriate
       call to the BbPhp framework, and checking whether it returns the correct
       result. This is also the file where most of the logging happens - we
       tried to make it pretty verbose so as to give as much debugging info
       to the admins as possible.

    -> LDAP - This is an interface into Cornell's LDAP directory. The first
       method in this class, data, is used to get information about the current
       user based on their NetID. It will return that person's first and last
       names, as well as their email. In addition, it checks that person's
       educational affiliation to determine whether or not they are an
       undergraduate student, using this information to determine whether or
       not they can create a course. The second method, getADGroups, is used to
       retrieve the AD groups that a person is affiliated with. This is then
       put to use in the VerifyUserRole middleware to check whether they are
       in the BBTools testing AD group, specified in the .env file.

    -> Testers - This helper is used to get the *debuggers* of the app, which
       as mentioned above means people who are able to change the buttons on
       the index page. It also provides functionality for adding and removing
       these debuggers.

  There are also two queued jobs that this application uses. They are described
  in full detail below:

    -> DoAccountCreation - As the name implies, this job is responsible for
       controlling the account creation process. After the user submits the
       account creation form, it is put on the queue to be run every minute.
       This job will attempt to create a new user account 6 times. If it
       succeeds one of those times, it will stop and send a success email to
       the user whose account was being created. If it fails after 6 tries,
       it will stop and send a failure email to the administrator with the
       name of the account in question.

    -> DoQueueStuff - This job is responsible for the course creation process.
       After the course creation form is submitted, it is put on the queue,
       where this job will be run every minute. This job attempts to create
       a new course, as well as enroll the user as an instructor in said course.
       If it succeeds before its 6th try, it will send a success email to the
       user who created the course. However, if it fails after 6 tries, it will
       send an email to the administrator detailing the problem and the course
       in question.

  In Laravel, the queue is controlled from the file app/Console/Kernel.php. We
  chose to use a database queueing system, so jobs are stored in the database.
  Every minute (our cronjob is set up to run every minute), this file polls
  the database and determines the job that is next in the queue to run.
