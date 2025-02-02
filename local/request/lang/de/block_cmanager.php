<?php
// --------------------------------------------------------- 
// block_request is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// block_request is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// COURSE REQUEST MANAGER BLOCK FOR MOODLE
// by Kyle Goslin & Daniel McSweeney
// Copyright 2012-2014 - Institute of Technology Blanchardstown.
//
// A special thanks to Alexander Kiy for this translation.
// --------------------------------------------------------- 
/**
 * COURSE REQUEST MANAGER
  *
 * @package    block_request
 * @copyright  2014 Kyle Goslin, Daniel McSweeney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Aktionen';
$string['addviewcomments'] = 'Kommentare hinzufügen / ansehen';
$string['administratorConfig'] = 'Weitere Einstellungen';
$string['allarchivedrequests'] = 'Alle archivierten Anträge';
$string['allowSelfCategorization'] = 'Nutzer/innen erlauben die Kurskategorie selbst auszuwählen';
$string['allowSelfCategorization_desc'] = 'Wenn diese Einstellung aktiviert ist, wird der Benutzer aufgefordert, eine Kategorie in Moodle auszuwählen, in der der Kurs platziert werden soll';
$string['approve'] = 'Genehmigen';
$string['approvecourse'] = 'Kurs genehmigen';
$string['approverequest_New'] = 'Neuer Kurs wurde erstellt';
$string['approverequest_Process'] = 'Übergabeprozess hat begonnen';
$string['approverequest_Title'] = 'Kursantrag - Antrag genehmigen';
$string['approvingcourses'] = 'Kurse genehmigen...';
$string['archivedrequests'] = 'Archivierte Anträge';
$string['back'] = 'Zurück';
$string['block_admin'] = 'Warteschlange für Kursanträge';
$string['block_config'] = 'Einstellungen';
$string['block_manage'] = 'Anträge verwalten';
$string['block_request'] = 'Antrag stellen';
$string['bulkactions'] = 'Massenverarbeitung';
$string['bulkapprove'] = 'Mehrere bestätigen';
$string['bulkdeny'] = 'Mehrere ablehnen';
$string['cancel'] = 'Abbrechen';
$string['catlocation'] = 'Kategorieliste';
$string['ChangesSaved'] = 'Änderungen gespeichert';
$string['clearHistoryTitle'] = 'Verlauf löschen';
$string['clickhere'] = 'Hier klicken';
$string['clickHereToReturn'] = 'Hier klicken um zurückzukehren';
$string['request'] = 'CRManager';
$string['requestActions'] = 'Aktionen';
$string['request:addinstance'] = 'Neuen Block \'Course Request Manager\' hinzufügen';
$string['requestConfirmCancel'] = 'Sind Sie sicher, dass Sie diesen Antrag abbrechen wollen?';
$string['requestDisplay'] = 'Course Request Manager';
$string['requestDisplaySearchForm'] = 'Antragsformular bearbeiten - Seite 1';
$string['requestEnrolmentInstruction'] = 'Der Course Request Manager kann automatisch einen Einschreibeschlüssel generieren. Ebenso können Sie den Benutzer dazu auffordern einen Einschreibeschlüssel seiner Wahl einzugeben.';
$string['requestEnrolmentOption1'] = 'Automatisch erzeugter Einschreibeschlüssel';
$string['requestEnrolmentOption2'] = 'Benutzer auffordern einen Einschreibeschlüssel einzugeben';
$string['requestExstingTab'] = 'Ausstehende Anträge';
$string['requestHistoryTab'] = 'Bisherige Anträge';
$string['request:myaddinstance'] = 'Neuen Block \'Course Request Manager\' auf MyMoodle hinzufügen';
$string['requestnonePending'] = 'Keine ausstehenden Anträge vorhanden';
$string['requestRequestBtn'] = 'Neues Kurs-Setup anfragen';
$string['requestStats'] = 'request Statistik';
$string['requestWelcome'] = 'Herzlich willkommen zum Moodle Course Request Manager.';
$string['comments'] = 'Kommentare';
$string['comments_date'] = 'Datum / Uhrzeit';
$string['comments_Forward'] = 'Alle Kommentare werden zusätzlich automatisch per E-Mail verschickt.';
$string['comments_from'] = 'Von';
$string['comments_Header'] = 'Kommentare ansehen / hinzufügen';
$string['comments_message'] = 'Nachricht';
$string['comments_PostComment'] = 'Kommentar absenden';
$string['config_addemail'] = 'E-Mail Adresse';
$string['configure'] = 'Course Request Manager konfigurieren';
$string['configureadminsettings'] = 'Verwaltung';
$string['configureadminsettings_desc'] = 'Weitere Einstellungen dem Course Request Manager hinzufügen';
$string['configurecourseformfields'] = 'Antragsformular konfigurieren - Seite 1';
$string['configurecoursemanagersettings'] = 'Course Request Manager Einstellungen bearbeiten';
$string['configure_defaultStartDate'] = 'Kursbeginn';
$string['configure_defaultStartDateInstructions'] = 'Wählen Sie einen Kursbeginn, der bei neuen Kursen voreingestellt ist.';
$string['configure_delete'] = 'Sind Sie sicher, dass Sie diesen Antrag löschen wollen?';
$string['configure_deleteMail'] = 'Möchten Sie diese E-Mail-Adresse wirklich löschen?';
$string['configureemailsettings'] = 'E-Mail-Einstellungen bearbeiten';
$string['configureemailsettings_desc'] = 'Dieser Abschnitt ermöglicht es, die E-Mail Einstellungen zu bearbeiten';
$string['configure_EnrolmentKey'] = 'Einschreibeschlüssel';
$string['configure_EnrolmentKeyInstruction'] = 'Der Course Request Manager kann automatisch einen Einschreibeschlüssel generieren oder den Benutzer zur Eingabe eines Einschreibeschlüssels auffordern. Wenn Sie den Benutzer dazu auffordern, einen Einschreibeschlüssel einzugeben, so wird ein Eingabefeld zur ersten Seite des Antragsformulars hinzugefügt.';
$string['configureHeader'] = 'Kursantragsverwaltung - request Einstellungen';
$string['configure_instruction1'] = 'Die Einstellungen erlauben dem Administrator, E-Mail Vorlagen, Kursnamenskonventionen und Kurseinstellungsvorlagen wie Kursbeginn und Einschreibeschlüssel zu konfigurieren.';
$string['configure_instruction2'] = 'Das Antragsformular ist in zwei Seiten geteilt. Die erste Seite fordert den Benutzer auf, den Namen und Kurznamen des Kurses einzugeben. Dieser Link erlaubt es, Felder zu benennen und ebenso zusätzliche Kurseinstellungen zu setzen.';
$string['configure_instruction3'] = 'Dies erlaubt es, die zweite Seite des Antrags zu konfigurieren. Auf dieser Seite können Sie Elemente erstellen, die vom Benutzer ausgefüllt werden müssen. Diese Informationen werden vom Course Request Manager nicht benötigt, aber sie erlauben dem Administrator weitere Informationen für den Kursantrag einzuholen die ihm bei seiner Entscheidung über den Antrag helfen können.';
$string['configure_leaveblankmail'] = 'Feld leer lassen, um zu verhindern dass eine E-Mail versendet wird.';
$string['Continue'] = 'Fortfahren';
$string['courseexists'] = 'Kurs besteht bereits';
$string['requestadmin'] = 'Kursantragsverwaltung';
$string['requestline1'] = 'Beachten Sie bitte die Kursnamenskonventionen ihrer Einrichtung';
$string['creationdate'] = 'Erstellungsdatum';
$string['currentrequests'] = 'Aktuelle Anträge';
$string['delete'] = 'Löschen';
$string['deleteAllRequests'] = 'Alle aktuellen und archivierten Anträge löschen';
$string['deleteOnlyArch'] = 'Alle archivierten Anträge löschen';
$string['deny'] = 'Ablehnen';
$string['denycourse'] = 'Kursantrag ablehnen';
$string['denyrequest_Btn'] = 'Antrag ablehnen';
$string['denyrequest_Instructions'] = 'Begründen Sie, warum der Kursantrag abgelehnt wurde';
$string['denyrequest_reason'] = 'Begründen Sie, warum der Kursantrag abgelehnt wurde';
$string['denyrequest_Title'] = 'Kursantragsverwaltung - Antrag ablehnen';
$string['Disabled'] = 'Deaktiviert';
$string['displayListWarningSideText'] = 'Der gewählte Kurzname existiert bereits. Achtung Administrator: Dieser Kursantrag wird von der Massenverarbeitung nicht erfasst.';
$string['displayListWarningTitle'] = 'ACHTUNG';
$string['edit'] = 'Bearbeiten';
$string['email_AdminMail'] = 'Administrator E-Mail';
$string['email_commentNotification'] = 'E-Mail kommentieren';
$string['emailConfig'] = 'E-Mail Einstellungen';
$string['emailConfigContents'] = 'Kommunikationsnachrichten E-Mails bearbeiten';
$string['emailConfigHeader'] = 'Hier können Sie die E-Mails bearbeiten, welche an die Benutzer als Benachrichtigung versendet werden, wenn sich der Status ihres Antrags verändert hat.';
$string['emailConfigInfo'] = 'Dieses Feld enthält E-Mailadressen von Administratoren, die benachrichtigt werden, wenn ein Antrag eingegangen ist.';
$string['emailConfigSectionContents'] = 'E-Mail Texte bearbeiten';
$string['emailConfigSectionHeader'] = 'E-Mails konfigurieren';
$string['email_courseCode'] = 'Kursschlüssel';
$string['email_courseName'] = 'Kursname';
$string['email_currentOwner'] = 'Aktuelle E-Mail des Besitzers';
$string['email_enrolmentKey'] = 'Einschreibeschlüssel';
$string['email_fullURL'] = 'Gesamte URL zum neuen Kurs';
$string['email_handover'] = 'Anfrage übergeben';
$string['email_newCourseApproved'] = 'Neuer Kurs wurde bewilligt';
$string['email_noReply'] = 'E-Mail Adresse';
$string['email_noReplyInstructions'] = 'Diese Funktion sendet E-Mails an Administratoren und Benutzer wenn Kursanfragen bearbeitet wurden. Bitte geben Sie eine Adresse ein, die als Absender angegeben wird, z.B. noreply@meinedomain.de';
$string['email_progCode'] = 'Programmcode';
$string['email_progName'] = 'Programmname';
$string['email_requestDenied'] = 'Benachrichtigung über gescheiterte Anfrage';
$string['email_requestNewModule'] = 'Neuen Kurs anfragen';
$string['emailSubj_adminApproved'] = 'Moodle Benutzeranfrage bewilligt';
$string['emailSubj_adminDeny'] = 'Anfrage abgewiesen';
$string['emailSubj_adminNewComment'] = 'Neuer Kommentar';
$string['emailSubj_adminNewRequest'] = 'Neue moodle Anfrage';
$string['emailSubj_Comment'] = 'Kommentar';
$string['emailSubj_From'] = 'Von';
$string['emailSubj_mailSent1'] = 'E-Mail wurde verschickt an';
$string['emailSubj_mailSent2'] = 'In Ihrem Namen';
$string['emailSubj_pleasecontact'] = 'Bitte kontaktieren';
$string['emailSubj_requester'] = 'Anfragesteller E-Mail';
$string['emailSubj_teacherHandover'] = 'Anfrage zur Prüfung';
$string['emailSubj_userApproved'] = 'Moodle Anfrage bestätigt!';
$string['emailSubj_userDeny'] = 'Anfrage abgelehnt';
$string['emailSubj_userNewComment'] = 'Neuer Kommentar';
$string['emailSubj_userNewRequest'] = 'Neue Moodle Anfrage';
$string['email_sumLink'] = 'Ganzer Course Request Manager Anfrage Link';
$string['emailswillbesent'] = 'E-Mails werden an den Besitzer vom Kurs verschickt. Nach dem Sie eine anfrage gesendet haben warten Sie bitte auf eine Antwort.';
$string['email_UserMail'] = 'Benutzer E-Mail';
$string['Enabled'] = 'Aktiviert';
$string['entryFields_AddNewItem'] = 'Neues Objekt hinzufügen';
$string['entryFields_Description'] = 'Beschreibung';
$string['entryFields_Dropdown'] = 'Zusätzliches Auswahlfeld';
$string['entryFields_DropdownDescription'] = 'Vielleicht möchten Sie optionale Auswahlmenüs mit Werten hinzufügen, die ihnen Helfen Kurse zu kategorisieren. Zum Beispiel wenn ihre Einrichtung Kurse in Vollzeit, Teilzeit oder Fernbildung anbietet. Sie können diese Optionen zu einem optionalen Auswahlmenü hinzufügen.';
$string['entryFields_instruction1'] = 'Bearbeiten sie die Einstellungen der ersten Seite des Antragsformulars. Diese Seite fragt den Benutzer nach dem Namen und Kurznamen des Kurses.';
$string['entryFields_instruction2'] = 'Für jedes der zwei unten aufgeführten Felder können Sie den Namen des Feldes und wie es für den Benutzer erscheint, ebenso wie den Hilfetext, verändern.';
$string['entryFields_Name'] = 'Name';
$string['entryFields_status'] = 'Status';
$string['entryFields_TextfieldOne'] = 'Konfigurationsfeld für den Kurs-Kurznamen';
$string['entryFields_TextfieldTwo'] = 'Konfigurationsfeld für den Kurs-Namen';
$string['existingrequests'] = 'Vorhandene Anfragen';
$string['formBuilder_about'] = 'Über';
$string['formBuilder_addedItemsTxt'] = 'Die folgenden Objekte sind hier Aufgelistet';
$string['formBuilder_addItemBtnTxt'] = 'Neues Objekt hinzufügen';
$string['formBuilder_addUserTxt'] = 'Text';
$string['formBuilder_confirmDelete'] = 'Möchten Sie dieses Formular löschen?';
$string['formBuilder_createNewText'] = 'Neues Formular erstellen';
$string['formBuilder_currentActiveForm'] = 'Aktuell aktives Formular';
$string['formBuilder_currentActiveFormInstructions'] = 'Bitte wählen Sie das Formular, dass Sie verwenden möchten aus dem Auswahlmenü. Sie können das aktuell aktive Formular jederzeit ändern oder ein neues erstellen und auswählen.';
$string['formBuilder_deleteForm'] = 'Formular löschen';
$string['formBuilder_dropdownTxt'] = 'Auswahlmenü';
$string['formBuilder_editForm'] = 'Formular bearbeiten';
$string['formBuilder_editingForm'] = 'Formular bearbeiten';
$string['formBuilder_instructions'] = 'Der Kursantragsmanager wurde entwickelt, um Benutzern die Möglichkeit zu geben zusätzliche Informationen zu ihrer Kursanfrage einzugeben. Dieses Formular kann so konfiguriert werden, dass möglichst viele oder weniger Informationen vom Benutzer bezogen werden. Wenn Sie Informationen von Benutzer benötigen um einen Kurs zu erstellen, dann können Sie dies hier entsprechend Einstellen.
Die typischen Fälle sind bereits vorhanden.';
$string['formBuilder_instructions1'] = 'Kursnummer und Kurstitel';
$string['formBuilder_instructions2'] = 'Gewünschtes Start- und Enddatum';
$string['formBuilder_instructions3'] = 'Gewünschter Einschreibeschlüssel';
$string['formBuilder_instructions4'] = 'Zusätzliche Berechtigungen';
$string['formBuilder_instructions5'] = 'Kursformat';
$string['formBuilder_instructions6'] = 'Viele andere...';
$string['formBuilder_leftTxt'] = 'Name';
$string['formBuilder_manageFormsText'] = 'Formulare verwalten';
$string['formBuilder_name'] = 'Anfrageformular konfigurieren - Seite 2';
$string['formBuilder_p2_addNewField'] = 'Neues Feld hinzufügen';
$string['formBuilder_p2_dropdown1'] = 'Neu hinzufügen....';
$string['formBuilder_p2_dropdown2'] = 'Textfeld';
$string['formBuilder_p2_dropdown3'] = 'Textbereich';
$string['formBuilder_p2_dropdown4'] = 'Auswahlbutton Gruppe';
$string['formBuilder_p2_dropdown5'] = 'Auswahlmenü';
$string['formBuilder_p2_dropdown6'] = 'Text';
$string['formBuilder_p2_error'] = 'Fehler: Keine Id hinzugefügt.';
$string['formBuilder_p2_header'] = '"Zusätzliche Informationen"-Formular Editor';
$string['formBuilder_p2_instructions'] = 'Dieses Formular kann so konfiguriert werden, dass Sie so viele oder so wenige Informationen vom Benutzer bekommen, wie Sie benötigen. Wenn Sie Informationen vom Benutzer benötigen die ihnen beim erstellen des Kurses helfen, dann konfigurieren Sie dies hier. Fügen Sie hier einfach Felder hinzu oder entfernen Sie diese, die der Benutzer während einer Kursanfrage ausfüllen muss. Diese Informationen können ihnen beim Bearbeiten der Kursanfragen helfen.';
$string['formBuilder_previewForm'] = 'Formularvorschau';
$string['formBuilder_previewHeader'] = 'Formularvorschau';
$string['formBuilder_previewInstructions1'] = 'Vervollständigen Sie bitte dieses Formular so sorgfältig wie möglich';
$string['formBuilder_previewInstructions2'] = 'Beachten Sie lokale Richtlinien in ihrer Kursanfrage';
$string['formBuilder_radioTxt'] = 'Auswahlbuttons';
$string['formBuilder_returntoCM'] = 'Zurück zum Course Request Manager';
$string['formBuilder_saveTxt'] = 'Speichern';
$string['formBuilder_selectAny'] = 'Wählen Sie ein Formular um es zu bearbeiten.';
$string['formBuilder_selectDescription'] = 'Wählen Sie ein Formular um es für Anfragen zu verwenden';
$string['formBuilder_selectOption'] = 'Formular auswählen..';
$string['formBuilder_shownbelow'] = 'wird unten angezeigt';
$string['formBuilder_step2'] = 'Schritt 2: Weitere Details';
$string['formBuilder_textAreaTxt'] = 'Textbereich';
$string['formBuilder_textFieldTxt'] = 'Textfeld';
$string['formfieldsHeader'] = 'Anfrageformular bearbeiten - Seite 1';
$string['formpage1'] = 'Formular Seite 1';
$string['formpage2'] = 'Formular Seite 2';
$string['formpage2builder'] = 'Formular Seite 2 - Builder';
$string['historynav'] = 'Verlauf';
$string['informationform'] = 'Anfrageformular bearbeiten - Seite 2';
$string['lecturingstaff'] = 'Dozenten';
$string['managersettings'] = 'Manager Einstellungen';
$string['modcode'] = 'Kursnummer';
$string['modexists'] = 'Der Kurs den Sie angefragt haben scheint bereits auf dem Server zu existieren.';
$string['modname'] = 'Kurs Name';
$string['modrequestfacility'] = 'Kursanfrageverwaltung';
$string['myarchivedrequests'] = 'Meine archivierten Anfragen';
$string['namingConvetion'] = 'Kursnamenskonvention';
$string['namingConvetionInstruction'] = 'Der Course Request Manager wird ihre Kurse mit der ausgewählten Namenskonvention einrichten.';
$string['namingConvetion_option1'] = 'Nur ganzer Name';
$string['namingConvetion_option2'] = 'Kurzer Name - Ganzer Name';
$string['namingConvetion_option3'] = 'Ganzer Name';
$string['namingConvetion_option4'] = 'Kurzer Name - Ganzer Name (Jahr)';
$string['namingConvetion_option5'] = 'Ganzer Name (Jahr)';
$string['nocatselected'] = 'Leider ist keine Kategorie für diesen Kurs ausgewählt';
$string['noneofthese'] = 'Keiner von diesen? Fortfahren um neuen Kurs zu erstellen';
$string['noPending'] = 'Leider steht nichts an!';
$string['optional_field'] = 'Optionales Feld';
$string['originator'] = 'Antragsteller';
$string['pluginname'] = 'Course Request Manager';
$string['plugindesc'] = 'Course Request Manager';
$string['previewform'] = 'Vorschau';
$string['quickapprove'] = 'Schnelle Bewilligung';
$string['quickapprove_desc'] = 'Schnelle Bewilligung in diesem Kurs durchführen?';
$string['recordsHaveBeenDeleted'] = 'Dokumente wurden gelöscht.';
$string['request_addModule'] = 'Modul hinzufügen';
$string['request_complete'] = 'Kursanfrage vollständig.';
$string['requestcontrol'] = 'Anfrageverwaltung';
$string['request_pleaseSelect'] = 'Bitte wählen Sie den Kursmodus';
$string['request_requestControl'] = 'Anfrageverwaltung dieser Aktivität';
$string['request_requestnewBlank'] = 'Neue leere Aktivität anfragen oder das Entfernen einer Aktivität.';
$string['requestReview_AlterRequest'] = 'Anfrage abändern';
$string['requestReview_ApproveRequest'] = 'Anfrage bestätigen';
$string['requestReview_CancelRequest'] = 'Anfrage abbrechen';
$string['requestReview_courseCode'] = 'Kursnummer';
$string['requestReview_courseName'] = 'Kursname';
$string['requestReview_creationDate'] = 'Erstellungsdatum';
$string['requestReview_intro1'] = 'Bitte überprüfen Sie die folgenden Informationen vor dem Absenden der Anfrage.';
$string['requestReview_intro2'] = 'Ihre Anfrage wird so schnell wie möglich bearbeitet.';
$string['requestReview_moduleCode'] = 'Kursnummer';
$string['requestReview_moduleName'] = 'Kursname';
$string['requestReview_OpenDetails'] = 'Offene Details';
$string['requestReview_originator'] = 'Antragssteller';
$string['requestReview_requestType'] = 'Anfrageart';
$string['requestReview_status'] = 'STATUS';
$string['requestReview_SubmitRequest'] = 'Anfrage abschicken';
$string['requestReview_Summary'] = 'Zusammenfassung der Anfrage';
$string['request_rule1'] = 'Bitte geben Sie in dieses Feld einen Wert ein.';
$string['request_rule2'] = 'Bitte wählen Sie einen Wert.';
$string['request_rule3'] = 'Bitte geben Sie einen Einschreibeschlüssel ein.';
$string['requesttype'] = 'Anfrageart';
$string['required_field'] = 'Benötigtes Feld';
$string['SaveAll'] = 'Alles Speichern';
$string['SaveChanges'] = 'Änderungen Speichern';
$string['SaveEMail'] = 'Neue E-Mail hinzufügen';
$string['searchAuthor'] = 'Autor';
$string['searchbuttontext'] = 'Suchen!';
$string['search_side_text'] = 'Suchen';
$string['selectedcategory'] = 'Kategorie';
$string['selfCatOff'] = 'Selbstkategorisierung aus';
$string['selfCatOn'] = 'Selbstkategorisierung an';
$string['sendrequestemail'] = 'Anfrageemail senden.';
$string['sendrequestforcontrol'] = 'Anfrage an Administrator schicken';
$string['snamingConvetion'] = 'Kurznamensformat';
$string['snamingConvetionInstruction'] = 'Bitte wählen Sie ein Kurznamensformat für neu erstellte Kurse';
$string['snamingConvetion_option1'] = 'Nur Kurznamen';
$string['snamingConvetion_option2'] = 'Kurzname - Modus';
$string['statsConfigInfo'] = 'Dieser Abschnitt zeigt Statistiken über die aktuelle Anzahl von Anträgen die gemacht wurden seit dem der Course Request Manager auf ihrem Server installiert wurde.';
$string['status'] = 'STATUS';
$string['step1text'] = 'Schritt 1: Kursanfragedetails';
$string['sureDeleteAll'] = 'Sind Sie sicher, dass Sie den gesamten Verlauf löschen wollen?';
$string['sureOnlyArch'] = 'Sind Sie sicher, dass Sie nur alle archivierten Einträge löschen wollen?';
$string['totalRequests'] = 'Gesamte Anzahl von Anfragen';
$string['update'] = 'Aktualisieren';
$string['view'] = 'Ansicht';
$string['viewmore'] = 'Mehr ansehen';
$string['viewsummary'] = 'Zusammenfassung ansehen';
$string['withselectedrequests'] = 'mit ausgewählten Anfragen';
$string['yesDeleteRecords'] = 'Ja, löschen!';



$string['cannotrequestcourse'] = 'Leider kann man einen Kurs nicht anfordern';
$string['cannotviewrecords'] = 'Leider kann man nicht Datensätze anzeigen';
$string['cannotapproverecord'] = 'Leider kann man nicht genehmigen Aufzeichnungen';
$string['cannoteditrequest'] = 'Es tut uns leid Sie können eine Aufzeichnung nicht bearbeiten';
$string['cannotcomment'] = 'Leider haben Sie nicht kommentieren kann';
$string['cannotdelete'] = 'Es tut uns leid Sie können eine Aufzeichnung nicht löschen';
$stirng['cannotdenyrecord'] = 'Es tut uns leid Sie können eine Aufzeichnung nicht leugnen';
$string['cannotviewconfig'] = 'Leider können Sie die Konfiguration nicht anzeigen';

