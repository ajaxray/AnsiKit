# Repository Guidelines

## Project Structure & Module Organization
- Source: `src/` (PSR-4 `Ajaxray\AnsiKit\`)
  - Core: `src/AnsiTerminal.php`
  - Components: `src/Components/` (e.g., `Table.php`, `Banner.php`, `Progressbar.php`, `Choice.php`, `Spinner.php`)
  - Support: `src/Support/` (helpers like `Input.php`, `Keypress.php`, `Str.php`)
  - Writers: `src/Writers/` (`StdoutWriter.php`, `MemoryWriter.php`)
  - Contracts: `src/Contracts/` (interfaces, if any)
- Tests: `tests/` mirrors structure (e.g., `tests/Components/TableTest.php`)
- Examples: `examples/` runnable demos; Assets in `media/`; CI in `.github/`.

## Build, Test, and Development Commands
- Install: `composer install`
- Run tests: `composer test` (PHPUnit 11, colors enabled)
- Run examples: `php examples/showcase.php`, `php examples/progress.php`, `php examples/input.php`, `php examples/choice.php`
- Local check: `php -l src/AnsiTerminal.php` (syntax), or run a linter you prefer.
Requires PHP >= 8.2 (per `composer.json`).

## Coding Style & Naming Conventions
- Standard: PSR-12, `declare(strict_types=1);` at file top.
- Naming: Classes PascalCase; methods/properties camelCase; constants UPPER_SNAKE_CASE.
- Files: One class per file; filename matches class name.
- Imports: Use explicit namespaces; keep chainable APIs readable (one call per line when long).
- Formatting: Use your PSR-12 tool of choice (e.g., PHP-CS-Fixer or PHP_CodeSniffer). No tool is enforced in scripts.

## Testing Guidelines
- Framework: PHPUnit (see `phpunit.xml`).
- Location: Put tests under `tests/` mirroring `src/` structure.
- Naming: `*Test.php`, class `XxxTest` with clear, single-purpose methods.
- Writers: Prefer `MemoryWriter` for output assertions.
- Run: `composer test`. Add cases when changing rendering, color sequences, or cursor control.

## Commit & Pull Request Guidelines
- Commits: Short, imperative subject; scope when helpful. Examples: `feat: add Choice component styling`, `fix: reset cursor after clear`.
- Branches: `feat/...`, `fix/...`, `docs/...` as appropriate.
- PRs: Include purpose, linked issues, and before/after output (snippet or screenshot). Note any API changes and update `README.md`/`examples/` accordingly.
- Keep PRs focused and small; add tests for new behavior.

## Security & Configuration Tips
- No secrets or network calls; library is terminal‑only and zero‑dependency.
- Windows users: prefer Windows Terminal/ConEmu; ensure VT processing is enabled for ANSI support.
