# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Symfony 7.2 (PHP >=8.4) skeleton for bootstrapping microservices. `src/Controller`, `src/Entity`,
and `src/Repository` are intentionally empty (only `.gitignore` placeholders) — this repo is a starting
point cloned per-service, not an app with existing business logic. Namespace root is `SystemeioTestTask\` (mapped to
`src/`, PSR-4), tests are `SystemeioTestTask\Tests\` (mapped to `tests/`).

Most cross-cutting framework behavior (JSON request decoding, exception-to-JSON response normalization,
argument resolvers, storage abstraction, health checks, ClickHouse client, scheduling) comes from the
private `g123udini/component-bundle` package (vendor `DDH\ComponentBundle`), configured in
`config/packages/ddh_component.yaml` and installed from `github.com/g123udini/component-bundle`. Read
`vendor/g123udini/component-bundle/src/` when you need to know how requests/responses/exceptions are
actually handled — don't assume vanilla Symfony behavior for these concerns.

Other private g123udini packages pulled via VCS repositories in `composer.json`: `code-style-rules`
(php-cs-fixer), `phpstan-rules`, `psalm-rules` (all under the `g123udini/*` composer package names).

## Development environment

The app runs in Docker; the `Makefile` targets wrap `docker compose exec` calls into the `app` container.
There is no host-side PHP toolchain expected — always go through `make` / `docker compose exec app ...`.

```shell
make build          # build containers (copies phpunit.xml.dist -> phpunit.xml if missing)
make up              # start containers (app, postgres, nginx via docker-compose.yml + docker-compose.$APP_ENV.yml)
make stop            # stop containers
make in-app          # shell into the app container
make in-postgres     # shell into the postgres container
```

`APP_ENV` (from `.env`) selects which compose overlay is merged in (`docker-compose.dev.yml` /
`docker-compose.prod.yml`); `docker-compose.override.yml`, if present, is merged in last for local
overrides. Building requires a GitLab/GitHub personal access token for the private repos, set as
`GIT_TOKEN` in `.env.local`.

## Common commands

All of these are composer scripts (`composer.json`), reachable via `make` from the host or directly
inside the `app` container:

```shell
make phpunit         # composer phpunit    -> bin/phpunit
make phpstan         # composer phpstan    -> cache:warmup, clear phpstan result cache, phpstan analyse
make psalm           # composer psalm      -> cache:warmup, vendor/bin/psalm --no-cache
make phpcscheck      # composer phpcscheck -> php-cs-fixer fix --dry-run
make phpcsfix        # composer phpcsfix   -> php-cs-fixer fix (auto-fixes style)
make lint            # composer lint       -> console lint:container
make doctrine        # composer doctrine   -> console doctrine:schema:validate --skip-sync
make check           # runs all of the above in sequence (cache:warmup, phpcscheck, phpstan, psalm, lint, phpunit, doctrine)
```

Run `make check` before considering a change done — it's the full pre-commit gate this repo expects.

To run a single test, exec into the container and invoke phpunit/bin/phpunit directly, e.g.:
```shell
docker compose exec -T app bin/phpunit tests/Path/To/SomeTest.php --filter=testMethodName
```

Static analysis config points at the compiled dev container and console loader, so `phpstan`/`psalm`
need a warm dev container dump (`console cache:warmup`) — the `phpstan`/`psalm` composer scripts already
do this, but if running the underlying binaries directly, warm the cache first.

## Test environment specifics

- `phpunit.xml` env vars (`DATABASE_URL`, `APP_SECRET`, etc.) take precedence via `force="true"`, but note
  the file also has a second, non-forced `APP_ENV=dev` / `DATABASE_URL=postgresql://...` block further
  down for symfony/doctrine recipe placeholders — the forced values above are what actually apply.
- `tests/bootstrap-orm.php` boots the real `SystemeioTestTask\Kernel` and returns the Doctrine entity manager — used by
  phpstan/psalm's Doctrine ORM integration (`objectManagerLoader`), not by phpunit tests themselves.
- `tests/ConsoleApplication.php` boots the kernel and returns a `Symfony\Bundle\FrameworkBundle\Console\Application`
  — used by phpstan's `consoleApplicationLoader` for analysing console commands.

## Architecture notes

- **Bundles** (`config/bundles.php`): framework-bundle, doctrine-bundle + migrations, twig, monolog,
  sentry-symfony, nelmio/api-doc-bundle, and `DDH\ComponentBundle`. `web_profiler`/`debug`/`maker` are
  dev+test only.
- **Service autowiring** (`config/services.yaml`): all classes under `src/` are auto-registered as
  services except `DependencyInjection`, `DTO`, `Entity`, `Exception`, `Functions`, `Request` subdirs and
  `Kernel.php`. Any `*Controller.php` anywhere under `src/` is auto-tagged as a controller
  (`controller.service_arguments`) — controllers don't need to live in `src/Controller/` specifically.
- **Storage**: `ddh_component.storage` in `config/packages/ddh_component.yaml` defines named storages
  (`main`, `test`) rooted at `SystemeioTestTask_STORAGE_DIR` (`var/storage` by default, from `.env`).
- **API docs**: nelmio/api-doc-bundle documents everything under `/api` except `/api/doc` itself
  (see `config/packages/nelmio_api_doc.yaml`).
- **Doctrine**: PostgreSQL via `DATABASE_URL`, `underscore_number_aware` naming strategy, auto-mapping on.
  Entity/mapping overrides for non-`src/`-rooted namespaces (e.g. mapping in the component bundle) go in
  the commented-out `mappings:` block in `config/packages/doctrine.yaml`.
- **Logging/errors**: monolog is channel-scoped per environment (dev: stream to file; test/prod:
  `fingers_crossed` buffering on error, excluding 404/405); Sentry is wired as a monolog handler at
  `ERROR` level via `config/packages/sentry.yaml`, DSN from `SENTRY_DSN`.
- **Cron**: `config/crontab.conf` — jobs reference a `$CONSOLE` variable populated by
  `php bin/console cron:setup` (from the component bundle), not hardcoded paths.
