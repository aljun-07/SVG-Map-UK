# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A standalone WordPress plugin (`uk-interactive-map`) that renders an SVG map of the United Kingdom with clickable/hoverable regions. No third-party JS or CSS libraries — everything is hand-written. Install by copying the whole repo folder into `wp-content/plugins/`, activate, then drop `[uk_interactive_map]` anywhere in content.

## Plugin structure

```
uk-interactive-map.php          ← plugin header + bootstrap (constants, requires, hooks)
assets/
  uk-map.svg                    ← inline SVG map; each region is a <path data-region="slug">
  css/uk-map.css                ← all visual styles (tooltip, modal, popover, dark-mode)
  js/uk-map.js                  ← all interaction logic (no dependencies)
includes/
  class-uk-map-data.php         ← static defaults() and slugs() for the 12 regions
  class-uk-map-shortcode.php    ← [uk_interactive_map] shortcode, enqueues assets
  class-uk-map-admin.php        ← Settings > UK Map admin page, sanitization
```

## Region data flow

1. On activation, `UK_Map_Data::defaults()` is written to `ukm_region_data` option.
2. Admin page reads/writes that option; `sanitize_data()` normalises every field.
3. Shortcode passes the option value to JS via `wp_localize_script` as `window.ukmData.regions`.
4. JS looks up each region by its `data-region` slug from `ukmData.regions`.

## Adding or editing a region

- Add the `<path data-region="new-slug" …>` to `assets/uk-map.svg`.
- Add the slug + default data object to `UK_Map_Data::defaults()`.
- No other files need changing — JS, CSS and admin iterate over whatever is in the data class.

## Interaction modes

The shortcode `mode` attribute controls click behaviour:

| mode      | effect                                              |
|-----------|-----------------------------------------------------|
| `modal`   | full overlay dialog (default)                       |
| `popover` | small card anchored near the clicked region         |
| `tooltip` | hover-only tooltip; clicks do nothing               |

## JavaScript conventions (`assets/js/uk-map.js`)

- IIFE, no globals except reading `window.ukmData`.
- `escHtml()` / `escAttr()` used for all user-supplied strings inserted into the DOM.
- Modal lifecycle: `openModal()` → `closeModal()`. Only one modal exists at a time.
- `setActiveRegion(id)` toggles `.ukm-active` CSS class; pass `null` to clear.

## PHP conventions

- Minimum PHP 7.4 (typed return types, short arrays).
- All output uses `esc_html()`, `esc_attr()`, `esc_url()`, `esc_textarea()` — no raw echoes.
- The SVG file is loaded with `file_get_contents` and then run through `wp_kses` with a strict SVG allowlist defined in `UK_Map_Shortcode::allowed_svg_tags()`.
- Admin form uses `settings_fields()` + `register_setting()` WordPress Settings API.
