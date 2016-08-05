<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class bare_class {
    function foo() {
        // Should be OK in this class.
        require_login();
        $PAGE->set_context(context_system::instance());
    }
}

class ws_class extends external_api {
    function foo() {
        // Should cause a warning.
        require_login();
        $PAGE->set_context(context_system::instance());
    }
}
