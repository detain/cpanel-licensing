---
name: cpanel-method
description: Adds a new public method to `src/Cpanel.php` following the validate→get pattern. Validates required args with `array_key_exists`, calls `validateIP()` or `validateID()`, then delegates to `$this->get('XMLfunctionName.cgi', $args)`. Use when user says 'add method', 'new API call', 'implement endpoint', or adds a cPanel CGI function. Do NOT use for modifying the private `get()` or `formatResult()` internals.
---
# cpanel-method

## Critical

- **Never throw exceptions** — validation failures call `error_log()` and `return;` (returns `null`)
- **Never modify** `get()`, `formatResult()`, `validateIP()`, or `validateID()` — these are private internals
- **Tabs only** for indentation (enforced by `.scrutinizer.yml`)
- IP params must go through `validateIP()`, license/group/package ID params through `validateID()`
- All public methods must return `null` on validation failure, or the raw result of `$this->get()`

## Instructions

1. **Identify the CGI endpoint.** Find the target in the known endpoint list in `src/Cpanel.php` or the manage2 API docs. The CGI filename becomes the first arg to `$this->get()`. Verify the endpoint exists before proceeding.

2. **Determine required vs optional params.** Required params get an `array_key_exists` guard. Optional params are passed through as-is. Check existing methods in `src/Cpanel.php` for the exact params a similar endpoint uses.

3. **Write the method** using this exact structure inside the `Detain\Cpanel\Cpanel` class:

```php
public function methodName($args) {
	if (!array_key_exists('requiredParam', $args)) {
		error_log('cpanelLicensing::methodName requires requiredParam');
		return;
	}
	if (!$this->validateIP($args['ip'])) {
		error_log('cpanelLicensing::methodName was passed an invalid ip');
		return;
	}
	return $this->get('XMLfunctionName.cgi', $args);
}
```

   - Use `validateIP()` for any `ip` parameter
   - Use `validateID()` for any `licenseid`, `packageid`, or `groupid` parameter (matches `/^(L|P|G)?\d*$/`)
   - Log format: `'cpanelLicensing::methodName requires paramName'` and `'cpanelLicensing::methodName was passed an invalid ip'`

4. **Add a bin/ script** mirroring `bin/add_new_license.php`:

```php
<?php
include '../src/Cpanel.php';
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);
$result = (array) $cpl->methodName(['ip' => '__IP__']);
if ($result['@attributes']['status'] > 0) {
	print 'success: ' . $result['@attributes']['licenseid'] . PHP_EOL;
} else {
	print 'failed: ' . $result['@attributes']['reason'] . PHP_EOL;
}
```

   Use `__IP__`, `__GROUPNAME__`, `__PACKAGENAME__` as placeholder values. Check `$result['@attributes']['status'] > 0` for success.

5. **Add a test** in `tests/CpanelTest.php` following the existing pattern:

```php
public function testMethodName() {
	$result = (array) $this->cpl->methodName(['ip' => $this->ip]);
	$this->assertArrayHasKey('@attributes', $result);
}
```

   Credentials come from `getenv('CPANEL_LICENSING_USERNAME')` / `getenv('CPANEL_LICENSING_PASSWORD')` — already wired in the test bootstrap.

6. **Run tests** to verify no regressions:
```bash
vendor/bin/phpunit tests/ -v
```

## Examples

**User says:** "Add a method to register an auth token"

**Actions taken:**
- Endpoint: `XMLregisterAuth.cgi`
- Required params: `ip`, `token`
- `ip` needs `validateIP()`

**Result in `src/Cpanel.php`:**
```php
public function registerAuth($args) {
	if (!array_key_exists('ip', $args)) {
		error_log('cpanelLicensing::registerAuth requires ip');
		return;
	}
	if (!$this->validateIP($args['ip'])) {
		error_log('cpanelLicensing::registerAuth was passed an invalid ip');
		return;
	}
	if (!array_key_exists('token', $args)) {
		error_log('cpanelLicensing::registerAuth requires token');
		return;
	}
	return $this->get('XMLregisterAuth.cgi', $args);
}
```

## Common Issues

- **Method returns `null` unexpectedly:** A required param guard fired. Check `error_log` output — the message will name the missing param.
- **`$result['@attributes']` undefined:** The `(array)` cast was skipped. Always cast: `$result = (array) $cpl->methodName($args)`.
- **`fetchLicenseId()` result causes type error:** `$lisc['licenseid']` may be an array when multiple licenses match. Guard with `if (is_array($lisc['licenseid'])) { $id = $lisc['licenseid'][0]; }`.
- **Indentation lint failure in CI:** Editor inserted spaces. Convert with `unexpand --first-only` or set editor to tabs. `.scrutinizer.yml` enforces tab indentation.
- **Test fails with credentials error:** Set env vars before running: `CPANEL_LICENSING_USERNAME=user CPANEL_LICENSING_PASSWORD=pass vendor/bin/phpunit ...`
