The livedesk provides a real time synchronous survey of forum and interpersonal communication
modules.

It has been disigned for online intensive training where students need to have a responsive
support service to their pedagogic queries.

The block now supports only forums, but is extensible to other modules if required.

Administrators can instanciate some Livedesk Office and will stores an event queue
per instance. Some blocks can be installed into course space, opening access to a
selected Livedesk instance. Users with capabiity can enter the livedesk instance through 
the block and thus open a support session. 

One or more observable modules can be attached to a Livedesk. The event queue will receive
instant notification of something input into the activity, and will provide a link to 
a reply interface (or anything capable to help handling the event, depending on the 
activity module implementation).

#####
Implementing Global Notification
#####

In order to enable notification sitewide the following the are steps needed .
- Install the Livedesk Block
- Open footer.html of your theme .
- Copy the following code and paste it there.

        <?php
        if(is_dir($CFG->dirroot.'/blocks/livedesk/'))
        {
            require_once($CFG->dirroot.'/blocks/livedesk/lib.php');
            block_livedesk_setup_theme_notification();
           
        }
        ?>

- Save the footer.html

