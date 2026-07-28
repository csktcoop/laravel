# TODO

This checklist captures the gaps needed to make the workspace feel more like a senior-level full-stack demo.

## Architecture and code quality
- [ ] Strengthen the separation between domain, application, and infrastructure concerns in both Laravel and Vue code.
- [ ] Standardize naming, folder structure, and module boundaries across the monorepo.
- [ ] Reduce duplication and improve consistency in shared types, utilities, and API integration patterns.
- [ ] Improve code comments and documentation so the project is easier to navigate for reviewers.

## Backend
- [ ] Finish authorization behavior so admin and user permissions are clearly enforced and easy to reason about.
- [ ] Add consistent API resources, DTOs, and error responses for task operations.
- [ ] Improve task queries with pagination, sorting, filtering, and eager loading where appropriate.
- [ ] Add realistic seed data and demo flows for a stronger showcase experience.
- [ ] Add automated tests for authentication, authorization, and task CRUD behavior.

## Frontend
- [ ] Polish the task management experience with a more refined form flow and better feedback states.
- [ ] Improve auth UX with stronger loading, error, and redirect handling.
- [ ] Add client-side validation and clearer inline error messaging.
- [ ] Improve accessibility, responsive layout, and visual consistency across pages.
- [ ] Refactor repeated UI logic into reusable components where it makes sense.

## Quality and reliability
- [ ] Add linting, formatting, and static analysis checks for both apps.
- [ ] Add integration or end-to-end tests for the core happy path.
- [ ] Improve observability and graceful error handling for production-style scenarios.
- [ ] Make the app easier to run from a fresh machine with clear setup instructions.

## Documentation and presentation
- [ ] Update the README so it clearly explains setup, assumptions, limitations, and demo credentials.
- [ ] Add screenshots, a short walkthrough, or a polished demo narrative.
- [ ] Ensure the repository feels complete enough to share with recruiters or hiring managers.
