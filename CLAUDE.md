# cpanel-licensing

PHP library wrapping the cPanel manage2 licensing API. Single class `Detain\Cpanel\Cpanel` in `src/Cpanel.php`. CLI scripts in `bin/`. Tests in `tests/`.

## Commands

```bash
composer install                          # install deps including phpunit
vendor/bin/phpunit tests/ -v   # run all tests
phpdbg -qrr vendor/bin/phpunit tests/ -v --coverage-clover coverage.xml --whitelist src/   # with coverage
```

## Architecture

**Main class**: `src/Cpanel.php` · namespace `Detain\Cpanel\` · PSR-4 autoload
**CLI scripts**: `bin/` — each takes `argv[1]` (user) and `argv[2]` (pass) as credentials
**Tests**: `tests/CpanelTest.php` extends `PHPUnit\Framework\TestCase` · credentials from `getenv('CPANEL_LICENSING_USERNAME')` / `getenv('CPANEL_LICENSING_PASSWORD')`
**IDE config**: `.idea/` — PhpStorm project settings: `inspectionProfiles/` (code quality rules), `cpanel-licensing.iml` (module definition), `deployment.xml` (server deployment config)

**API endpoint**: all calls hit `https://manage2.cpanel.net/{XMLfunction}.cgi` via cURL

**Method pattern** in `src/Cpanel.php`:
```php
public function methodName($args) {
    if (!array_key_exists('requiredParam', $args)) {
        error_log('cpanelLicensing::methodName requires requiredParam');
        return;
    }
    if (!$this->validateIP($args['ip'])) {   // or validateID()
        error_log('cpanelLicensing::methodName was passed an invalid ip');
        return;
    }
    return $this->get('XMLfunctionName.cgi', $args);
}
```

**Known CGI endpoints**: `XMLlicenseAdd.cgi` · `XMLlicenseExpire.cgi` · `XMLlicenseReActivate.cgi` · `XMLtransfer.cgi` · `XMLtransferRequest.cgi` · `XMLlookup.cgi` · `XMLRawlookup.cgi` · `XMLsecverify.cgi` · `XMLpackageInfo.cgi` · `XMLlicenseInfo.cgi` · `XMLgroupInfo.cgi` · `XMLaddPickupPass.cgi` · `XMLregisterAuth.cgi` · `XMLonetimeext.cgi`

**Validation helpers** (private): `validateID($id)` — matches `/^(L|P|G)?\d*$/` · `validateIP($ip)` — matches `/^\d*\.\d*\.\d*\.\d*$/`

**Output formats**: `simplexml` (default, returns array via `formatResult()`) · `xml` · `json` · `yaml`

## bin/ Script Pattern

```php
<?php
include '../src/Cpanel.php';  // or autoloader in integrated contexts
$cpl = new \Detain\Cpanel\Cpanel($_SERVER['argv'][1], $_SERVER['argv'][2]);
// use __IP__, __GROUPNAME__, __PACKAGENAME__ as placeholder values
$result = (array) $cpl->someMethod(['param' => $value]);
if ($result['@attributes']['status'] > 0) {
    print 'success: ' . $result['@attributes']['relevantField'] . PHP_EOL;
} else {
    print 'failed: ' . $result['@attributes']['reason'] . PHP_EOL;
}
```

## Conventions

- Errors via `error_log()` not exceptions — all public methods return `null` on validation failure
- Response attributes accessed as `$result['@attributes']['fieldname']` after `(array)` cast
- `fetchLicenseId()` result: `$lisc['licenseid']` may be array or scalar — check with `is_array()`
- Tabs for indentation (per `.scrutinizer.yml`)
- `StatisticClient` integration is optional — guarded by `class_exists(\StatisticClient::class, false)`
- No PDO, no framework — pure PHP with `ext-soap` and cURL
- Commit messages: lowercase, descriptive

## Testing

- `tests/CpanelTest.php` — most methods stubbed with `markTestIncomplete()`
- Credentials injected via env: `CPANEL_LICENSING_USERNAME` / `CPANEL_LICENSING_PASSWORD`
- `setUp()` instantiates `$this->object = new Cpanel(getenv(...), getenv(...))`
- Live API calls required for integration tests — no mocking of cURL

<!-- caliber:managed:pre-commit -->
## Before Committing

Run `caliber refresh` before creating git commits to keep docs in sync with code changes.
After it completes, stage any modified doc files before committing:

```bash
caliber refresh && git add CLAUDE.md .claude/ .cursor/ .github/copilot-instructions.md AGENTS.md CALIBER_LEARNINGS.md 2>/dev/null
```
<!-- /caliber:managed:pre-commit -->

<!-- caliber:managed:learnings -->
## Session Learnings

Read `CALIBER_LEARNINGS.md` for patterns and anti-patterns learned from previous sessions.
These are auto-extracted from real tool usage — treat them as project-specific rules.
<!-- /caliber:managed:learnings -->
