# Laravel Blog Assignment (AI-first Company)

This document describes setup, features, and usage for the interview assignment. It is designed to help reviewers spin up the app quickly and verify requirements.

## Tech Stack
- Laravel 11, PHP 8.3
- Breeze authentication, Tailwind + Vite
- SQLite database (default)
- Database-backed queues

## Quick Start

1) Install dependencies
- Composer: `composer install`
- Node: `npm install`

2) Environment
- Copy `.env.example` to `.env` if needed
- Ensure `DB_CONNECTION=sqlite`
- If using the bundled SQLite file (recommended for reviewers), set `DB_DATABASE=database/database.sqlite`

3) Database
- If the bundled SQLite file is present: you can skip migrations
- If you prefer a fresh database:
  - Create the SQLite file: `touch database/database.sqlite`
  - Run migrations: `php artisan migrate`
  - Seed data: `php artisan db:seed --class=SampleDataSeeder`

4) App key
- `php artisan key:generate`

5) Run the app
- Start server: `php artisan serve`
- Start queue worker (for risk scoring): `php artisan queue:work`

6) Frontend assets
- Using the bundled production build (recommended for reviewers): nothing to do
- If you want to rebuild assets locally:
  - `npm run dev` (for development) or `npm run build` (for production)

## Login Credentials

Seeded accounts (use these to sign in):

- Admin
  - Email: `admin@example.com`
  - Password: `password`
- Moderator
  - Email: `moderator@example.com`
  - Password: `password`

You can also register a new user via the UI.

## URLs

- Home: `/`
- Posts index: `/posts`
- Dashboard: `/dashboard` (requires authentication)
- Dashboard stats: `/dashboard/stats` (requires authentication)

## Bundled Database (Reviewer Convenience)

- A pre-seeded SQLite database file is included. This lets you run the app immediately after `composer install` without running migrations or seeders.
- If you encounter “credentials do not match,” ensure your `.env` points to the correct SQLite file:
  - `DB_CONNECTION=sqlite`
  - `DB_DATABASE=database/database.sqlite`

## Bundled Assets (Reviewer Convenience)

- A pre-built `public/build` is included. You can skip Node/Vite entirely.
- If you prefer to rebuild assets:
  - `npm install`
  - `npm run build` (or `npm run dev` while developing)

## Seeding (If You Use a Fresh DB)

- Run: `php artisan migrate --seed --seeder=SampleDataSeeder`
- The seeder ensures the admin/moderator users exist with the password `password`, and populates posts, comments, and tags.

## Troubleshooting

- Login fails (“These credentials do not match our records.”):
  - Confirm `.env` points to the intended SQLite file
  - If you switched DBs, reseed: `php artisan migrate:fresh --seed --seeder=SampleDataSeeder`
- Risk scoring not updated:
  - Ensure the queue worker is running: `php artisan queue:work`
- Config or route changes not applied:
  - `php artisan config:clear && php artisan cache:clear && php artisan route:clear`

## Routes Overview

Public
- GET `/` — Home
- GET `/posts` — List posts
- GET `/posts/{post}` — View a post

Authenticated
- GET `/dashboard` — Dashboard
- GET `/dashboard/stats` — Stats page

Posts (requires authentication for create/update/delete)
- GET `/posts/create` — Create form
- POST `/posts` — Store post
- GET `/posts/{post}/edit` — Edit form
- PUT/PATCH `/posts/{post}` — Update post
- DELETE `/posts/{post}` — Delete post

Comments (requires authentication)
- POST `/posts/{post}/comments` — Add comment
- GET `/comments/{comment}/edit` — Edit comment form
- PATCH `/comments/{comment}` — Update comment
- DELETE `/comments/{comment}` — Delete comment
- POST `/comments/{comment}/flag` — Flag comment
- DELETE `/comments/{comment}/flag` — Clear flag (moderators/admins)

Profile (requires authentication)
- GET `/profile` — Edit profile
- PATCH `/profile` — Update profile
- DELETE `/profile` — Delete account

Authentication (Breeze)
- GET `/login`, POST `/login`
- GET `/register`, POST `/register`
- GET `/forgot-password`, POST `/forgot-password`
- GET `/reset-password/{token}`, POST `/reset-password`
- GET `/verify-email`, GET `/verify-email/{id}/{hash}`, POST `/email/verification-notification`
- POST `/logout`

2) Environment
- Copy `.env.example` to `.env`
- Ensure `DB_CONNECTION=sqlite`
- Create the SQLite file: `touch database/database.sqlite`

3) App key and migrations
- Generate app key: `php artisan key:generate`
- Run migrations: `php artisan migrate`

4) Seed sample data
- Seed sample users, posts, comments, and tags:
  - `php artisan db:seed --class=SampleDataSeeder`

5) Run the app
- Start dev server: `php artisan serve`
- Start Vite dev: `npm run dev`
- Start queue worker (risk scoring): `php artisan queue:work` (or `php artisan queue:listen`)

You can now visit:
- App: http://127.0.0.1:8000
- Posts index: http://127.0.0.1:8000/posts
- Dashboard (authenticated): http://127.0.0.1:8000/dashboard
- Stats endpoint (authenticated, JSON): http://127.0.0.1:8000/dashboard/stats

## Seeded Accounts

- Admin: admin@example.com
- Moderator: moderator@example.com
- Regular users: generated by seeder

Default password for seeded users is typically "password" (unless changed in your User factory).

## Features Implemented

1) Basic Blog CRUD
- Auth via Breeze
- Posts CRUD (create, read, update, delete)
- Ownership enforced: only the owner can edit/delete
- Validation: title and content required

2) Comments & Relationships
- Comments on posts with create/update/delete
- Only comment owners can delete their comments
- Eloquent relationships: User → Posts, Post → Comments

3) Roles & Authorization
- Roles: Admin, Moderator, User
- Admin/Moderator can moderate content; Admin can delete, Moderator cannot delete others’ posts; regular users manage their own
- Authorization enforced via policies

4) Flags, Tags & Filters
- Flagging for inappropriate comments (users can flag; Admin/Moderator can clear)
- Tagging system for posts (comma-separated input)
- Post filters: search, tag; sorting by created_at/title/risk_score
- Eager loading and basic indexes for performance

5) Analytics & Scheduling
- Dashboard stats endpoint: `/dashboard/stats` (authenticated) returns totals and top users
- Scheduling (archive old posts, daily summary) can be added upon request

6) Risk Assessment & Notification Service
- Asynchronous risk scoring job dispatched on post create/update
- Rules:
  - Contains: accident, fire, theft, damage → +50
  - Short content (<50 chars) → +10
  - Default baseline → +20
- Levels: low (<30), medium (30–69), high (70+)
- High-risk posts produce an admin notification via logging (mail integration can be added)

7) Filtering & Dynamic Reports
- Basic filters included on posts (search, tag, sort, direction)
- Full dynamic reporting (AND/OR logic, exports, advanced aggregations) can be implemented as a follow-up

## Endpoints Overview

- Auth (Breeze): register, login, password reset, email verification
- Posts:
  - CRUD: `/posts`, `/posts/{post}`
  - Filters: `?search=...&tag=...&sort=created_at|title|risk_score&direction=asc|desc`
- Comments:
  - Create: `POST /posts/{post}/comments`
  - Edit/Update: `GET /comments/{comment}/edit`, `PATCH /comments/{comment}`
  - Delete: `DELETE /comments/{comment}`
  - Flag/Unflag: `POST /comments/{comment}/flag`, `DELETE /comments/{comment}/flag`
- Stats:
  - `GET /dashboard/stats` (authenticated JSON: totals and top users)

## Roles & Permissions Summary

- Admin
  - Moderate content
  - Delete any post/comment
- Moderator
  - Moderate content
  - Cannot delete others’ posts
- User
  - Manage own posts/comments

## Testing

- Run test suite: `php artisan test`

## Notes

- Queues: ensure the queue worker is running to process risk scoring.
- SQLite is default for simplicity; feel free to switch DB in `.env`.
- Additional items (scheduling with mail summaries, advanced reporting, CSV/Excel export) are available as next steps on request.
