You are maintaining a version-2 repository catalog for a token-efficient
web-development agent playbook.

Your task is to create or update exactly provided YAML repository records.

## Scope

The catalog may include any repository related to AI.

The records must answer:

1. What does this repository do?
2. When should a web-development agent use it?
3. When should it avoid it?
4. Where does it fit in the development ecosystem?
5. What measurable or repository-native benefit does it provide?
6. Can it run within 4 GB RAM?
7. Can it fully or partially combine with other catalog items?
8. Should the playbook adopt it, partially adopt it, study it, benchmark it first, or skip it?

## Input

{{PASTE_REPOSITORY_IDS_OR_NAMES}}

## Research rules

1. Use the canonical GitHub repository as the primary source.
2. Inspect the most useful available sources, e.g. README, documentation, official website. Prefer the source with the most
   authoritative information.
3. Do not infer technical capability from stars or forks.
4. Treat stars, forks, release counts, and commit activity only as adoption
   or activity signals.
5. Do not invent benchmark results.
6. Distinguish clearly between:
   - repository claim
   - official benchmark
   - independent benchmark
   - analyst assessment
   - unknown
7. If evidence is missing, write `unknown`.
8. Do not assign `ram-4gb: verified` merely because the project is written
   in Rust, has a small binary, or has few dependencies.
9. Assign `ram-4gb: verified` only when:
   - official documentation supports 4 GB or less, or
   - a reproducible test confirms the relevant operation within 4 GB, or
   - the item is only a lightweight skill, text file, or local CLI and the
     tested scope is explicitly stated.
10. For server, database, model-hosting, GPU, desktop, or large-indexing
    projects, prefer `unknown` unless a real deployment benchmark exists.
11. Keep the record concise. Use short phrases instead of paragraphs.
12. Preserve existing values unless new evidence directly changes them.
13. Prefer the controlled vocabulary below.
14. If no existing term accurately describes the repository, add a concise
    lowercase hyphenated term and report it in `validation_notes.new_terms`.
15. If the repository identity is ambiguous, stop and report the ambiguity
    instead of silently choosing a repository.

## Naming rules


- Prefer the normalized form `vendor-project`.

## Controlled tag vocabulary

domain:
- web-development
- ai-agents
- agent-engineering
- developer-tools
- frontend
- backend
- data
- finance
- media
- project-management
- system-design

function:
- agent-skills
- architecture
- code-understanding
- dependency-analysis
- design-quality
- document-ingestion
- graph-database
- knowledge-graph
- memory
- orchestration
- project-management
- retrieval
- rag
- token-reduction
- tracing
- workflow
- backtesting
- media-editing

layer:
- frontend
- backend
- data
- agent-runtime
- developer-workflow
- infrastructure
- desktop
- knowledge
- storage
- orchestration

interface:
- api
- cli
- desktop
- library
- mcp
- web-ui
- skills
- hooks
- graph
- workflow

runtime:
- browser
- cpu-only
- docker
- local
- python
- rust
- typescript
- server
- gpu-required

resource:
- ram-4gb
- cpu-only
- gpu-required
- remote-model
- local-model
- server-required

## Required YAML schema

Return valid YAML only. If anything is uncertain, include it in the `validation_notes` field.

Use this exact structure:

id: owner/repository
name: concise-catalog-name
title: one-line description
url: canonical-repository-url
adoption: [main-list, low-list]

tags:
  domain: []
  function: []
  layer: []
  interface: []
  runtime: []
  resource: []

use_when:
  - short condition
  - short condition

avoid_when:
  - short condition
  - short condition

ecosystem:
  layer: one controlled layer
  role: concise ecosystem role

benefit:
  type: one function value
  value: concise benefit statement
  quantified:
    - repository-native metric or capability count
    - benchmark result, if verified
  evidence: one of:
    - repository-claim
    - official-benchmark
    - independent-benchmark
    - repository-metadata
    - analyst-assessment
    - unknown

ram-4gb: one of:
  - verified
  - plausible
  - unknown
  - fails

ram-4gb_scope: required when `ram-4gb` is `verified`
ram-4gb_note: required when status is plausible, unknown, or fails

combine:
  with: []
  complements: []
  overlaps: []
  replaces: []
  incompatible_with: []

decision:
  web_playbook_value: one of:
    - very-high
    - high
    - medium
    - low
    - none
  adopt: one of:
    - adopt-full
    - adopt-partial
    - study
    - benchmark-first
    - skip
  adopt_parts:
    - specific reusable component
  benchmark:
    - metric to validate locally

## Composition rules

Use these distinctions:

- `with`: items that can be used in the same architecture.
- `complements`: items that solve adjacent problems.
- `overlaps`: items with materially similar functions.
- `replaces`: items this repository could substitute for.
- `incompatible_with`: items or deployment assumptions that conflict.

Do not claim that two repositories are compatible merely because both
mention agents, graphs, or RAG.

## Output rules

- Return one valid complete YAML record for each unambiguous input.
- Keep descriptions concise.
- Use lowercase, globally unique catalog names.
- Prefer the normalized form `vendor-project`.
- Add a `validation_notes` section only for uncertainty, new terms, missing evidence, ambiguous identity, licensing concerns, required local benchmarks, etc.