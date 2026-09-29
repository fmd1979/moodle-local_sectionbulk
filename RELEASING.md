# Release process

1. Update `version.php`.
2. Update `CHANGES.md`.
3. Run Moodle Plugin CI.
4. Test upgrade on staging.
5. Create a tag such as `v1.2.0`.
6. Build the installable ZIP with `sectionbulk/` as top-level directory.
7. Create GitHub Release and attach ZIP.
8. Upload the release to Moodle Marketplace.
