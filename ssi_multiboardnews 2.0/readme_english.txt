ssi_multiBoardNews adds several options that are not normally available from SMF's own ssi_boardNews function.

* Ability to specify multiple boards from which to gather topics. The limit is applied from all; there is no
  guarantee you will get 2 from each of 5 boards if you specify 5 boards and 10 topics to receive, it will
  just be the ten most recent topics across all five boards.
  
* Ability to specify one or more categories, in addition to or in place of boards, and all boards are then
  fetched.

* Ability to have the usual permissions check be overridden. In this instance, any boards can be called upon,
  but there will be no comments links displayed in the default output. The information is returned in 'array'
  mode, however, so programmers are free to make use it of it in their own layouts, e.g. combined with checks
  whether the current user is logged in or not. Note also that attachments will still be attempted to be loaded
  however may not be able to be displayed if the boards are not visible to guests and that guests have the
  permission to view attachments.

* Ability to retrieve the attachments for the different threads and display them in multiple styles.

* Ability to display avatars for the poster of those threads, optionally either on the left or right of the
  post.

* Ability to display the RSS, Atom, and RDF feed links underneath.

* Ability to filter the messages based on their icon.


All of the information for the above is also returned in an array form that can be used in your own scripts,
too, using a variation on the $output_method parameter.