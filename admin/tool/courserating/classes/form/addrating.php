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

namespace tool_courserating\form;

use moodle_exception;
use moodle_url;
use tool_courserating\api;
use tool_courserating\constants;
use tool_courserating\helper;
use tool_courserating\local\models\summary;
use tool_courserating\permission;

/**
 * Form to add or change a rating
 *
 * @package     tool_courserating
 * @copyright   2022 Marina Glancy <marina.glancy@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class addrating extends \core_form\dynamic_form {

    /**
     * Current course id
     *
     * @return int
     */
    protected function get_course_id(): int {
        $componentid = $this->optional_param('componentid', 0, PARAM_INT);
        if (!$componentid) {
            throw new moodle_exception('missingparam', '', '', 'componentid');
        }
        return $componentid;
    }
      protected function get_course_name(): string {
        $componentname = $this->optional_param('componentname', '', PARAM_RAW);
        if (!$componentname) {
            throw new moodle_exception('missingparam', '', '', 'componentname');
        }
        return $componentname;
    }
    /**
     * Form definition
     */
    protected function definition() {
        global $OUTPUT;
        $mform = $this->_form;
        $mform->addElement('hidden', 'componentid', $this->get_course_id());
        $mform->setType('componentid', PARAM_INT);

        $componentname = $this->optional_param('componentname', "", PARAM_RAW);
        $mform->addElement('hidden', 'componentname', $componentname);
        $mform->setType('componentname', PARAM_RAW); 

        $summary = summary::get_for_course($this->get_course_id(),$this->get_course_name());
        if ($summary->get('cntall')) {
            $componentid = $this->get_course_id();
            $str = get_string('viewallreviews', 'tool_courserating');
            $mform->addElement('html', <<<EOF
<p class="mdl-align"><a href="#" data-action="tool_courserating-viewratings" data-componentid="$componentid" data-componentname="$componentname">$str</a></p>
EOF
            );
        }

        $radioarray = [];
        foreach ([1, 2, 3, 4, 5] as $r) {
            $label = $OUTPUT->pix_icon('star', $r, 'tool_courserating', ['class' => 'star-on tool_courserating-stars']);
            $label .= $OUTPUT->pix_icon('star-o', $r, 'tool_courserating', ['class' => 'star-off tool_courserating-stars']);
            $label = \html_writer::span($label);
            /** @var \MoodleQuickForm_radio $el */
            $el = $mform->createElement('radio', 'rating', '', $label, $r);
            $el->setAttributes($el->getAttributes() + ['class' => ' stars-' . $r]);
            $radioarray[] = $el;
        }
        $el = $mform->addGroup($radioarray, 'ratinggroup', get_string('rating', 'tool_courserating'), [' ', ' '], false);
        $el->setAttributes($el->getAttributes() + ['class' => 'tool_courserating-form-stars-group']);

        if (helper::get_setting(constants::SETTING_USEHTML)) {
            $options = helper::review_editor_options($this->get_context_for_dynamic_submission());
            $mform->addElement('editor', 'review_editor', get_string('review', 'tool_courserating'),
                ['rows' => 4], $options);
        } else {
            $mform->addElement('textarea', 'review', get_string('review', 'tool_courserating'),
                ['rows' => 4]);
            $mform->setType('review', PARAM_TEXT);
        }
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = [];
        if (empty($data['rating'])) {
            $errors['ratinggroup'] = get_string('required');
        }
        return $errors;
    }

    /**
     * Display the form
     *
     * @return void
     */
    public function display() {
        parent::display();
        global $PAGE;
        $PAGE->requires->js_call_amd('tool_courserating/rating', 'setupAddRatingForm',
        [$this->_form->getElement('ratinggroup')->getAttribute('id')]);
    }

    /**
     * Current context
     *
     * @return \context
     */
    protected function get_context_for_dynamic_submission(): \context {
        switch ($this->get_course_name()) {
            case "local_courses":
                $categorycontext = \context_course::instance($this->get_course_id());;
            break;    
             case "local_program":
                $categorycontext = (new \local_program\lib\accesslib())::get_module_context($this->get_course_id());
            break;
            case "local_learningplan":
                $categorycontext = (new \local_learningplan\lib\accesslib())::get_module_context($this->get_course_id());
            break;
              case "local_classroom":
                $categorycontext = (new \local_classroom\lib\accesslib())::get_module_context($this->get_course_id());
            break;
 
        }
        return $categorycontext;
    }

    /**
     * Check access and throw exception if not allowed
     *
     * @return void
     * @throws moodle_exception
     */
    protected function check_access_for_dynamic_submission(): void {
        permission::require_can_add_rating($this->get_course_id());
    }

    /**
     * Process form submission
     */
    public function process_dynamic_submission() {
        $data = $this->get_data();
        api::set_rating($this->get_course_id(), $data);
    }

    /**
     * Load in existing data as form defaults
     */
    public function set_data_for_dynamic_submission(): void {
        $this->set_data(api::prepare_rating_for_form($this->get_course_id(),$this->get_course_name()));
    }

    /**
     * Fake URL for atto auto-save
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/course/view.php', ['id' => $this->get_course_id(), 'addrating' => 1]);
    }
}
