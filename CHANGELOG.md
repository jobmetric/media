# Changelog

## 2.1.0

- Add a storage-backed file-manager API for Unicode uploads, folders, rename, copy/move, conflict resolution and protected soft deletion.
- Implement the previously empty `Media::move` operation and maintain closure paths during tree moves.
- Add ZIP creation and bounded extraction with traversal protection.
- Add paginated ZIP and spreadsheet preview data for application-owned interfaces.
- Keep existing schema and media relations; explicitly confirmed upload replacement preserves references.
- Add independent file-manager regression tests.
