<?php
//
// Capability definitions for the email_list block.
//
// The capabilities are loaded into the database table when the block is
// installed or updated. Whenever the capability definitions are updated,
// the module version number should be bumped up.
//
// The system has four possible values for a capability:
// CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
//
//
// CAPABILITY NAMING CONVENTION
//
// It is important that capability names are unique. The naming convention
// for capabilities that are specific to modules and blocks is as follows:
//   [mod/block]/<component_name>:<capabilityname>
//
// component_name should be the same as the directory name of the mod or block.
//
// Core moodle capabilities are defined thus:
//    moodle/<capabilityclass>:<capabilityname>
//
// Examples: mod/forum:viewpost
//           block/recent_activity:view
//           moodle/site:deleteuser
//
// The variable name for the capability definitions array follows the format
//   $<componenttype>_<component_name>_capabilities
//
// For the core capabilities, the variable is $moodle_capabilities.

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    'block/livedesk:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        )
    ),

    'block/livedesk:myaddinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
            'manager' => CAP_ALLOW,
        )
    ),

    'block/livedesk:runlivedesk' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'teacher' => CAP_ALLOW
        )
    ),

    'block/livedesk:createlivedesk' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'manager' => CAP_ALLOW
        )
    ),
        'block/livedesk:managelivedesks' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'manager' => CAP_ALLOW
        )
    ),
        'block/livedesk:deletelivedesk' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'manager' => CAP_ALLOW
        )
    ),
        'block/livedesk:viewuserstatistics' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'manager' => CAP_ALLOW,
            'user' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        )
    ),
        'block/livedesk:viewinstancestatistics' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'manager' => CAP_ALLOW,
            'teacher' => CAP_ALLOW
        )
    ),
        'block/livedesk:viewlivedeskstatistics' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'manager' => CAP_ALLOW
        )
    )

);

?>