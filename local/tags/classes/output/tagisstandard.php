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
 * Contains class local_tags\output\tagisstandard
 *
 * @package   local_tags
 * @copyright 2019 eAbyas <eAbyas.in>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tags\output;

use context_system;
use local_tags_tag;

/**
 * Class to display/toggle tag isstandard attribute
 *
 * @package   local_tags
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tagisstandard extends \core\output\inplace_editable {

    /**
     * Constructor.
     *
     * @param \stdClass|local_tags_tag $tag
     */
    public function __construct($tag) {
        $editable = has_capability('moodle/tag:manage', (new \local_tags\lib\accesslib())::get_module_context());
        $value = (int)(bool)$tag->isstandard;

        parent::__construct('local_tags', 'tagisstandard', $tag->id, $editable, $value, $value);
        $this->set_type_toggle();
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output) {
        if ($this->value) {
            $this->edithint = get_string('settypedefault', 'local_tags');
            $this->displayvalue = $output->pix_icon('i/checked', $this->edithint);
        } else {
            $this->edithint = get_string('settypestandard', 'local_tags');
            $this->displayvalue = $output->pix_icon('i/unchecked', $this->edithint);
        }

        return parent::export_for_template($output);
    }

    /**
     * Updates the value in database and returns itself, called from inplace_editable callback
     *
     * @param int $itemid
     * @param mixed $newvalue
     * @return \self
     */
    public static function update($itemid, $newvalue) {
        require_capability('moodle/tag:manage', (new \local_tags\lib\accesslib())::get_module_context());
        $tag = local_tags_tag::get($itemid, '*', MUST_EXIST);
        $newvalue = (int)clean_param($newvalue, PARAM_BOOL);
        $tag->update(array('isstandard' => $newvalue));
        return new self($tag);
    }
}
