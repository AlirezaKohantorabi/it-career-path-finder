=== IT Career Path Finder ===
Contributors: khosroazimi, alirezakohantorabi
Tags: career, quiz, shortcode, persian, rtl
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A Persian IT career path finder for Iran. Visitors answer 19 short questions and get matching IT careers, explained in plain words.

== Description ==

دستیار انتخاب مسیر شغلی IT در ایران. کاربر به ۱۹ سؤال کوتاه جواب می‌دهد و افزونه از بین ۸۷ شغل IT، مناسب‌ترین‌ها را همراه دلیل، ریسک، برنامه ۳۰ روزه و توضیح ساده و غیرفنی پیشنهاد می‌دهد.

* Shortcode: `[it_career_path_finder]` with optional `title` and `subtitle` attributes.
* Manage roles from wp-admin: add, edit, delete, restore, import and export JSON.
* All content lives in JSON files inside the `data` folder.
* Matching runs in the visitor's browser. Answers are never sent to the server.
* Uses the theme's font. Right-to-left, mobile-friendly and printable.

Authors: Khosro Azimi (https://khosro-azimi.ir) and Alireza Kohantorabi.
Source code and documentation: https://github.com/AlirezaKohantorabi/it-career-path-finder

== Installation ==

1. In wp-admin go to Plugins → Add New → Upload Plugin and upload the zip file.
2. Activate the plugin.
3. Put `[it_career_path_finder]` in any page or post (use the Shortcode block in the block editor).

== Frequently Asked Questions ==

= Where are the answers stored? =

Only in the visitor's browser tab (sessionStorage). Nothing is sent to the server.

= How do I add or edit a career? =

wp-admin → مسیر شغلی IT → افزودن شغل. You can also export all roles as JSON, edit the file and import it again.

= The tool shows a loading message forever =

An optimization plugin may be blocking or delaying the plugin's scripts. Exclude the `it-career-path-finder` scripts from "delay JavaScript" or "combine JavaScript" options. See docs/fa/SETUP.fa.md on GitHub.

== Changelog ==

= 1.0.0 =
* First release as a WordPress plugin. See CHANGELOG.md for details.
