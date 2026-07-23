# Known issues and recommendations

This document tracks the issues that still merit attention. Items that have been verified as resolved and are no longer actionable are removed from the active list to keep the tracker concise.

## 1. Icon rendering and Vuetify configuration
- Location: vue3/src/App.vue and vue3/src/main.ts
- Symptom: icon-only controls can appear blank if the icon provider is not configured correctly.
- Resolution: Vuetify now uses the mdi-svg icon set through vue3/src/main.ts, and the app depends on @mdi/js for those icons.
- Status: verified and addressed.

## 2. Action button accessibility
- Location: vue3/src/pages/Tasks.vue
- Symptom: icon-only actions can be hard to understand for keyboard and screen-reader users.
- Recommendation: keep labels and titles on icon-only controls so the UI remains accessible even if visual icons fail.
- Status: monitored and should remain consistent in future UI changes.

## 3. Icon dependency consistency
- Location: frontend configuration and component usage
- Symptom: mismatches between icon imports and runtime configuration can cause inconsistent rendering.
- Recommendation: keep icon usage aligned with Vuetify setup and verify it when introducing new UI components.
- Status: verified as compatible with the current configuration.

## Resolved and retired
- Issue 4 was verified as already implemented in the current UI and has been removed from the active backlog.
