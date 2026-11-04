<p align="center">
  <img src="docs/demo.gif" alt="Demo: from the landing page to a printable timetable" width="720">
</p>

<h1 align="center">Generátor harmonogramu</h1>

<p align="center"><b>Turn a Google Calendar into a printable, day-by-day camp timetable in one click.</b></p>

<p align="center">
  <a href="https://harmonogram.skauting.cz/">Live app</a> ·
  <a href="https://harmonogram.skauting.cz/sample.php">Sample timetable</a> ·
  <a href="LICENSE">MIT License</a>
</p>

---

## What it does

You plan your camp, course or event in Google Calendar. The app reads the events and prints a clean A4 timetable, one page per day.

- **Sign in with Google**: read-only access to your calendars.
- **Pick a calendar, a date range and a template**: Výchozí (default), VK Ursus or RK Pecka.
- **Smart layout**: box height follows event length, overlapping events sit side by side.
- **Meals are highlighted**: events named *snídaně, svačina, oběd, večeře* get a dark box.
- **Print or save as PDF**: straight from the browser.

No account? Open the [sample timetable](https://harmonogram.skauting.cz/sample.php).

## Quick start

Needs PHP 8.1+ and [Composer](https://getcomposer.org/).

```bash
git clone https://github.com/jankorbay/Timetable-generator.git
cd Timetable-generator
composer setup   # installs dependencies, creates google/config.php
composer start   # serves the app on http://localhost:8000
```

The sample timetable (`/sample.php`) works right away. Google sign-in needs the step below.

## Google Calendar setup

1. In [Google Cloud Console](https://console.cloud.google.com/), enable the **Google Calendar API**.
2. Create an **OAuth client ID** (type: Web application).
3. Add `http://localhost:8000/google/callback.php` as an authorized redirect URI.
4. Fill `client_id`, `client_secret` and `redirect_uri` in `google/config.php`.

`google/config.php` is git-ignored, so your secrets stay local.

## How to fill the calendar

| In Google Calendar | In the timetable |
| --- | --- |
| Event title | Bold heading |
| Each line of the description | One line under the heading |
| Title contains a meal word | Dark "food" box |
| Events at the same time | Shown side by side |

The app also shows this guide with screenshots (click **?** after sign-in).

## Project structure

```
index.php           Landing page, calendar/date/template picker
google_export.php   Fetches events, groups them by day, renders the template
sample.php          Demo timetable with hard-coded events
google/             OAuth flow and Calendar API calls
templates/          Print layouts (default, ursus, pecka)
```

### Add your own template

1. Copy `templates/default.php` to `templates/<name>.php` and change the look.
2. Add `<option value="<name>">` to the template `<select>` in `index.php`.

## License

[MIT](LICENSE) © Jan Korbay
