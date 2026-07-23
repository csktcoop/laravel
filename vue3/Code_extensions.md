# Extensions for Vue 3 development in VS Code
## 🌟 Core & Language Support
- Vue - Official: Provides essential syntax highlighting, template type-checking, and auto-completion. Note: Ensure you disable the older Vetur extension, as it is deprecated and conflicts with Vue 3 tooling.
- TypeScript Vue Plugin (Volar): Enables TS server support to recognize .vue files inside standard .ts files.
## ⚡ Productivity & Snippets
- Vue VSCode Snippets (by Sarah Drasner): Generates quick Vue single-file component scaffolding using shortcuts like vbase.
- Vue 3 Snippets (by Hollow Tree): Specifically targets Vue 3 Composition API hooks and syntax structure.
- Auto Rename Tag: Automatically updates the matching closing HTML/Vue component tag when you edit the opening tag.
## 🎨 Formatting & Linting
- Biome (was ESLint): Flags layout code quality bugs and pairs perfectly with eslint-plugin-vue to catch Vue-specific template errors.
- Biome (was Prettier - Code formatter): Keeps Single File Components (.vue) clean and standardizes indentation across script, template, and style blocks.
- Error Lens: Forces warnings and syntax errors to render inline right next to the code line, preventing you from missing failed compilations.
## 💻 Styling & Ecosystem
- Tailwind CSS IntelliSense: Gives autocomplete suggestions for classes if you use Tailwind CSS within your utility-first components.
- Nuxtr: Recommended if your Vue 3 application scales up into a Nuxt 3 architecture, helping automate file and component creation.