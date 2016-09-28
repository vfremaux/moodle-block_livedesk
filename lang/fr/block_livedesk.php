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
 * @package   block_livedesk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//capabilities
$string['livedesk:addinstance'] = 'Peut ajouter une instance';
$string['livedesk:myaddinstance'] = 'Can add an instance à la page personnalisée';
$string['livedesk:runlivedesk'] = 'Peut assister les utilisateurs';
$string['livedesk:createlivedesk'] = 'Peut créer un centre support';
$string['livedesk:managelivedesk'] = 'Peut modifier un centre support';
$string['livedesk:deletelivedesk'] = 'Peut détruire un centre support';
$string['livedesk:viewinstancestatistics'] = 'Peut voir les statistiques d\'instance';
$string['livedesk:viewuserstatistics'] = 'Peut voir les statistiques des opérateurs';
$string['livedesk:viewlivedeskstatistics'] = 'Peut voir les statistiques générales';

//admin settings 

$string['adddeskinstances'] = '+ Créer un nouveau centre';
$string['allusers'] = 'Tous les usagers vérifient les notifications (déconseillé)';
$string['attenderreleasetime'] = 'Temps de garde de l\'opérateur (minutes)' ;
$string['blockname'] = 'Centre support pédagogique';
$string['blocksattached'] = 'Blocs attachés';
$string['capabilitycontrol'] = 'Contrôle d\'une capacité';
$string['commands'] = 'Commandes';
$string['configattenderreleasetime'] = 'Temps de garde de l\'opérateur' ;
$string['configattenderreleasetime_desc'] = 'Une fois qu\'une réponse a été donnée, ce temps peut être exploité par l\'opérateur sans être sollicité de nouveau.' ;
$string['configcontent'] = 'Content';
$string['configkeepalivedelay'] = 'Signal de présence' ;
$string['configkeepalivedelay_desc'] = 'La période (en secondes) avec laquelle le Cente Support émet une requête de présence vers le serveur.' ;
$string['configlivenotificationcontrol'] = 'Acces aux notifications' ;
$string['configlivenotificationcontrol_desc'] = 'Sélectionne la façon dont le code de notification est ajouté au code de la page Moodle.' ;
$string['configlivenotificationcontrolvalue'] = 'Valeur de controle du code de notification' ;
$string['configlivenotificationcontrolvalue_desc'] = 'Le nom d\'une capacité Moodle contrôlant la présence du code de notification, ou le nom d\'un champ de profil (test non vide).' ;
$string['configmaxstacksize'] = 'Taille de pile max (default)' ;
$string['configmaxstacksize_desc'] = 'La taille par défaut maximale d\'une pile d\'instance de centre support' ;
$string['confignotificationbacktrackrange'] = 'Détection d\'activité recente' ;
$string['confignotificationbacktrackrange_desc'] = 'Le temps en minutes d\'examen arrière pour savoir si des centres support ont une activité recente à notifier.' ;
$string['confignotificationonscreentime'] = 'Temps de maintien de la notification' ;
$string['confignotificationonscreentime_desc'] = 'Le temps (millisecondes) pendant lequel la notification reste visible à l\'écran. Doit être inférieur à la période de rafraichissement.' ;
$string['confignotificationrefreshtime'] = 'Période de rafraichissement de notification' ;
$string['confignotificationrefreshtime_desc'] = 'La période (millisecondes) avec laquelle les notifications sont demandées au serveur. Des temps courts peuvent augmenter la charge permanente du serveur.' ;
$string['configrefresh'] = 'Raffraichissement' ;
$string['configrefresh_desc'] = 'La période de raffraichissement (en secondes) des informations du Centre Support.' ;
$string['configresolvingpostrelease'] = 'Temps de relâchement d\'un post en cours' ;
$string['configresolvingpostrelease_desc'] = 'Si un message est en attente de réponse pendant une durée supérieure à ce temps, alors le support considère que ce message peut avoir été oublié ou l\'opérateur occupé à d\'autres tâches et le remettra en jeu dans la pile.' ;
$string['configservicetimerangeend'] = 'Heure de fermeture' ;
$string['configservicetimerangeend_desc'] = 'Heure de fermeture du service support.' ;
$string['configservicetimerangestart'] = 'Heure d\'ouverture' ;
$string['configservicetimerangestart_desc'] = 'Heure d\'ouverture du service support.' ;                    
$string['configstackovertime'] = 'Délai d\'obsolescence (minutes)' ;
$string['configstackovertime_desc'] = 'Date en dessous de laquelle plus aucune demande ne sera traitée. Ceci permet aux opérateurs d\'éliminer une partie trop ancienne du flux, évitant ainsi au signal de rappel de déclencher en permanence.' ;
$string['configtitle'] = 'Titre visible du bloc';
$string['configurations'] = 'Configuration';
$string['confirmdiscard'] = 'Confirmer';
$string['createnewinstance'] = 'Créer un centre support';
$string['deleted'] = 'Module Supprimé';
$string['discard'] = 'Ignorer';
$string['discard_before'] = 'Ignorer les messages avant...';
$string['discard_before_txt'] = 'Ignorer tous les messages avant cette date';
$string['discard_date'] = 'Date';
$string['editdeskinstance'] = 'Modifier une instance de centre support';
$string['email_user'] = 'Envoyer un message à l\'auteur';
$string['errornoname'] = 'Le nom est vide';
$string['globalparams'] = 'Réglages globaux';
$string['instance_notbounded_to_livedesk'] = 'Ce bloc n\'est actuellement lié à aucune instance de centre support. Il doit être configuré.';
$string['instances'] = 'Instances';
$string['keepalivedelay'] = 'Signal de présence';
$string['leaveblanktohide'] = 'Laisser vide pour cacher le titre';
$string['live_queue'] = 'Pile des demandes';
$string['livedesk'] = 'Centre support pédagogique';
$string['livedesk_info'] = 'Information.';
$string['livedeskdescription'] = 'Description de l\'instance';
$string['livedeskmanagement'] = 'Gestion des centres supports';
$string['livedeskname'] = 'Nom de l\'instance';
$string['livedeskref'] = 'Service support associé :';
$string['lockedby'] = 'Traité par';
$string['manage_livedesks'] = 'Gérer les centres supports';
$string['manageinstances'] = 'Gérer les centres support';
$string['maxstacksize'] = 'Taille max de la pile (en entrées)';
$string['message'] = 'Message';
$string['message_sent'] = 'Votre message a été envoyé.';
$string['message_time'] = 'Heure d\'émission';
$string['messagealreadylocked'] = 'Oups!, il semble qu\'un autre opérateur ait répondu à ce message, attendez que ce message soit relâché et vous pourrez consulter la réponse et intervenir le cas échéant.';
$string['messagesinqueue'] = 'Messages dans le centre support : {$a} ';
$string['monitorableplugins'] = 'Modules à surveiller';
$string['monitoredplugins'] = 'Modules surveillés';
$string['morethanmessagesinqueue'] = 'Plus de {$a->count} messages attendent dans le centre support : {$a->queue}';
$string['newlivedesk'] = 'Nouveau Service Support';
$string['newlivedeskblock'] = '(nouveau support)';
$string['newmessage'] = 'Nouveau message';
$string['newmessages'] = 'Nouveaux messages... ';
$string['nomonitorableplugins'] = 'Aucun module observable dans ce cours.';
$string['noreference'] = 'Il n\'y a pas encore de centre support défini pour y associer ce bloc. Si vous en avez les droits, vous devriez en créer une.';
$string['notificationmail'] = 'Texte de notification';
$string['notificationtitle'] = 'Titre du mail de notification (laisser vide pour le message par défaut)';
$string['online_attenderes_count'] = 'Opérateurs connectés : ';
$string['online_users'] = 'Utilisateurs connectés';
$string['online_users_count'] = 'Utilisateurs connectés : ';
$string['origin'] = 'Origine';
$string['pluginname'] = 'Centre support pédagogique';
$string['pluginssattached'] = 'Modules surveillés';
$string['profilefieldcontrol'] = 'Contrôle d\'un champ de profil';
$string['received'] = 'Reception de Demande Support ';
$string['refresh'] = 'Raffraichissement' ;
$string['refresh_posts'] = 'Raffraichir la liste';
$string['reply'] = 'Répondre dans le forum';
$string['resolvereleasedelay'] = 'Temps de relâchement des demandes (minutes)' ;
$string['sendnotification'] = 'Emission de notification d\'acceptation d\'un message dans la queue';
$string['serviceendtime'] = 'Heure de fermeture' ;
$string['servicestarttime'] = 'Heure d\'ouverture' ;
$string['stackovertime'] = 'Date d\'obsolescence' ;
$string['statistics'] = 'Statistiques';
$string['task_livedesk'] = 'Traitements de fond Livedesk';
$string['user'] = 'Utilisateur';

// statistics

$string['attendedpostscount'] = 'Nombre de messages traités';
$string['stats'] = 'Statistiques';
$string['livedeskstats'] = 'Statistiques globales';
$string['maxattendedpostsbysession'] = 'Nombre maximum de messages traités par session';
$string['averageanswerdelay'] = 'Délai de réponse moyen';
$string['instancestats'] = 'Statistiques d\'<i>Instance</i> de Centre Support';
$string['userstats'] = 'Statistiques de l\'Opérateur';
$string['showhideanswered'] = 'Afficher / Cacher les messages traités';
$string['showhidelocked'] = 'Afficher / Cacher les messages verouillés ';
$string['showhidediscarded'] = 'Afficher / Cacher les messages ignorés ';
$string['getasexcel'] = 'Télécharger au format excel';

