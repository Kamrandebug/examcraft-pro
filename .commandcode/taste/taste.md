# Taste (Continuously Learned by [CommandCode][cmd])

[cmd]: https://commandcode.ai/

# Documentation
- Keep WALKTHROUGH.md updated with latest codebase changes whenever features, routes, or auth flow change. Confidence: 0.80

# Styling
- Use Bootstrap 5 classes only — do not use Tailwind classes in Vue components or Blade views. Confidence: 0.70
- Use the Academic Authority design tokens (navy #1B2A4A, gold #C9A84C, parchment #F7F2E4, crimson #8B1A1A) via CSS variables rather than hardcoded hex values in Vue component/Blade styles. Confidence: 0.75

# Exam Formatting
- Format exam papers to match formal Cambridge style: plain black text (no hyperlinks/underline/inline color), Times New Roman 10pt, tight option spacing (compact ~65-70px rows), bold-only headings. Confidence: 0.80
- Inline option images on MCQ papers must have NO border/box-shadow/outline wrapper (Cambridge style). Confidence: 0.70

# Development Workflow
- Prefers minimal, non-destructive changes: read the original file first, reuse only existing state/fields, and don't add new fields or rewrite existing logic (e.g. leave the generate() action untouched). Confidence: 0.85
- Keeps original/legacy files for rollback (rename to a Legacy suffix) rather than deleting them. Confidence: 0.65
- New Vue components should use the Composition API with <script setup>. Confidence: 0.70
