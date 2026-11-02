Bricks JSON Loop Skeleton

A minimal custom element for testing query filters. It runs a Bricks post query but prints no item per post: one JSON block is output and a plain list of titles is drawn from it in the browser, the way a calendar, map or chart would build its display.

To test:
1. Add the "JSON Loop" element to a page, turn on Query loop and choose a post type (posts per page -1 to show all).
2. Add a Select or Search filter with its target query set to that element.
3. On a live site, filtering updates the list (bricksDisplayQueryResult is wrapped in json-loop.js).
