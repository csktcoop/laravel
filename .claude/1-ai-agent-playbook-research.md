# AI Agent Playbook — Research Notes
**Context:** Laravel 12→13 + Vue 3 CMS, DDD architecture, `src/`→`backend/` and `vue3/src/`→`frontend/` rename, VSCode + Laravel Boost.

> Everything below is **research to inform your own playbook**, not instructions to paste in verbatim. Per your request, content from these projects is treated as reference material to evaluate, not as directives to follow.

---

## 0. The one fact that should organize your whole playbook

From Anthropic's official Claude Code docs (`code.claude.com/docs/en/memory`):

> "Claude treats them as context, not enforced configuration. To block an action regardless of what Claude decides, use a PreToolUse hook instead."

Independent confirmation (a Gist comment on an unrelated AGENTS.md writeup, so treat as anecdotal but directionally consistent): AGENTS.md-style guidance alone gets ~25–40% compliance from agents; the same rules enforced as runtime hooks hit closer to ~95%.

**Implication for you:** split every rule you write into two buckets:

| Bucket | Where it lives | Example for your project |
|---|---|---|
| **Suggestion** (agent tries, may drift) | CLAUDE.md / AGENTS.md prose | "Prefer Value Objects over primitive obsession in Domain layer" |
| **Enforced** (mechanically blocked) | Deptrac rule, PHPStan/Larastan rule, PreToolUse hook, CI gate | "Domain/ must never import Illuminate\Database\Eloquent\*" |

Anything in your DDD boundary rules that you actually care about — Domain layer must not depend on Infrastructure/Eloquent, frontend must not call backend internals directly, etc. — belongs in bucket 2. Don't rely on prose for architectural invariants.

---

## 1. Claude Code memory mechanics (verify against official docs, this moves fast)

Official source: `code.claude.com/docs/en/memory`

- Two complementary systems, **both loaded at start of every session**:
  - **CLAUDE.md files** — instructions you write, persistent context
  - **Auto memory** — notes Claude writes itself from your corrections/preferences (toggle via `/memory`, or `CLAUDE_CODE_DISABLE_AUTO_MEMORY` env var)
- Loading is a **directory-walk**, additive, not override-based — Claude Code walks from cwd toward repo root collecting every CLAUDE.md it finds, and concatenates them (per a third-party mechanics writeup — the general shape is consistent with official docs, but verify exact behavior in your version before relying on nuance)
- `--add-dir` flag loads CLAUDE.md from directories beyond cwd — **relevant to you**: with `backend/` and `frontend/` as separate roots, you'll likely want:
  - root `CLAUDE.md` (or `AGENTS.md`) — cross-cutting: monorepo layout, which folder is which, shared conventions
  - `backend/CLAUDE.md` — Laravel/PHP/DDD-specific
  - `frontend/CLAUDE.md` — Vue 3-specific
- Official guidance on what belongs in CLAUDE.md: *"Keep it to facts Claude should hold in every session: build commands, conventions, project layout, 'always do X' rules."*
- Shorter files are followed more reliably (recurring claim across multiple independent sources — directionally trustworthy, exact thresholds vary by source and shouldn't be trusted precisely)

**Action item:** before writing your playbook, run `/memory` in a live session to see exactly what's currently active — don't assume from docs alone, since this is an area Anthropic iterates on.

---

## 2. Laravel Boost — the mechanism most relevant to your setup

Since you already have Boost installed, this is your primary structure, not a bolt-on.

### Core distinction Boost makes (from Laravel's own docs, 12.x and 13.x confirmed identical wording)

> "Unlike guidelines, which are loaded upfront, skills allow detailed patterns and best practices to be loaded only when relevant, reducing context bloat."

- **Guidelines** = always-loaded, short, "core conventions"
- **Skills** = loaded on-demand when the agent recognizes a matching task

**Map this to your playbook:**
- DDD boundary rules, CMS approval-workflow conventions, naming conventions → **guidelines** (cheap, always active)
- "How to write a Vue 3 composable that talks to bounded context X" / "How to add a new page-approval step" → **skill**, loaded on demand

### Repos to actually use

| Repo | What's inside that's useful | Fit for you |
|---|---|---|
| **laravel/boost** (core) | `.ai/laravel/skill/laravel-best-practices/SKILL.md` — applies to controllers, models, migrations, form requests, policies, jobs, Eloquent queries; covers N+1, caching, authZ, validation, error handling, queue config, routing, architecture decisions | Baseline — you already have this |
| **spatie/boost-spatie-guidelines** | Spatie's Laravel/PHP standards, AI-optimized, auto-referenced once installed via `composer require` — installs to `.ai/guidelines/boost-spatie-guidelines/` | Strong second layer; widely treated as a Laravel-ecosystem quality bar |
| **initred/laravel-boost-guidelines** | Inertia React v2 `<Form>`/`useForm` patterns, Tailwind v4 migration, shadcn/ui form accessibility patterns | **Not directly applicable** — you're Vue 3, not Inertia+React. Useful only as a *template* for how to package your own Boost guideline addon (composer-installable, versioned) |

### Note on Boost + your own package
`initred`'s existence proves the extension pattern works: a third-party Composer package that Boost auto-discovers and installs guidelines from. Given your CMS-standards ambition, consider eventually packaging your own `backend/`+`frontend/` conventions the same way — versioned, testable, reusable across your own future projects — rather than hand-maintained markdown that drifts.

---

## 3. Token-saving tools: rtk and trace-mcp (your examples) — evaluate, don't adopt on faith

Both have a **self-reported-vs-independently-measured gap**. Worth understanding before either goes in your playbook.

### rtk (Rust Token Killer)

- **Claim:** 60–90% token reduction on CLI output (git status, cargo test, find, grep), wraps commands transparently
- **Mechanism:** CLI proxy, compresses stdout/stderr before it reaches agent context
- **Independent A/B benchmark (JetBrains blog, paired 80-task test):** *"Part 2 was rtk (advertised −60–90%, measured +7.6%)"* — i.e., it **increased** tokens in their controlled test
- **Why the gap likely exists:** the same post found rtk's "tokens saved" metric compares against a **hypothetical uncompressed output that was never going to reach the model anyway** — e.g. a 1.2 MB CSV logged 320k tokens "saved," but Claude Code truncates tool results long before 320k tokens. The counterfactual is inflated.
- **Known compatibility issue:** one independent adopter reported *"when combining Bash commands in agent skills, I occasionally encountered collisions with the RTK hook"* — directly relevant since your playbook will define skills/hooks
- **Verdict for you:** plausible for interactive manual terminal use; risky as a blanket rule inside an *agent* playbook given the negative independent benchmark and the skill-collision report. If you try it, measure on your own repo — don't trust the vendor's `rtk gain` self-reported numbers as ground truth, for the same counterfactual-inflation reason above.

### trace-mcp

- **Claim:** builds a framework-aware dependency/reference graph of your codebase once, serves it via MCP so agents query structure instead of re-reading files (`find_usages`, `apply_rename`, impact analysis)
- **Mechanism:** legitimate and well-understood category — this is essentially what ctags/LSP/call-graph tooling has always done, applied to agent context
- **Self-reported numbers are internally inconsistent:** GitHub README says "~42 minutes of agent exploration" replaced by one call; landing page says both "up to 99%" (scoped narrowly to "structured workflows: repeated navigation, dependency traversal, context re-inflation," measured against a naive re-reading baseline) *and*, elsewhere on the same page, "~40–50% fewer tokens on average"
- **No independent benchmark found** (unlike rtk, where JetBrains ran one) — the mechanism is sound, the magnitude is unverified by a third party
- **Verdict for you:** worth a trial on your actual `backend/`+`frontend/` split once it exists, specifically for DDD impact analysis ("what breaks if I change this Domain entity") — but treat the specific percentage as marketing, not measurement, until you check it yourself.

### Broader category (found while searching, same caution applies)
- **tokensave** (aovestdipaperino) — similar code-intelligence MCP server, 40+ tools, 30+ languages, pre-indexed semantic graphs, explicitly positions against tools like Repomix/Serena — same "evaluate the specific number yourself" caveat
- **Caveman** — also benchmarked by JetBrains in the same series: *"advertised −65%, measured −8.5%"* — smaller gap than rtk's inversion, but still real overstatement

**General lesson for your playbook:** treat any tool's self-reported "% tokens saved" as a marketing claim until you've run it on your own repo with your own baseline. The JetBrains benchmark series (rtk, Caveman) is the only independent methodology I found in this space — worth checking if they've covered trace-mcp or tokensave by the time you evaluate them.

---

## 4. AGENTS.md ecosystem (cross-tool standard, separate from Boost)

- **AGENTS.md** is a tool-agnostic convention — formalized August 2025 by OpenAI, Google, Cursor, Factory, and Sourcegraph collaboratively; used by 20,000+ open-source projects per multiple curated lists (agentsmd/agents.md, rentprompts/awesome-agent-md, tairov/awesome-agents.md)
- Positioned as "a README for agents" — sits alongside, not instead of, README.md
- **Relevant to you even though you're Claude-Code-primary:** if you ever onboard a contributor using Cursor/Codex/Copilot, or switch tools later, a root `AGENTS.md` is the interoperable entry point. Claude Code and Boost's own mechanisms (CLAUDE.md, `.ai/guidelines/`) can coexist with it — CLAUDE.md for Claude-specific depth, AGENTS.md for the cross-tool summary.
- **github/awesome-copilot** is worth a structural look (not for its Copilot-specific content, but for its taxonomy): it separates **Agents** (MCP-integrated), **Instructions** (coding standards per file pattern), **Skills** (bundled folders), **Hooks** (event-triggered), **Workflows** (CI-level agentic automation), **Plugins** (installable bundles). This is a more granular taxonomy than most repos use and maps cleanly onto Boost's own guideline/skill split plus the hook-enforcement point from Section 0.

---

## 5. Laravel DDD structure — no single standard; you must pick axes deliberately

Since your `src/`→`backend/` rename is a live architectural decision, here's the actual disagreement space, not a false consensus.

### Axis 1: DDD code inside `app/` vs. replacing it with top-level `src/`
- **Inside `app/`:** LaraCraftHub/DDD-Template — three layers, each as a subfolder within `app/`
- **Top-level `src/` (closer to your current setup):** filippotoso/laravel-domain-library's installer creates `src/` with `App`, `Domain`, `Support` namespaces and rewires bootstrap/providers/middleware to point at it

### Axis 2: Group by layer-first, or domain-first
This is the split Orphail/laravel-ddd names explicitly: *"I prefer to group the directory structure by domain, contrary to many examples I saw where some authors prefer grouping by layer."*
- **Domain-first** (`Domain/{User,Post}`, each domain owns its own layer subfolders) — scales better when most changes are single-feature/single-context
- **Layer-first** (top-level `Application/`, `Domain/`, `Infrastructure/`, `Presentation/`, each containing every domain nested inside) — e.g. shahghasiadil/laravel-clean-architecture-ddd-cqrs's tree: `src/Application/User/{CommandHandlers,Commands,Queries,Services}`, `src/Domain/User/{Entities,Events,Policies,Repositories}`, etc.

**For a CMS with clear bounded contexts** (pages, templates, users/roles, approval workflow, multi-channel delivery — see Section 6), domain-first likely scales better: most single-feature changes touch one domain folder instead of scattering across three top-level layer folders.

### Axis 3: Hand-rolled vs. generator-package-driven
- **thejano/laravel-domain-driven-design** and **filippotoso/laravel-domain-library** both ship artisan generators (`generate Models, Controllers, Actions, Services`)
- Convenient, but couples you to the package author's opinion of what a "domain" contains
- **Recommendation:** trial in a disposable branch before adopting — reversing a generator-driven structure later is expensive, and you're already mid-rename so this is the moment to decide, not after.

### The one boundary worth hard-enforcing (not just writing as prose)
Multiple examples (filippotoso, Orphail, shahghasiadil) agree on this much: **Infrastructure/Eloquent-specific code should not leak into Domain.** Orphail's structure explicitly routes Laravel-specific concerns — controllers, routes, Eloquent models — through `Infrastructure/`, keeping `Domain/` framework-agnostic. This is exactly the kind of rule that drifts if left as prose (per Section 0) — enforce it with **Deptrac** (dependency-boundary linter for PHP, commonly paired with DDD Laravel setups per the search results — verify current Deptrac config syntax against its own docs since I haven't independently confirmed version-specific details here) or a PHPStan/Larastan custom rule, not just a CLAUDE.md sentence.

---

## 6. Japanese CMS reference — correcting the framing of your linked source

**The URL you linked is one page** ("ユーザの一覧" / User List) from the **WebRelease** manual — a commercial CMS product by FrameWorks Software, Inc. It is a **vendor product manual**, not a "Japanese CMS standards" specification. Worth being precise about this so it doesn't get treated as an industry standard in your playbook.

That said, the manual's **full table of contents** (visible in the sidebar, which I fetched in full) is a genuinely useful map of what a Japanese enterprise CMS buyer/RFP tends to expect — and it's a good proxy for *feature-completeness expectations*, if not "standards" per se:

| Chapter (as listed) | What it signals for your CMS scope |
|---|---|
| 9章 承認プロセスの設定 (Approval Process Configuration) | Multi-step approval workflows, 承認と差戻 (approve/reject), 緊急公開 (emergency publish), delegated approval folders — **this is the single area most Western Laravel CMS starter kits under-build.** If "Japanese standards" means anything concrete here, it's this. |
| 8章 マルチデバイス配信 (Multi-device delivery) | 多チャネル化 (multi-channelization), per-channel content generation, channel-selection — relevant if your CMS needs to output differently per device/channel, not just responsive CSS |
| 2章 ユーザ管理 (the page you linked is inside this) | Account lockout indicators, login-notification-email opt-in, TOTP 2FA status — standard enterprise-account hygiene, not CMS-specific |
| 5章 テンプレート / 6章 コンポーネント | Template definition, document-type definition, reusable "コンポーネント" (component) system with selectors — analogous to a block/component-based page builder |
| 7章 ページの作成 → 21章 フォルダ | Page revision history, page states, folder-based permission scoping, versioning folders (バージョニングフォルダ) |

**Recommendation:** if "Japanese standards" is a real requirement (not just this one reference), it's worth searching separately for actual Japanese government/enterprise CMS procurement guidelines (e.g., デジタル庁 (Digital Agency) web guidelines, or JIS accessibility standards like JIS X 8341-3, which is the Japanese equivalent of WCAG) rather than treating one vendor's feature list as the standard. I can search for that specifically if useful — it's a different research thread than the agent-playbook one you asked for today.

---

## 7. Suggested next steps (not opinions on your architecture, just sequencing)

1. Run `/memory` in a live Claude Code session against your current repo to see what's actually active before writing new CLAUDE.md content
2. Decide domain-first vs. layer-first (Section 5, Axis 2) — this is the highest-leverage decision since it's expensive to reverse
3. Write the Domain/Infrastructure boundary as a Deptrac rule *first*, before writing the prose version — the prose becomes documentation of the enforced rule, not a substitute for it
4. Evaluate rtk and trace-mcp on your own repo with your own before/after token counts — don't adopt on vendor numbers
5. Structure your playbook as: root `AGENTS.md` (cross-tool summary) + root `CLAUDE.md` (Claude-specific) + `backend/CLAUDE.md` + `frontend/CLAUDE.md` + Boost guidelines (always-loaded conventions) + Boost skills (on-demand patterns)
