---
name: phpunit-test
description: Implements incomplete test stubs in tests/CpanelTest.php by replacing markTestIncomplete() with real assertions. Use when user says 'implement test', 'write test for', 'complete test stub', or references a src/Cpanel.php method lacking coverage. Do NOT use for adding new test classes or new test methods that don't already exist as stubs.
---
# phpunit-test

## Critical

- **Never remove the `@covers` docblock** — it maps to the method under test; keep it verbatim.
- **`$this->object` is already instantiated** in `setUp()` as `new Cpanel(getenv('CPANEL_LICENSING_USERNAME'), getenv('CPANEL_LICENSING_PASSWORD'))` — do not re-instantiate.
- All public methods return **`null` on validation failure** (not exceptions). Test the `null` return, not an exception.
- Use **tabs** for indentation (per `.scrutinizer.yml`), not spaces.
- Run tests with: `vendor/bin/phpunit tests/ -v`

## Instructions

1. **Read the stub** in `tests/CpanelTest.php`. Locate the target `testMethodName()` containing `$this->markTestIncomplete(...)`. Note the `@covers` tag to confirm which method is under test.

2. **Read `src/Cpanel.php`** and study the target method. Identify:
   - Required array keys (checked via `array_key_exists`)
   - Which validator is called: `validateIP($args['ip'])` (matches `\d*.\d*.\d*.\d*`) or `validateID($args['liscid'])` (matches `(L|P|G)?\d*`)
   - The return type (array from `get()`, or `null`)

3. **Write validation-failure assertions first** (no network needed). For each missing required key or invalid value, call the method and assert the return is `null`:
   ```php
   $this->assertNull($this->object->methodName([]));
   $this->assertNull($this->object->methodName(['ip' => 'not-an-ip']));
   ```

4. **Write a setFormat test** (if implementing `testSet_format`) — no network call needed:
   ```php
   $this->object->setFormat('json');
   $this->assertEquals('json', $this->object->format);
   $this->object->setFormat('invalid');
   $this->assertEquals('json', $this->object->format, 'Invalid format must not change the value');
   $this->object->setFormat('simplexml'); // restore default
   ```

5. **For methods that make live API calls** (require credentials), guard with `markTestSkipped` if env vars are absent:
   ```php
   if (!getenv('CPANEL_LICENSING_USERNAME')) {
       $this->markTestSkipped('CPANEL_LICENSING_USERNAME not set');
   }
   $result = (array) $this->object->fetchGroups();
   $this->assertIsArray($result);
   ```

6. **Remove** the two lines:
   ```php
   $this->markTestIncomplete(
       'This test has not been implemented yet.'
   );
   ```
   Replace with the assertions from steps 3–5.

7. **Verify**: `vendor/bin/phpunit tests/ -v` — the test must no longer show `I` (incomplete); it must show `OK` or `S` (skipped).

## Examples

**User says:** "implement testExtendOnetimeUpdates"

**Method signature in `src/Cpanel.php`:**
```php
public function extendOnetimeUpdates($args) {
    if (!array_key_exists('ip', $args)) { error_log(...); return; }
    if (!$this->validateIP($args['ip'])) { error_log(...); return; }
    return $this->get('XMLonetimeext.cgi', $args);
}
```

**Result in `tests/CpanelTest.php`:**
```php
/**
 * @covers Detain\Cpanel\Cpanel::extendOnetimeUpdates
 */
public function testExtendOnetimeUpdates()
{
	$this->assertNull($this->object->extendOnetimeUpdates([]));
	$this->assertNull($this->object->extendOnetimeUpdates(['ip' => 'invalid']));
	if (!getenv('CPANEL_LICENSING_USERNAME')) {
		$this->markTestSkipped('CPANEL_LICENSING_USERNAME not set');
	}
	$result = (array) $this->object->extendOnetimeUpdates(['ip' => '1.2.3.4']);
	$this->assertIsArray($result);
}
```

## Common Issues

- **`markTestIncomplete` still present after edit:** You replaced only the string argument, not the full two-line call. Remove both `$this->markTestIncomplete(` and `'This test has not been implemented yet.'` and the closing `);`.
- **`assertIsArray` undefined:** Upgrade to PHPUnit 7.5+ or use `$this->assertTrue(is_array($result))` as fallback.
- **Test fails with `null` for a valid-looking IP like `'1.2.3.4'`:** `validateIP` uses `preg_match("/^\d*.\d*.\d*.\d*$/")` — dots are unescaped wildcards, but any IPv4 string with three dots passes. Ensure no missing required keys in `$args`.
- **`Cannot find class StatisticClient`:** Normal — the class_exists guard in `src/Cpanel.php` makes this optional. Tests pass without it.
- **Credentials error in live test:** Set env vars before running: `CPANEL_LICENSING_USERNAME=user CPANEL_LICENSING_PASSWORD=pass vendor/bin/phpunit ...`
