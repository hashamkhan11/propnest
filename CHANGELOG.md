# Changelog

All notable changes to this project are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added
- Live demo at [propnest.gencodix.com](https://propnest.gencodix.com).
- Custom domain steps in the deployment guide.

## [1.0.0] - 2026-10-04

First public release.

### Added
- Property search with filters, map view with clustered pins, radius and polygon search.
- Favorites, saved searches with email alerts and side-by-side comparison for buyers.
- Agent listings with queued image processing, inquiry inbox, public profiles and directory.
- Stripe Checkout for agent subscriptions and featured listings, confirmed by webhooks.
- Prorated refund requests for featured listings, processed through the Stripe Refunds API.
- Admin console: moderation, agent verification, users, payments, refunds, pricing, settings and analytics.
- Google sign-in.
- Production-safe base seeder and a deterministic demo marketplace (`DemoSeeder`).
- Docker image, Docker Compose file and Render blueprint for a self-resetting live demo.
- CI with tests, Laravel Pint, Larastan (PHPStan level 5) and a Docker smoke test.

### Security
- Role checks are re-applied to Livewire actions, not only to page routes.
- Suspended users are signed out on their next request.
- Rate limits on the contact and newsletter forms.
- Agents can only cancel their own pending checkouts.

### Fixed
- Super Admins could not open the listing editor.
- Stripe charges now use the site currency setting.
- The analytics revenue chart showed cents instead of dollars.
- Seeding failed on installs without dev dependencies.

[1.0.0]: https://github.com/hashamkhan11/propnest/releases/tag/v1.0.0
