/**
 * Draws the element's list in the browser from the JSON the PHP printed, and keeps it up to date
 * when a Bricks query filter changes.
 */
(function () {
	// The elements on this page, by query ID.
	var instances = {};

	// Replaces the element's list with the given posts as simple example.
	function draw(element, items) {
		var list = element.querySelector('.json-loop-list');

		list.textContent = '';
		items.forEach(function (item) {
			var li = document.createElement('li');
			li.textContent = item.title;
			list.appendChild(li);
		});
	}

	// Sets up each element on the page. Global because Bricks also calls it in the builder (see $scripts in the PHP).
	window.bricksJsonLoopInit = function () {
		document.querySelectorAll('[data-query-id] > .json-loop-data').forEach(function (data) {
			var element = data.parentElement;
			var queryId = element.getAttribute('data-query-id');

			instances[queryId] = element;
			draw(element, JSON.parse(data.textContent));

			// Registers the element as a query loop, so Bricks filters fetch results for it.
			if (window.bricksData) {
				window.bricksData.queryLoopInstances = window.bricksData.queryLoopInstances || {};
				window.bricksData.queryLoopInstances[queryId] = { element: element, isLiveSearch: false, disableUrlParams: false };
			}
		});
	};

	/*
	 * When a filter changes, Bricks asks WordPress for the filtered results and passes them to
	 * bricksDisplayQueryResult. Normally that function inserts the new loop items in the page, but this element has
	 * none, so it's wrapped to read the results for itself. No Bricks event carries the results, only this function.
	 */
	function wrapBricksFilterResults() {
		if (typeof window.bricksDisplayQueryResult !== 'function') {
			return;
		}

		var original = window.bricksDisplayQueryResult;

		window.bricksDisplayQueryResult = function (queryId, result) {
			var returnValue = original(queryId, result);

			// One of ours: result.html is this element re-rendered with the filtered query, so read its new JSON.
			if (instances[queryId]) {
				var doc = new DOMParser().parseFromString(result.html || '', 'text/html');
				var data = doc.querySelector('.json-loop-data');

				draw(instances[queryId], data ? JSON.parse(data.textContent) : []);
			}

			return returnValue;
		};
	}

	/*
	 * For static exports, this element would need:
	 *
	 * 1. Its posts included in the filter data during the export, even though it prints no item per post.
	 * 2. A JS event after each filter change and on page load, with the query ID and all matching post IDs in its
	 *    details. It would listen for that here and call draw() with only the posts whose IDs match.
	 */

	document.addEventListener('DOMContentLoaded', function () {
		window.bricksJsonLoopInit();
		wrapBricksFilterResults();
	});
})();
