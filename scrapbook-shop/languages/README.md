Scrapbook Shop Translations

- Text domain: `scrapbook-shop`
- Domain path: `/languages`

Files:
- `scrapbook-shop.pot`: Starter template. Regenerate with WP-CLI:

```sh
wp i18n make-pot . languages/scrapbook-shop.pot --slug=scrapbook-shop
```

To add a language:
1) Copy `scrapbook-shop.pot` to `scrapbook-shop-LOCALE.po` (e.g. `scrapbook-shop-de_DE.po`).
2) Translate strings in the `.po` file with a tool like Poedit.
3) Compile to `.mo` (Poedit does this automatically) and ensure the file is named `scrapbook-shop-LOCALE.mo` in this directory.
4) WordPress will load the `.mo` automatically when the site language matches.
