# Agentic Prompting — Terms & Canonical Usage

This document collects concise definitions, usage notes, and canonical prompt templates for "agentic prompting" — designing prompts that instruct an LLM to behave like an agent (planner, tool-user, verifier). Use this as a reference when writing prompts for Copilot (VS Code), Cursor, Claude Code, or similar systems.

## Goals
- Provide normative definitions for common terms.
- Show practical usage patterns and brief examples.
- Offer a canonical prompt/doc structure teams can copy.

---

## Core Terms

- Agentic Prompting
  - Definition: Writing prompts that instruct an LLM to take on an agent role: plan, select tools/actions, execute steps, and verify results. Emphasizes multi-step workflows and tool integration.
  - Usage note: Prefer explicit sub-tasks and success criteria. Limit scope per turn.

- Session / Chat Session
  - Definition: A contiguous interactive exchange between user and model. Sessions maintain context (memory) across messages.
  - Usage note: Use session-scoped state for progressive tasks (e.g., multi-step edit flows). Reset or checkpoint when task boundaries end.

- System Prompt (or Instruction)
  - Definition: High-level, usually immutable instruction that sets the assistant's role, constraints, and formatting rules.
  - Usage note: Keep short, authoritative, and unambiguous. Put safety and data-handling rules here.

- User Prompt
  - Definition: The explicit request from the user describing goals and constraints.

- Assistant Prompt / Output
  - Definition: Model-generated content. In agentic prompting, often structured (JSON, YAML, or marked sections) to allow parsing or tool execution.

- Tool / Action / API Call
  - Definition: External capabilities the agent can invoke (code edit, run tests, call web API). The prompt must include a precise spec for each tool (inputs, outputs, failure modes).

- Planner / Executor / Verifier
  - Planner: Produces a step-by-step plan.
  - Executor: Performs steps (invokes tools or emits actions).
  - Verifier: Checks outputs against success criteria.

- Chain-of-Thought (CoT)
  - Definition: Internal reasoning path. Often omitted from final output for safety or brevity; used during design of prompts to encourage stepwise reasoning.
  - Usage note: If you need transparent reasoning, require a separate `explain` output block.

- ReAct Pattern
  - Definition: Interleaves reasoning and actions (reason -> act -> observe -> repeat).
  - Usage note: Good for tool-using agents. Shape prompts to require an `Action:` field followed by `Observation:`.

- Context Window / Token Budget
  - Definition: The limit on tokens available for prompt+response. Manage by summarizing or truncating older context.

- Few-shot / Examples
  - Definition: Provide 1–5 curated examples to show desired structure or behavior.
  - Usage note: Example quality > quantity; prefer canonical, small examples.

- Instruction Granularity
  - Definition: How coarse or fine each instruction is. Fine-grained instructions reduce ambiguity but increase prompt length.

- Safety & Guardrails
  - Definition: Explicit constraints preventing undesired actions (e.g., "do not call external APIs without user confirm").
  - Usage note: Place critical guardrails into the System Prompt.

---

## Practical Patterns & Short Examples

1) Minimal agentic plan output (useful for Copilot-like edits)

System: You are an assistant that edits code. Always provide a `Plan:` list and a `Patch:` code block when editing files.

User: Fix the bug in `src/utils/math.ts` where divide-by-zero can occur.

Assistant (expected):

Plan:
- Identify division sites in `src/utils/math.ts`.
- Add checks to prevent divide-by-zero; return null or throw with message.

Patch:
```diff
*** Begin Patch
*** Update File: src/utils/math.ts
@@
-  return a / b;
+  if (b === 0) throw new Error('division by zero');
+  return a / b;
*** End Patch
```

2) Tool-calling structure (ReAct style)

- Output fields: `Thought`, `Action`, `ActionInput`
- After each `Action`, the system returns `Observation` which the assistant uses for the next `Thought`.

Example:
```
Thought: Need to fetch user list to validate assignee
Action: http_get
ActionInput: { "path": "/api/users" }
```

3) Form / API payload canonicalizer (useful for UI forms)

- Include: `ValidationRules`, `Transformations` (e.g., convert empty string to null), `ResponseMapping`.

---

## Canonical Prompt / Doc Section (copyable)

**Purpose**: One-sentence description of the goal.

**System**: Role + constraints (1–3 short lines).

**Inputs**: Enumerate inputs and their types.

**Success Criteria**: Clear, testable outcomes.

**Steps / Plan**: Ask the model to produce a numbered plan before making changes.

**Action Schema**: Provide the exact JSON schema for actions/tools the model may emit.

**Examples**: 1–3 small examples following the schema.

**Output Format**: Exact structure the model must return (JSON only, or tagged sections).

**Safety Rules**: Deny dangerous operations; require user confirmation for external effects.

---

## Quick Templates

1) Copilot / VS Code quick-edit template
```
System: You are a code-edit assistant. Output must include `Plan` then a `Patch` in unified-diff format.
User: <task description>
```

2) Cursor-style multi-step agent
```
System: You are an agent. Provide: { plan: string[], actions: Action[] }
Action: { type: "edit" | "run" | "http", target?: string, payload?: any }
```

3) Claude Code (structured JSON)
```
System: Return a JSON with keys: "plan","steps","artifacts". Each step may include "action" and "expected_result".
```

---

## Authoring Tips
- Be explicit about formats (diffs, JSON, YAML).
- Provide validators/examples for every action schema.
- Keep system-level safety concise and non-negotiable.
- Use `Plan` as a separate initial response to let reviewers confirm before destructive actions.
- Limit single-turn scope (one edit, one API call) unless you explicitly request multi-step execution.

---

## References & Further Reading
- Prompt engineering basics (internal notes)
- ReAct paper (reason + action patterns)
- Best practices for tool-use with LLMs

---

## Copilot (VS Code) — Examples & Snippets

This section contains Copilot-specific prompt examples and a companion VS Code snippet to quickly insert canonical agentic prompts when requesting code edits.

- Copilot guidance:
  - Keep the System line short and authoritative (role + strict output format).
  - Require a `Plan:` before any `Patch:` so reviewers can confirm the plan before applying edits.
  - Use unified-diff format for `Patch:` so tools (or human reviewers) can apply changes.

Example 1 — Bugfix (Plan + Patch):

System: You are a code-edit assistant for VS Code. Always return a `Plan:` numbered list, then a `Patch:` in unified-diff format wrapped in ```diff blocks. Do not execute changes — only propose the patch.

User: Fix the null-check in `src/utils/math.ts` that can cause an exception.

Assistant (expected):

Plan:
- Find potential null dereferences in `src/utils/math.ts`.
- Add safe guards and return explicit errors.

Patch:
```diff
*** Begin Patch
*** Update File: src/utils/math.ts
@@
-  return a / b;
+  if (b === 0) throw new Error('division by zero');
+  return a / b;
*** End Patch
```

Example 2 — Refactor + Explain (Plan + Patch + Explain):

System: You are Copilot in edit mode. Provide `Plan`, `Patch`, and an optional `Explain:` brief block that summarizes why changes are safe.

User: Simplify `calculateTotal()` and handle empty arrays.

Assistant (expected):

Plan:
1. Guard empty input in `calculateTotal()`.
2. Replace manual loop with `reduce` for clarity.

Patch:
```diff
*** Begin Patch
*** Update File: src/cart/total.ts
@@
-  let sum = 0;
-  for (const v of values) sum += v;
-  return sum;
+  if (!values || values.length === 0) return 0;
+  return values.reduce((a, b) => a + b, 0);
*** End Patch
```

Explain:
- Replacing the loop with `reduce` is idiomatic and returns `0` for empty arrays, preventing `undefined` errors.

---

Use the included VS Code snippet file to quickly insert the above templates. Snippet prefixes:
- `agent-plan-patch` — full Plan + Patch template
- `copilot-quick-edit` — minimal quick-edit template

File: .vscode/copilot-agentic-prompts.code-snippets

---

## Copilot — SDLC-focused Examples

Below are Copilot-tailored patterns and short examples for common SDLC activities: architecting, tests, multi-file edits, knowledge maintenance, and playbook improvements. Each example follows the `Plan` + `Patch` (and optional `Explain`) structure so reviewers can validate the plan before applying changes.

### 1) Architecting / High-level design changes

Use case: Introduce a new `Queue` abstraction across multiple modules.

Plan:
- List impacted modules and interfaces.
- Create new `src/lib/queue.ts` with minimal API and tests.
- Replace direct queue implementations in 2–3 files with the new abstraction.

Patch (example snippet):
```diff
*** Begin Patch
*** Add File: src/lib/queue.ts
+export interface Queue<T> {
+  push(item: T): void;
+  pop(): T | undefined;
+}

+export class InMemoryQueue<T> implements Queue<T> {
+  private items: T[] = [];
+  push(item: T) { this.items.push(item); }
+  pop() { return this.items.shift(); }
+}
*** End Patch
```

Notes:
- After proposing this patch, run tests and open an approval checklist for each replaced file.

### 2) Tests — Adding or fixing unit tests

Use case: Add a unit test that covers a previously untested codepath.

Plan:
- Identify uncovered function in `src/utils/math.ts`.
- Add `tests/unit/math.test.ts` with edge-case assertions.

Patch (example):
```diff
*** Begin Patch
*** Add File: tests/unit/math.test.ts
+import { divide } from '../../src/utils/math';
+
+test('divide by zero throws', () => {
+  expect(() => divide(3, 0)).toThrow('division by zero');
+});
*** End Patch
```

Execution: After applying the patch, run the test suite and include the failing/passing output in the PR description.

### 3) Multi-file edits (refactors, API changes)

Use case: Rename a prop throughout the codebase or change a function signature.

Plan:
- Create a targeted list of files to update.
- Apply coordinated `Patch` sections for each file in one unified patch.
- Add migration notes and update the changelog.

Patch (multi-file example):
```diff
*** Begin Patch
*** Update File: src/components/TaskCard.vue
@@
-props: { assigned_to: Number },
+props: { assignee_id: Number },
*** End Patch

*** Begin Patch
*** Update File: src/pages/Tasks.vue
@@
-  assigned_to: task.assigned_to ?? null,
+  assignee_id: task.assigned_to ?? null,
*** End Patch
```

Notes:
- Keep the `Patch` atomic but grouped; reviewers should be able to apply it in a single PR.

### 4) Knowledge maintenance (docs, playbooks)

Use case: Update the project's playbook or architecture docs to reflect a new process.

Plan:
- Edit `docs/playbook.md` with new steps and links.
- Add an example checklist and an owner tag.

Patch (docs example):
```diff
*** Begin Patch
*** Update File: docs/playbook.md
@@
-## Release Checklist
- - Run tests
- - Bump version
- - Tag release
-
+## Release Checklist
+- [ ] Run unit & integration tests
+- [ ] Update changelog with PR summaries
+- [ ] Bump version and tag
+- [ ] Notify stakeholders
*** End Patch
```

### 5) Improving playbooks and automation

Use case: Turn a manual checklist into an automated script and document it.

Plan:
- Add `scripts/release.sh` that runs tests and publishes artifacts.
- Update `docs/playbook.md` to reference the script and how to run it.

Patch (script + docs):
```diff
*** Begin Patch
*** Add File: scripts/release.sh
+#!/usr/bin/env bash
+set -euo pipefail
+pnpm test
+pnpm run build
+echo "Release steps executed"
*** End Patch
```

Follow-up: Add CI job step that calls `scripts/release.sh` on tagged commits.

---

Authoring tips for SDLC prompts
- Always include success criteria (tests passing, CI green, changelog updated).
- Mark which changes are breaking and list migration steps.
- When proposing multi-file edits, include a short rollback plan.
- Use `Explain:` to justify design decisions for reviewers.

Files added/edited:
- `AGENTIC_TERMS.md` — appended SDLC Copilot examples
- `.vscode/copilot-agentic-prompts.code-snippets` — created earlier for quick insertion

---

## Copilot — Code Review Feedback Loops

These patterns focus on using Copilot-style agentic prompts to respond to code review comments, iterate on patches, and keep reviewers coordinated.

Pattern: `Plan` → `Patch` → `Explain` → `AddressReview`

1) Responding to a reviewer comment

Use case: A reviewer requests a safer null-check and a rationale.

Plan:
- Update the function to handle null inputs.
- Add a unit test covering the null case.
- Add an `Explain` block to justify the change.

Patch:
```diff
*** Begin Patch
*** Update File: src/utils/parser.ts
@@
-  return process(input.value);
+  if (input == null) return null;
+  return process(input.value);
*** End Patch
```

Explain:
- Returning `null` makes the API explicit about missing inputs and keeps the `process` function contract intact.

AddressReview (follow-up format):
```
Reviewer comment: Please handle null inputs and explain why returning null is safe.

Response:
Plan: Added guard and test. See Patch. Explain: Returning null is explicit and avoids throwing; tests added to cover behavior.
Patch: <as above>
```

2) Iterative multi-round reviews (small diffs)

When reviews request multiple small changes, keep each reply focused:
- Provide a short `Plan` describing the single change.
- Provide a tiny `Patch` that is easy to review and apply.
- If changes are related, include a short migration note.

3) Automated reviewer checklist generation

Have Copilot produce a reviewer checklist from the `Plan` so reviewers can quickly gate on each item. Example output:

```
Review checklist:
- [ ] API contract unchanged
- [ ] Tests added/updated
- [ ] Docs updated
```

Authoring tips for review loops
- Keep each change small and test-first when possible.
- Use `Explain:` to document design decisions for future maintainers.
- Reference PR numbers or issue IDs when addressing past decisions.



