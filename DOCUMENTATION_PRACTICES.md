# Documentation practices

This document defines how the repository documentation should be maintained so it stays useful for humans and AI agents.

## Goals

- Keep documentation current with the actual codebase and development workflow
- Make onboarding faster for new contributors and automation tools
- Reduce drift between documented behavior and implementation

## Documentation map

Use the current set of documents as the primary source of truth:

- README.md — product overview, setup, and entry points
- ARCHITECTURE.md — core design and system boundaries
- TECH_STACKS.md — runtime versions, frameworks, and tooling
- PRACTICES.md — coding and architecture conventions
- ISSUES.md — active issues and follow-up work
- DOCUMENTATION_PRACTICES.md — this file

## Maintenance rules

1. Update documentation when behavior changes.
   - If a feature, command, dependency, or workflow changes, update the relevant docs in the same change.
   - Do not leave documentation stale when code changes are merged.

2. Prefer concise, task-oriented content.
   - Focus on what a reader needs to do next.
   - Keep examples practical and close to the repository reality.

3. Keep docs aligned with the workspace structure.
   - Reference folders and files that actually exist in this repository.
   - Prefer links to local files over descriptions that may drift.

4. Separate stable guidance from temporary notes.
   - Keep long-term architecture and onboarding information in the main docs.
   - Track transient issues in ISSUES.md rather than burying them in unrelated files.

5. Remove verified resolutions from issue lists.
   - If an issue is fixed, tested, and no longer relevant, remove it from the active list.
   - Preserve the outcome so future readers are not misled.

## Working with AI agents

When preparing changes that affect documentation:

- Review the existing docs before editing so the update fits established patterns.
- Keep the language explicit and implementation-aware.
- Mention commands, paths, and status with enough detail for the next agent or developer to continue safely.
- If a change introduces a new workflow, add it to both the relevant documentation file and the runbook or setup instructions that depend on it.

## Review checklist

Before considering documentation complete, confirm:

- The content matches the current repository layout
- The setup steps still work with the current Makefile and package manifests
- The stack versions match the actual dependencies in laravel12 and vue3
- The issue tracker only contains unresolved or actionable items
- The documentation remains concise enough to be maintained

## Continuous improvement

Treat documentation as an evolving artifact:

- Revisit the docs after significant feature work, dependency upgrades, or workflow changes.
- When a contributor or AI agent gets blocked by missing context, add the missing note to the appropriate document.
- Prefer small, frequent updates over large documentation rewrites.
