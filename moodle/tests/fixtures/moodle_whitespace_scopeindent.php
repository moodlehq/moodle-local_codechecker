<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

if (1=1) {
    $somevar = true;
  $anothervar = true; // Bad aligned PHP.
      $anothervar = true; // Bad aligned PHP.
    // Next lines (PHP close, inline HTML and PHP start should be skipped.
?>
<div>
    <p>
        <span>some page content</span>
    </p>
</div>
<?php
    // Back to work, incorrect indenting should be detected again.
    $somevar = true;
  $anothervar = true; // Bad aligned PHP.
      $anothervar = true; // Bad aligned PHP.
}
