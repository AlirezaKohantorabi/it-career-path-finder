# `assets/js/boot.js` → `ItcpFinder.mountAll`

**Purpose:** the entry point, loaded last. When the page is ready it reads the dataset **once** and starts a [`Finder`](finder.md) for every `[data-itcp-app]` element on the page. If the data is missing or broken, or the markup is incomplete, it shows the "data did not load" message instead of a forever-loading tool. Unexpected errors are shown to the visitor as that message **and** re-thrown asynchronously so they still appear in the browser console for debugging.

## Functions

| Function | What it does |
|---|---|
| `reportError(error)` | Re-throws an error on the next tick so the console shows it. |
| `showDataError(root)` | Adds `is-error`, shows the `data-error` text, hides the loading text. |
| `instanceNumberOf(root, position)` | The `data-itcp-instance` number (from PHP), or the position + 1. |
| `startFinder(root, dataset, position)` | Creates and starts a Finder, catching errors. |
| `mountRoot(root, dataset, position)` | Skips already-started roots; shows the data error when needed; otherwise starts. |
| `mountAll()` | **Public** as `ItcpFinder.mountAll`. Mounts every tool on the page. Page builders that insert the shortcode later can call `window.ItcpFinder.mountAll()` again. |

## Relations

- Uses: [`dataset`](dataset.md), [`finder`](finder.md). Loaded as `itcp-boot` by [`Assets`](../../src/Frontend/Assets.md).
