---
applyTo: "**/*.php"
description: Performance optimization guidance
---

# Performance

Optimize only when there is evidence that performance matters.

## Principles

Prefer:

- algorithmic improvements
- reducing unnecessary allocations
- lazy evaluation
- generators for large datasets
- efficient collection operations

Avoid:

- premature optimization
- micro-optimizations
- sacrificing readability

## Existing Code

Before optimizing:

1. Understand the current implementation.
2. Preserve correctness.
3. Minimize behavior changes.

## Trade-offs

When introducing a performance optimization:

- explain any readability trade-offs
- explain any memory trade-offs
- explain any compatibility trade-offs