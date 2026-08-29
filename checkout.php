<?php
// Design export is intentionally rendered verbatim; the bridge only adds site behaviour.
echo str_replace('</body>', '<script src="assets/design-flow-bridge.js"></script></body>', file_get_contents(__DIR__ . '/design/export-20260829/export/IMA PRIME/IMA PRIME - Dat hang.html'));
?>
