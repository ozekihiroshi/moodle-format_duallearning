<?php
// Test-only clock for core_courseformat's second-resolution session cache key.
// Loaded explicitly by the browser-test job, never by the installed plugin.
namespace core_courseformat;

/**
 * Reproduce multiple role transitions occurring within the same clock second.
 *
 * @return int A fixed cache clock; does not freeze Moodle's other clocks.
 */
function time(): int {
    return 1790719200;
}
