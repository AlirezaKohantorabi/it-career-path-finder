<div dir="rtl">

# دستیار انتخاب مسیر شغلی IT (افزونه وردپرس)

یک افزونه متن‌باز وردپرس که با چند سؤال ساده، شغل‌های مناسب دنیای IT را بر اساس علاقه‌ها، شرایط زندگی و واقعیت بازار کار ایران به کاربر پیشنهاد می‌دهد. ابزار را با یک شورت‌کد در هر برگه یا نوشته‌ای قرار می‌دهید.

```
[it_career_path_finder]
```

## چه کار می‌کند؟

- ۱۹ سؤال کوتاه و دوستانه (حدود ۳ دقیقه) در ۵ بخش.
- مقایسه جواب‌ها با ۸۷ شغل IT و نمایش:
  - شغل‌های پیشنهادی (همراه درصد تطبیق)،
  - شغل‌هایی که با کمی کوتاه آمدن قابل بررسی‌اند،
  - شغل‌هایی که با شرایط فعلی پیشنهاد نمی‌شوند.
- اول هر کارت شغل، **به زبان خیلی ساده و غیرفنی** توضیح می‌دهد آن شغل اصلاً چیست و چه کار می‌کند.
- دلیل پیشنهاد، ریسک‌ها، مهارت‌های لازم، محیط‌های کاری در ایران، برنامه عملی ۳۰ روزه و یک آزمایش کم‌هزینه برای هر شغل.
- تشخیص تناقض در جواب‌ها (مثلاً «فریلنسری» + «بیمه حتمی») و دکمه‌هایی برای امتحان کردن شرط‌های دیگر.
- کپی، اشتراک‌گذاری و چاپ/PDF نتیجه.
- مدیریت کامل شغل‌ها از پیشخوان وردپرس: افزودن، ویرایش، حذف، بازگردانی، درون‌ریزی و برون‌بری JSON.
- فونت ابزار همان فونت قالب سایت است.
- پاسخ‌های کاربر فقط در مرورگر خودش می‌ماند و به سرور فرستاده نمی‌شود.

## نصب سریع

۱. فایل `it-career-path-finder-x.y.z.zip` را از بخش [Releases](https://github.com/AlirezaKohantorabi/it-career-path-finder/releases) دانلود کنید.
۲. در پیشخوان وردپرس: **افزونه‌ها ← افزودن ← بارگذاری افزونه**، فایل zip را انتخاب و نصب کنید، بعد **فعال‌سازی** را بزنید.
۳. شورت‌کد `[it_career_path_finder]` را در یک برگه قرار دهید (در گوتنبرگ با بلوک «کد کوتاه»).

راهنمای کامل نصب، راه‌اندازی و رفع اشکال: **[docs/fa/SETUP.fa.md](docs/fa/SETUP.fa.md)**

## راهنماهای فارسی

| راهنما | برای چه کسی |
|---|---|
| [نصب و راه‌اندازی](docs/fa/SETUP.fa.md) | مدیر سایت |
| [اضافه/ویرایش شغل و تغییر سطح‌ها در فایل‌های JSON](docs/fa/DATA.fa.md) | هر کسی که محتوای ابزار را تغییر می‌دهد |
| [توسعه، تست و انتشار نسخه جدید](docs/fa/DEVELOPMENT.fa.md) | برنامه‌نویس‌ها |

## سازندگان

- **خسرو عظیمی** — [khosro-azimi.ir](https://khosro-azimi.ir) — [github.com/khosro-web](https://github.com/khosro-web)
- **علیرضا کهن‌ترابی** — [github.com/AlirezaKohantorabi](https://github.com/AlirezaKohantorabi)

مجوز: [GPL-2.0-or-later](LICENSE)

</div>

---

## English summary

**IT Career Path Finder** is an open-source WordPress plugin that recommends IT careers for people in Iran. Visitors answer 19 short questions; the plugin compares the answers with 87 IT roles and shows recommended roles, roles that need a compromise, and roles that don't fit right now. Every role card starts with a plain, non-technical explanation of what the job is.

- Embed anywhere with the shortcode `[it_career_path_finder]` (optional `title` and `subtitle` attributes). Multiple instances per page are supported.
- Manage roles in **wp-admin → مسیر شغلی IT**: add, edit, delete, restore, import/export JSON.
- All content (questions, roles, scoring weights, interface text) lives in separate JSON files in [`data/`](data).
- Matching runs entirely in the visitor's browser; answers are never sent to the server.
- Uses the theme's font. Persian, right-to-left, mobile-friendly, printable.

### Requirements

WordPress 5.8+ (tested on 7.1), PHP 7.4+.

### Documentation

| Document | What it covers |
|---|---|
| [docs/PROJECT.md](docs/PROJECT.md) | Architecture, folder map, how the pieces talk to each other, hooks |
| [docs/code/README.md](docs/code/README.md) | One page per code file: every function, its purpose, its relations |
| [docs/DATA.md](docs/DATA.md) | JSON file formats |
| [docs/SECURITY.md](docs/SECURITY.md) | Security model |
| [docs/TESTING.md](docs/TESTING.md) | Running the PHP and JavaScript tests, local WordPress, manual checklist |
| [docs/DECISIONS.md](docs/DECISIONS.md) | Every design decision and why |
| [docs/LESSONS.md](docs/LESSONS.md) | Mistakes, bugs found, and what we learned |
| [CHANGELOG.md](CHANGELOG.md) | What changed in each version and why |
| [CONTRIBUTING.md](CONTRIBUTING.md) | Coding rules and the release checklist |

### Quick development commands

```bash
php tests/run.php
```

```bash
php tools/build-zip.php
```

### Authors

Khosro Azimi ([khosro-azimi.ir](https://khosro-azimi.ir), [@khosro-web](https://github.com/khosro-web)) and Alireza Kohantorabi ([@AlirezaKohantorabi](https://github.com/AlirezaKohantorabi)).

License: GPL-2.0-or-later.
