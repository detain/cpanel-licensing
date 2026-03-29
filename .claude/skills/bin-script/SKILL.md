---
name: bin-script
description: Creates a new CLI script in `bin/` that instantiates `\Detain\Cpanel\Cpanel` with `argv[1]`/`argv[2]` credentials, calls a method, and prints results. Use when user says 'add script', 'create CLI tool', 'new bin file', or needs a command-line wrapper for a Cpanel API method. Do NOT use for library code in `src/` or test code in `tests/`.
---
# bin-script

## Critical

- Always use `include '../src/Cpanel.php';` — never use the Composer autoloader path in `bin/` scripts
- Credentials are **always** `$_SERVER['argv'][1]` (user) and `$_SERVER['argv'][2]` (pass) — never hardcode them
- Use tabs for indentation (per `.scrutinizer.yml`)
- Methods return `null` on validation failure — check the response before accessing `['@attributes']`
- `fetchLicenseId()` result `$lisc['licenseid']` may be array or scalar — always guard with `is_array()`

## Instructions

1. **Create the script file** in the `bin/` directory using snake_case naming.
   Verify the method you need exists in `src/Cpanel.php` before proceeding.

2. **Add the boilerplate header** — always exactly this structure:
   ```php
   <?php

   include '../src/Cpanel.php';

   $cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);
   ```

3. **Capture any additional CLI args** starting at `argv[3]`:
   ```php
   $ipAddress = $_SERVER['argv'][3];
   ```
   Use placeholder strings (`'__IP__'`, `'__GROUPNAME__'`, `'__PACKAGENAME__'`, `'__SOURCEIP__'`, `'__DESTINATIONIP__'`) when the value is a constant the caller will edit in-place.

4. **Set output format** if you need JSON (list results, raw lookups):
   ```php
   $cpl->format = 'json';
   ```
   Default format (`simplexml`) returns an object — cast to array with `(array)`. JSON format requires `json_decode(..., true)`.

5. **Call the method and decode the result** using the format-appropriate pattern:

   *simplexml (default) — status/action methods:*
   ```php
   $result = (array) $cpl->someMethod(['param' => $value]);
   if ($result['@attributes']['status'] > 0) {
       print 'success: ' . $result['@attributes']['relevantField'] . PHP_EOL;
   } else {
       print 'failed: ' . $result['@attributes']['reason'] . PHP_EOL;
   }
   ```

   *JSON — list/lookup methods:*
   ```php
   $result = json_decode($cpl->someMethod(['param' => $value]), true);
   echo json_encode($result, JSON_PRETTY_PRINT);
   ```

   *fetchLicenseId — always guard the returned id:*
   ```php
   $lisc = (array) $cpl->fetchLicenseId(['ip' => $ipAddress]);
   $id = $lisc['licenseid'];
   $id = is_array($id) ? $id[0] : $id;
   if ($id) {
       print "The license id for $ipAddress is $id\n";
   } else {
       print "No valid license exists for $ipAddress\n";
   }
   ```

6. **Verify manually** after writing:
   ```bash
   php bin/your_script.php USERNAME PASSWORD [extra_args]
   ```

## Examples

**User says:** "Add a script to reactivate a license by IP"

**Actions:**
- Confirm `reActivateLicense()` exists in `src/Cpanel.php`
- Create the script in `bin/`:

```php
<?php

include '../src/Cpanel.php';

$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);

$ipAddress = $_SERVER['argv'][3];
$lisc = (array) $cpl->fetchLicenseId(['ip' => $ipAddress]);
$id = $lisc['licenseid'];
$id = is_array($id) ? $id[0] : $id;

if ($id > 0) {
	$result = (array) $cpl->reActivateLicense(['liscid' => $id]);
	if ($result['@attributes']['status'] > 0) {
		print 'reactivated license id: ' . $result['@attributes']['licenseid'] . PHP_EOL;
	} else {
		print 'reactivation failed: ' . $result['@attributes']['reason'] . PHP_EOL;
	}
} else {
	print "No valid license exists for $ipAddress\n";
}
```

**Result:** Runnable via `php bin/your_script.php user pass 1.2.3.4`

## Common Issues

- **`include '../src/Cpanel.php'` fails / class not found:** Script must be run from `bin/` or via `php bin/script_name.php` from the repo root. The path is relative to the script file, not the CWD — if running from another directory, adjust the include path or use the Composer autoloader instead.
- **Response is `null` or empty:** The method returned early due to validation failure. Check `error_log` output — `validateIP()` requires dotted-decimal format (`/^\d*.\d*.\d*.\d*$/`), `validateID()` requires optional `L`/`P`/`G` prefix followed by digits.
- **`$result['@attributes']` undefined:** Method returned a simplexml object — you forgot `(array)` cast, or the API returned an error XML without attributes. Add `print_r($result)` before the attribute access to inspect the raw structure.
- **`json_decode` returns `null`:** Format was not set to `'json'` before the call (`$cpl->format = 'json';`), so the response is XML, not JSON.
- **`$lisc['licenseid']` causes array-to-string error:** Multiple licenses matched the IP. Always use the `is_array($id) ? $id[0] : $id` guard (Step 5, fetchLicenseId pattern).
