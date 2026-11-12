# Observatorium

The public website for Observatorium, a studio for public art and place-making. Visitors browse a gallery of projects, then open a project, a news item, a blog post, or an about page. Editors manage that content in a Filament admin.

The application is a Laravel 13 site for PHP 8.5. It replaces the previous hash-routed site and keeps the same pictures, text, and addresses for existing content.

## Public pages

| Page | Address |
| --- | --- |
| Gallery | `/` |
| Projects | `/projects` |
| A project | `/projects/{id}` |
| News | `/news` |
| A news item | `/news/{id}` |
| Blog | `/blog` |
| A blog post | `/blog/{id}` |
| About | `/about` |
| Newsletter | `/newsletter` |

The gallery leads with the projects chosen on the Homepage screen, then the rest of the archive. A project page shows the pictures first. The title, year, place, introduction, and the two text columns sit under the pictures. `<` and `>` step through the images, and clicking the picture moves to the next one.

News and blog posts show the title, date, subtitle, and body, with a link to more images when a post has several. About pages show one text column and any attached PDFs. The newsletter page embeds the existing subscription form.

Records keep their original ids. `/projects/3627` is the same project as the old site, and the same is true for news, blog, and about pages. Old addresses such as `#/project_item_image/3627/0` and `#/news_item/3614` redirect to the new pages. An old project text link opens the project page, because the text is already shown there.

## Admin

Editors sign in at `/admin`.

- **Homepage** sets the mission statement shown on the gallery and the order of the featured projects.
- **Projects** edits the title, place, year, introduction, body, second column, images, and PDFs.
- **News**, **Blog**, and **About** edit their own pages, images, and PDFs.

Images in the editor are shown with FilePond. Existing files stay where they are. A newly uploaded file is stored under `storage/app/public/uploads` and served from `/storage/uploads/`.

Create the first editor with:

```bash
php artisan make:filament-user
```

## Content and files

The database is MySQL, database `observatorium` on `127.0.0.1` port `3306`. Connection settings live in `.env` (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). The old `observat_cms` database is left as it was. The Laravel tables hold the site content: projects, news, blog, about pages, media, and the homepage settings.

Imported records originally came from `../obervatorium/site/data/data.json`.

Pictures and PDFs from the old site stay in `../obervatorium/files`. `public/files` points at that folder, so a path such as `99/3630_DenBosch_opening.jpg` is served from `/files/99/3630_DenBosch_opening.jpg`. The site CSS, graphics, and webfonts are served from `public/css`, `public/graphics`, and `public/webfonts`.

Import or re-import the export with:

```bash
php artisan observatorium:import
```

Pass a path if the JSON file is somewhere else. The command updates records by their original id and replaces each record’s images and PDFs from the export.

## Run it locally

From the project directory, with PHP 8.5. This path does not use `proc_open`:

```bash
composer install
php scripts/install.php
```

`composer install` only refreshes PHP packages. `php scripts/install.php` creates `.env` when it is missing, sets `APP_KEY` when it is empty, publishes the Filament assets, migrates the database, and links `public/storage`.

Create the first editor in the same run:

```bash
php scripts/install.php --name="Editor" --email="editor@example.com" --password="change-me"
```

Point the web server document root at `public`. The site is then at `/` and the admin at `/admin`.

`php artisan dev`, `php artisan serve`, and `php artisan test` still need `proc_open`. Import content with `php artisan observatorium:import` once the database settings in `.env` are in place. The vendor directory has to be present first. Composer can create it when the zip extension is enabled. Otherwise copy `vendor` from a machine where the dependencies are already installed.

`public/files` must point at `../obervatorium/files`. On Windows that is a directory junction:

```powershell
New-Item -ItemType Junction -Path public\files -Target ..\obervatorium\files
```

Sessions and the cache use files, not the database. That avoids a PHP 8.5 incompatibility in Symfony Finder while sessions are being started. `composer install` and `composer update` reapply `scripts/patch-php85-finder.php` for the same reason.
