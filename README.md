# Bulk Sections & Quizzes Manager (`local_sectionbulk`)

A Moodle local plugin for previewing and applying bulk changes to course sections and quizzes from the web administration interface, without using CLI scripts.

**Developed by SiteEcuador · Msg. Franklin Moya**

## Compatibility

- Minimum declared version: Moodle 4.3.
- Designed for Moodle 4.3, 4.5 LTS, 5.0 and 5.1 environments.
- Before production use, test the plugin on the exact Moodle/PHP/database combination used by the site.

## Scope selection

The administrator can select:

- One or more courses by course ID.
- A complete course category.
- Optionally all child categories.

Every write operation requires a preview first.

## Section operations

A section target can be selected as: all regular sections in each course, one section number, multiple section numbers/ranges (for example `1,2,4-7`), or an exact section name.

Available operations:

1. Add/update `Date from`.
2. Add/update `Date until`.
3. Add/update both date restrictions.
4. Remove only `Date from`.
5. Remove only `Date until`.
6. Remove all date restrictions while preserving other availability conditions.
7. Add/update a custom user profile field restriction, for example `Code_Book contains BUCK`.
8. Remove only `Activity completion` restrictions while preserving the rest of the availability tree.
9. Create a new section in all courses in the selected scope.

The plugin preserves unrelated availability conditions such as groups, grades, profile fields, completion conditions and nested AND/OR trees.

## Quiz operations

Quizzes can be targeted by:

- All quizzes in the selected courses.
- Exact quiz name.
- Activity ID number.
- Optional section-number filter.

Available operations:

1. Set **Open the quiz**.
2. Set **Close the quiz**.
3. Set both Open and Close dates.
4. Remove Open date.
5. Remove Close date.
6. Remove both Open and Close dates.
7. Set allowed attempts (`0` = unlimited).
8. Set grading method:
   - Highest grade.
   - Average grade.
   - First attempt.
   - Last attempt.
9. Automatically set **Highest grade** only on quizzes that allow two or more attempts, optionally including unlimited attempts.

When the grading method changes, the administrator can choose to recompute final quiz grades and update the Moodle gradebook. Quiz date changes also refresh quiz calendar events and open-attempt timing state.

## Safety

- Requires capability `local/sectionbulk:manage`.
- The Manager archetype receives the capability by default.
- Preview is mandatory before write operations.
- Preview confirmation expires after 30 minutes.
- Course caches are rebuilt after changes.
- An audit event is emitted for every applied batch.
- The plugin does not store its own personal data and implements Moodle's privacy API as a null provider.

## Installation

1. Extract the `sectionbulk` directory into:

   `moodle/local/sectionbulk`

2. Log in as a Moodle administrator.
3. Visit **Site administration > Notifications**.
4. Complete the upgrade/install process.
5. Open:

   **Site administration > Plugins > Local plugins > Bulk sections and quizzes manager**

## Upgrade

Replace the previous `/local/sectionbulk` directory with the 1.2.0 files and visit **Site administration > Notifications**. No database schema migration is required.

## Marketplace preparation

Version 1.2.0 includes repository-ready GitHub Actions for Moodle Plugin CI, PHPUnit coverage for key bulk behaviours, public issue templates, security and contribution guidance, and a Marketplace submission checklist. Before submission, the CI matrix must pass in the public repository and the plugin should be manually tested on staging sites matching the Moodle/PHP/database combinations claimed in Marketplace.

See `MARKETPLACE.md`, `CONTRIBUTING.md`, `SECURITY.md`, and `RELEASING.md`.

## License

GNU GPL v3 or later.

---

# Resumen en español

Este plugin permite ejecutar desde la interfaz web las operaciones masivas que normalmente se realizaban por archivos PHP CLI: fechas y restricciones de secciones, creación de secciones y administración masiva de fechas, intentos y método de calificación de cuestionarios.

Incluye siempre una **Vista previa** antes de aplicar cambios y muestra por curso qué elemento será modificado.

**Créditos: SiteEcuador · Msg. Franklin Moya**
