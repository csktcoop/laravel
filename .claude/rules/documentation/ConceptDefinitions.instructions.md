# Documentation Concept Definitions

## Deflate Table

A simplified table-like list where:

- The first item defines the columns (header row).
- Each subsequent item is a row, with values separated by dashes.

Example: ../README.md — Complete SDLC

## ID as Heading

Agents can jump to the exact target via the slug heading:

- Instead of descriptive heading, we define ID.
- If ID collides, we append `-X`(e.g., `## id-as-heading-1`)
- Link remains readable in the link text

Examples:

```md
## id-as-heading
## id-as-heading-1
[ID as Heading](file.md#id-as-heading)
```