# Project Agent Instructions

## Scope

- Work only inside this repository unless explicitly authorized.
- Do not modify files unrelated to the requested task.
- Preserve existing user changes.
- Ask before changing architecture or expanding the agreed scope.

## Environment and secrets

- Never read, modify, reveal, copy, or commit the real `.env`.
- Files matching `.env.*` may only be read after explicit user approval.
- Use `.env.example` or `.env-example` as the configuration reference.
- Never expose credentials, passwords, tokens, private keys, or production endpoints.

## Git safety

- Inspect `git status` before editing.
- Preserve uncommitted changes that already exist.
- Never run `git reset`, `git clean`, force push, or commands that discard changes.
- Do not commit or push unless explicitly requested.
- Review the final diff before declaring completion.

## Database safety

- Confirm the active environment and database before running database commands.
- Never run destructive operations such as fresh, reset, drop, truncate, or bulk delete without explicit approval.
- Prefer read-only inspection while diagnosing database issues.

## Workflow

- Understand the existing implementation before editing.
- Keep changes minimal and consistent with the current architecture.
- Run relevant tests, linting, or validation.
- Before finishing, report:
  - files changed;
  - commands and tests executed;
  - failed or skipped validation;
  - unresolved risks.