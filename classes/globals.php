<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die;

/**
* GLOBAL PARAMETERS.
*/

$STATUS_ARRAY = array('new'=>'mail.png',
                    'in_progress'=>'mail-open-document.png',
                    'opened'=>'mail-open-document.png',
                    'answered'=>'mail-reply.png',
                    'locked'=>'locked_mail.png',
                    'discarded'=>'discard_mail.png'
);

$STATUS_ARRAY_TITLES = array('new' => get_string('newmessage', 'block_livedesk'),
                    'in_progress' => get_string('inprogress', 'block_livedesk'),
                    'opened' => get_string('opened', 'block_livedesk'),
                    'answered' => get_string('answered', 'block_livedesk'),
                    'locked' => get_string('locked', 'block_livedesk'),
                    'discarded' => get_string('discarded', 'block_livedesk')
);

//post status
define('MAIL_STATUS_NEW','new');
define('MAIL_STATUS_inprogress','inprogress');
define('MAIL_STATUS_OPENED','opened');
define('MAIL_STATUS_ANSWERED','answered');
define('MAIL_STATUS_DISCARDED','discarded');
