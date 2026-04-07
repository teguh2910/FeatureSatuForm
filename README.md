# FeatureSatuForm

Laravel package for SATU Form feature module.

## Installation

1. Add repository in host app `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/teguh2910/FeatureSatuForm"
    }
  ]
}
```

2. Require package:

```bash
composer require teguh/feature-satu-form:^1.0
```

3. Publish migrations/seeders if needed:

```bash
php artisan vendor:publish --tag=feature-satu-form-migrations
php artisan vendor:publish --tag=feature-satu-form-seeders
```

4. Run migrations:

```bash
php artisan migrate
```

## Release Workflow

### Package Repo

1. Commit changes in this repository.
2. Create tag:

```bash
git tag v1.0.1
git push origin main --tags
```

### Main App Repo

1. Update package version:

```bash
composer update teguh/feature-satu-form
```

2. Commit `composer.json` and `composer.lock`.

## Notes

- Package auto-discovery provider:
  `Teguh\\FeatureSatuForm\\FeatureSatuFormServiceProvider`
- Keep package changes in this repository only (do not edit `vendor/teguh/feature-satu-form` directly).

## Developer Workflow (Package-Only Access)

If a developer only has access to this package repository (without the confidential main app), use the local example playground:

1. Open folder `example-app`.
2. Run `setup.ps1`.
3. Edit package code in this repository.
4. Validate package behavior using `example-app/app`.
5. Commit and open PR in this package repo.

See full guide in `example-app/README.md`.
