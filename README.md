# WHO Team Manager

A minimal WordPress plugin that provides a Team Members custom post type with a drag-and-drop sortable grid block.

## Features

- **Team Member CPT** - Name (post title), Job Title (custom meta), Description (post content), Photo (featured image)
- **Team Grid Block** - Gutenberg block to display team members in a responsive, configurable grid
- **Drag & Drop Ordering** - Reorder team members per-block via drag and drop in the editor
- **Responsive Grid** - Auto-collapses from configured columns to 2 on tablet, 1 on mobile
- **Configurable Columns** - 1 to 6 columns via the block sidebar
- **Auto-Updates** - Checks GitHub Releases for new versions via the WordPress dashboard
- **Minimal CSS** - Designed to inherit theme styles where possible

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Installation

### Manual Installation

1. Download or clone this repository into `wp-content/plugins/who-team-manager/`
2. Run `npm install && npm run build` to compile block assets
3. Activate the plugin through the WordPress admin

[Click to download the latest release](https://github.com/chrisfromthelc/who-team-manager/releases/latest/download/who-team-manager.zip).

### From GitHub

```bash
cd wp-content/plugins/
git clone https://github.com/chrisfromthelc/who-team-manager.git
cd who-team-manager
npm install && npm run build
```

## Usage

### Adding Team Members

1. Navigate to **Team Members** in the WordPress admin sidebar
2. Click **Add New**
3. Enter the team member's name as the post title
4. Add their job title in the Job Title field (in the post meta sidebar)
5. Write their description/bio in the content editor
6. Set a photo using the **Featured Image** panel
7. Publish

### Using the Team Grid Block

1. Edit any page or post
2. Add the **Team Grid** block (found under Widgets or search "Team Grid")
3. All published team members will appear in the grid
4. Drag and drop to reorder members within this block instance
5. Use the sidebar to configure the number of columns (1-6)

## Development

### Prerequisites

```bash
composer install
npm install
```

### Build Block Assets

```bash
npm run build       # Production build
npm start           # Watch mode for development
```

### Coding Standards (PHPCS)

```bash
composer phpcs        # Check
composer phpcbf       # Auto-fix
```

### Static Analysis (PHPStan)

```bash
composer phpstan      # Level 5
```

### Rector

```bash
composer rector-dry   # Dry run
composer rector       # Apply changes
```

### Run All PHP Linters

```bash
composer lint         # PHPCS + PHPStan + Rector dry-run
```

### JavaScript Tests (Jest)

```bash
npm run test:js       # Run once
npm run test:js:watch # Watch mode
```

### PHP Tests (PHPUnit)

Tests require the WordPress test suite:

```bash
export WP_TESTS_DIR=/path/to/wordpress-tests-lib
composer test
```

### Building the Plugin ZIP

```bash
npm run plugin-zip    # Builds assets and writes dist/who-team-manager.zip
npm run version:check # Confirms every version string matches
```

The ZIP contains a single `who-team-manager/` folder and installs through **Plugins > Add New > Upload Plugin**.

### Releasing

The `Release` workflow builds the ZIP, installs it into WordPress 6.0 (PHP 7.4) and the latest WordPress (PHP 8.3), then publishes it. It runs on every pull request to `main` without publishing.

1. Bump the version in `who-team-manager.php` (header and `WHO_TEAM_MANAGER_VERSION`), `readme.txt` (`Stable tag`), `package.json` and `src/block.json`.
2. Add a `= X.Y.Z =` entry under `== Changelog ==` in `readme.txt`. It becomes the release notes.
3. Merge to `main`, then either:
   - push a tag: `git tag vX.Y.Z && git push origin vX.Y.Z`, or
   - run **Actions > Release > Run workflow** on `main` with **publish** ticked. This creates the tag for you.

The workflow fails if any version string disagrees, if the tag doesn't match the plugin header, or if the tag already exists. Installed sites pick up the release through the built-in GitHub updater.

### Architecture

```
who-team-manager/
  who-team-manager.php           # Bootstrap + autoloader
  uninstall.php                  # Cleanup on deletion
  includes/
    class-plugin.php             # Singleton orchestrator
    class-team-member-cpt.php    # CPT + meta registration
    class-team-grid-block.php    # Block registration + server-side render
    class-github-updater.php     # GitHub release update checker
  src/
    block.json                   # Block metadata
    index.js                     # Block entry point
    edit.js                      # Editor component (drag & drop grid)
    style.scss                   # Frontend styles
    editor.scss                  # Editor-only styles
  build/                         # Compiled block assets (gitignored)
  tests/
    bootstrap.php                # PHPUnit bootstrap
    TeamMemberCPTTest.php        # CPT tests
    TeamGridBlockTest.php        # Block render tests
    GitHubUpdaterTest.php        # GitHub updater tests
    js/
      edit.test.js               # Jest tests for editor component
```

## License

MIT License. See [LICENSE](LICENSE).
