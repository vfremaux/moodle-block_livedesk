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

/**
 * Strings for component 'block_html', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   block_html
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//capabilities
 
$string['livedesk:runlivedesk'] = 'Peut assister les utilisateurs';
$string['livedesk:createlivedesk'] = 'Peut créer un centre support';
$string['livedesk:managelivedesk'] = 'Peut modifier un centre support';
$string['livedesk:deletelivedesk'] = 'Peut détruire un centre support';
$string['livedesk:viewinstancestatistics'] = 'Peut voir les statistiques d\'instance';
$string['livedesk:viewuserstatistics'] = 'Peut voir les statistiques des opérateurs';
$string['livedesk:viewlivedeskstatistics'] = 'Peut voir les statistiques générales';

//admin settings 

$string['resolvereleasedelay'] = 'Temps de relâchement des demandes' ;
$string['block_livedesk_resolving_post_release'] = 'Temps de relâchement d\'un post en cours' ;
$string['block_livedesk_resolving_post_release_comment'] = 'Si un message est en attente de réponse pendant une durée supérieure à ce temps, alors le support considère que ce message peut avoir été oublié ou l\'opérateur occupé à d\'autres tâches et le remettra en jeu dans la pile.' ;
$string['attenderreleasetime'] = 'Temps de garde de l\'opérateur' ;
$string['block_livedesk_attender_release_time'] = 'Temps de garde de l\'opérateur' ;
$string['block_livedesk_attender_release_time_comment'] = 'Une fois qu\'une réponse a été donnée, ce temps peut être exploité par l\'opérateur sans être sollicité de nouveau.' ;
$string['stackovertime'] = 'Date d\'obsolescence' ;
$string['block_livedesk_stack_over_time'] = 'Date d\'obsolescence' ;
$string['block_livedesk_stack_over_time_comment'] = 'Date en desous de laquelle plus aucune demande ne sera traitée. Ceci permet aux opérateurs d\'éliminer une partie trop ancienne du flux, évitant ainsi au signal de rappel de déclencher en permanence.' ;
$string['block_livedesk_max_stack_size'] = 'Taille de pile max (default)' ;
$string['block_livedesk_max_stack_size_comment'] = 'La taille par défaut maximale d\'une pile d\'instance de centre support' ;
$string['servicestarttime'] = 'Heure d\'ouverture' ;
$string['block_livedesk_service_timerange_start'] = 'Heure d\'ouverture' ;
$string['block_livedesk_service_timerange_start_comment'] = 'Heure d\'ouverture du service support.' ;                    
$string['serviceendtime'] = 'Heure de fermeture' ;
$string['block_livedesk_service_timerange_end'] = 'Heure de fermeture' ;
$string['block_livedesk_service_timerange_end_comment'] = 'Heure de fermeture du service support.' ;

$string['adddeskinstances'] = '+ Créer un nouveau centre';
$string['blockname'] = 'Centre support pédagogique';
$string['blocksattached'] = 'Blocs attachés';
$string['commands'] = 'Commandes';
$string['configcontent'] = 'Content';
$string['configtitle'] = 'Block title';
$string['configurations'] = 'Configuration';
$string['createnewinstance'] = 'Créer un centre support';
$string['leaveblanktohide'] = 'Laisser vide pour cacher le titre';
$string['errornoname'] = 'Le nom est vide';
$string['live_queue'] = 'Pile des demandes';
$string['livedesk'] = 'Centre support pédagogique';
$string['livedeskmanagement'] = 'Gestion des centres supports';
$string['livedeskedit'] = 'Edtion de centre support';
$string['livedesk_info'] = 'Information.';
$string['livedeskdescription'] = 'Description de l\'instance';
$string['livedeskname'] = 'Nom de l\'instance';
$string['livedeskref'] = 'Service support associé :';
$string['lockedby'] = 'Traité par';
$string['manageinstances'] = 'Gérer les centres support';
$string['maxstacksize'] = 'Taille max de la pile (en entrées)';
$string['message'] = 'Message';
$string['message_sent'] = 'Votre message a été envoyé.';
$string['message_time'] = 'Heure d\'émission';
$string['messagealreadylocked'] = 'Oups!, il semble qu\'un autre opérateur ait répondu à ce message, attendez que ce message soit relâché et vous pourrez consulter la réponse et intervenir le cas échéant.';
$string['monitoredplugins'] = 'Modules surveillés';
$string['monitorableplugins'] = 'Modules à surveiller';
$string['newlivedeskblock'] = '(nouveau support)';
$string['nomonitorableplugins'] = 'Aucun module observable dans ce cours.';
$string['noreference'] = 'Il n\'y a pas encore de centre support défini pour y associer ce bloc. Si vous en avez les droits, vous devriez en créer une.';
$string['online_attenderes_count'] = 'Opérateurs connectés : ';
$string['online_users'] = 'Utilisateurs connectés';
$string['online_users_count'] = 'Utilisateurs connectés : ';
$string['origin'] = 'Origine';
$string['pluginname'] = 'Centre support pédagogique';
$string['pluginssattached'] = 'Modules surveillés';
$string['refresh_posts'] = 'Raffraichir la liste';
$string['statistics'] = 'Statistiques';
$string['user'] = 'Utilisateur';
$string['reply'] = 'Répondre dans le forum';
$string['discard'] = 'Ignorer';
$string['email_user'] = 'Envoyer un message à l\'auteur';
$string['newmessage'] = 'Nouveau message';
$string['discard_before'] = 'Ignorer les messages avant...';
$string['discard_date'] = 'Date';
$string['discard_before_txt'] = 'Ignorer tous les messages avant cette date';
$string['confirmdiscard'] = 'Confirmer';
$string['manage_livedesks'] = 'Gérer les centres supports';
$string['newmessages'] = 'Nouveaux messages... ';
$string['messagesinqueue'] = 'Messages dans le centre support : $a ';
$string['morethanmessagesinqueue'] = 'PLus de {$a->count} messages attendent dans le centre support : {$a->queue}';

// statistics

$string['attendedpostscount'] = 'Nombre de messages traités';
$string['stats'] = 'Statistiques';
$string['livedeskstats'] = 'Statistiques globales';
$string['maxattendedpostsbysession'] = 'Nombre maximum de messages traités par session';
$string['averageanswerdelay'] = 'Délai de réponse moyen';
$string['instancestats'] = 'Statistiques d\'<i>Instance</i> de Centre Support';
$string['userstats'] = 'Statistiques de l\'Opérateur';

