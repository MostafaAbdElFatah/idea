# Testing

The suite is built on Pest 5 and treats the tests as a product: every test is isolated, deterministic, grouped, and lives in the folder for its layer.

## Prerequisites

| Tool | Needed for | Status on this machine |
|---|---|---|
| PHP 8.5 with `pdo_sqlite` | all suites | installed |
| Xdebug (`XDEBUG_MODE=coverage`) or PCOV | coverage, mutation | Xdebug installed (PCOV preferred if you add it) |
| Playwright browsers (`npx playwright install`) | browser suite | installed |
| [k6](https://k6.io) | stress suite | **not installed** (`brew install k6`) |

## Running

| Command | What it runs |
|---|---|
| `composer test` | unit + feature + architecture, parallel (default) |
| `composer test:unit` / `test:feature` / `test:arch` | one suite |
| `composer test:browser` | Playwright browser tests |
| `composer test:random` | default run in random order |
| `composer test:coverage` | line coverage, HTML in `reports/coverage`, Clover in `reports/clover.xml`, min 90 % |
| `composer test:types` | type coverage, min 100 % |
| `composer test:mutate` | mutation testing, min 80 % (`test:mutate:models` for the models group at 90 %) |
| `composer test:stress` | k6 stress tests; needs `STRESS_BASE_URL` pointing at a dedicated seeded environment |
| `composer test:all` | arch, coverage, types, browser |

Narrow a run with a path or `--filter`: `vendor/bin/pest tests/Feature/Models/IdeaTest.php --filter="round-trips"`.

## Layout

```
tests/
  Pest.php            binds TestCase; RefreshDatabase for Feature + Browser; loads Helpers/
  TestCase.php        enables strict model checks (no lazy loading, no missing attributes)
  Datasets/           shared datasets (idea statuses, policy abilities, invalid emails)
  Helpers/            tiny pure helpers, e.g. loginAs()
  Unit/               no DB, no HTTP: enums, model config, request rules, policy logic
  Feature/            boots the app with RefreshDatabase: models, database, policies, requests, controllers, console
  Browser/            Playwright smoke tests (desktop, mobile, dark mode)
  Architecture/       arch() rules, one file per concern
  Stress/             k6 stressless tests, excluded from the default run
```

## Groups

Every test carries a layer group and a domain group.

- Layer: `unit`, `feature`, `browser`, `architecture`, `stress`
- Domain: `models`, `database`, `components`, `console`, `controllers`, `policies`, `requests`, `auth`, `middleware`, `jobs`

Run a group with `vendor/bin/pest --group=models`.

## Rules

- Tests create their own data through factories. Never rely on seeders or other tests.
- Fake every external effect (`Http`, `Mail`, `Notification`, `Queue`, `Storage`, `Bus`).
- Freeze time with `$this->travelTo()` when time matters. No `sleep()`, no unseeded randomness in assertions.
- Each file that exercises a class declares `covers(Foo::class)` so mutation runs stay targeted.
- `declare(strict_types=1);` in every test file. Names read as sentences: `it('rejects a duplicate email')`.

## Adding tests for a new…

- **Model**: factory with explicit states in `database/factories`; config tests in `tests/Unit/Models`; relationships, casts and persistence in `tests/Feature/Models`; add the factory to `tests/Feature/Database/FactoriesTest.php` and constraints to `ConstraintsTest.php`. Groups `models` / `database`.
- **Controller**: `tests/Feature/Controllers/{Name}ControllerTest.php` with happy path, guest, 403, validation failure, and 404 per endpoint. Group `controllers`. Add a browser journey under `tests/Browser/{Feature}/` using `dusk="…"` selectors.
- **Policy**: unit matrix in `tests/Unit/Policies` using `Model::factory()->make()`; gate integration in `tests/Feature/Policies`. Use the `model abilities` and `class abilities` datasets. Group `policies`.
- **Form request**: rules and `authorize()` in `tests/Unit/Requests`; HTTP validation in `tests/Feature/Requests`. Group `requests`.
- **Command**: `tests/Feature/Console/{Name}CommandTest.php` using `$this->artisan()`; assert exit codes and side effects. Group `console`.
- **Component**: `tests/Feature/Components` rendering via `$this->blade()` or `$this->component()`. Group `components`.
