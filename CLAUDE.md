# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A standalone WordPress plugin (`uk-interactive-map`) that renders an inline SVG map of the United Kingdom with 227 clickable regions. Each region can hold multiple **projects** (image, title, description, location, URL). A permanent side panel lists all projects with a search bar. Regions with located projects show a marker pin with a project count. No third-party JS or CSS libraries. Install by copying the folder into `wp-content/plugins/`, activate, then drop `[uk_interactive_map]` in content.

## Plugin structure

```
uk-interactive-map.php          ← plugin header + bootstrap (constants, requires, hooks)
assets/
  uk-map.svg                    ← inline SVG; regions are <path id="GBXXX" name="..."> inside <g id="features">
  css/uk-map.css                ← all visual styles (layout, markers, sidebar, cards, tooltip, admin)
  js/uk-map.js                  ← all front-end interaction logic (IIFE, no dependencies)
includes/
  class-uk-map-data.php         ← defaults() for 227 GB region codes; slugs(), region_names(), merge_with_defaults()
  class-uk-map-shortcode.php    ← [uk_interactive_map] shortcode, asset enqueuing, SVG inline + wp_kses
  class-uk-map-admin.php        ← Settings > UK Map admin page, repeater editor, AJAX save handlers
```

## Region codes

Regions use **ISO 3166-2:GB codes** (e.g. `GBABD`, `GBBIR`, `GBLND`). These are the `id` attributes on `<path>` elements inside `<g id="features">` in `uk-map.svg`. There are 227 regions total.

## Region data structure

Each region stored in the `ukm_region_data` WordPress option:

```php
'GBABD' => [
    'name'        => 'Aberdeenshire',
    'color'       => '',          // hex or '' for map default
    'hover_color' => '',
    'projects'    => [            // repeater — zero or more entries
        [
            'title'       => '',
            'description' => '',
            'image'       => '',  // URL
            'location'    => '',  // plain text; non-empty triggers a map marker
            'url'         => '',
        ],
    ],
]
```

A separate `ukm_settings` option holds global marker settings:
```php
[ 'marker_icon' => '', 'marker_color' => '#e74c3c' ]
```

## Data flow

1. On activation, `UK_Map_Data::defaults()` is written to `ukm_region_data`.
2. Admin editor reads/writes `ukm_region_data` per-region via AJAX (`ukm_save_region`). Global marker settings save via `ukm_save_settings`.
3. Shortcode passes both options to JS via `wp_localize_script` as `window.ukmData` (`regions`, `markerIcon`, `markerColor`).
4. JS binds `#features path` elements by `path.id`, looks up `ukmData.regions[id]`.

## Adding or editing a region

- Add `<path id="GBNEW" name="New Region" …>` inside `<g id="features">` in `assets/uk-map.svg`.
- Add the code + default object to `UK_Map_Data::defaults()`.
- No other files need changing.

## Front-end layout

The shortcode outputs a **70 % map / 30 % sidebar** flex layout:

```html
<div class="ukm-wrap">
  <div class="ukm-layout">
    <div class="ukm-map-area">   <!-- SVG here -->   </div>
    <div class="ukm-side-panel"> <!-- always visible --> </div>
  </div>
</div>
```

Collapses to single column at ≤ 900 px.

## Markers

`placeMarkers()` in `uk-map.js` iterates `ukmData.regions` and, for every region with ≥ 1 project whose `location` is non-empty, appends a `<g class="ukm-marker-group" data-rid="GBXXX">` to a `<g id="ukm-markers">` layer at the end of the SVG. Position is the `getBBox()` centre of the region path.

- **Default pin**: teardrop SVG `<path>` (fill = `markerColor`) + `<text>` count inside.
- **Custom icon**: `<image href="...">` + small `<circle>` badge with count. Set in admin under **Marker Settings**.

## Side panel

- Always visible; populated on `DOMContentLoaded`.
- Default state: lists **all projects** from all regions sorted by region name, with a count badge.
- Clicking a region or its marker **filters** the list to that region's projects.
- A `← All` button resets the filter.
- A search `<input>` filters live across title, description, location, and region name.

## JavaScript conventions (`assets/js/uk-map.js`)

- IIFE, no globals except reading `window.ukmData`.
- `escHtml()` / `escAttr()` used for every user-supplied string inserted into DOM.
- `setActive(id)` toggles `.ukm-active` on the path and `.ukm-marker--active` on the marker group; pass `null` to clear.
- `renderSidebar()` is the single function that redraws the panel based on current `activeId` and `searchTerm`.
- `placeMarkers(svg, data, markerIcon, markerColor, onActivate)` — `onActivate(id)` callback is `handleActivate` from `initMap`.

## Admin

- **Marker Settings** section (top): uploads a custom marker icon (WP media picker) and sets the default pin colour. Saves via `ukm_save_settings` AJAX action.
- **Region editor**: two-column layout — searchable list of 227 regions (left, 240 px) + JS-rendered form (right). Region list items show a blue project-count badge.
- **Projects repeater**: each region form has an "Add Project" button that appends a row with title, description, image (media picker), location, and URL fields. Rows can be removed individually.
- Saves are per-region via `ukm_save_region` AJAX to avoid PHP `max_input_vars` limits (227 regions × many fields).

## PHP conventions

- Minimum PHP 7.4 (typed return types, short arrays).
- All output uses `esc_html()`, `esc_attr()`, `esc_url()`, `esc_textarea()` — no raw echoes.
- SVG loaded with `file_get_contents` then sanitised via `wp_kses` with the allowlist in `UK_Map_Shortcode::allowed_svg_tags()`.
- Admin saves use `check_ajax_referer` + `current_user_can('manage_options')` before any write.
