# Project Agent Rules

## Tooling and workflow

- Prefix shell commands with `rtk` and follow `C:\Users\fikri\.codex\RTK.md`.
- Treat `Docs/plan.md` as requirements source, `Docs/todo.md` as the Markdown checklist, and `Docs/update.md` as the implementation audit trail.
- Preserve the UI/backend boundary: UI work may use mock/local state; do not add backend integrations unless the user explicitly asks for them.

## Skills and plugins

- Use the most relevant installed skill before work. UI tasks use UI/UX guidance; new behavior uses brainstorming before implementation; agent instruction changes use writing-for-agents.
- Route planning, architecture, diagnosis, implementation, TDD, research, review, and handoff work through the matching Matt Pocock skill; use `ask-matt` when the route is unclear.
- When ECC is installed and available, use its TDD, security-review, and verification workflows for matching tasks.
- Apply Ponytail principles to keep implementations minimal, standard-library-first, and free of unrequested abstractions.
- Use Caveman’s concise communication style only when the user requests brevity or names Caveman mode; keep code, safety warnings, and multi-step instructions explicit.
- Do not claim ECC, Ponytail, or other plugins are globally active unless `codex plugin list --json` verifies them in the active `CODEX_HOME`.

## Delivery

- Record actual changes, decisions, tests, and blockers in `Docs/update.md`.
- Mark `Docs/todo.md` items `[x]` only after acceptance criteria and verification are complete.
