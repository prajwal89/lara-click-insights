// ! impressions count will be missed if user navigate to other page before impressions polling happens
// ! we can fix this with intercepting all "A" tag clicks
// ! but this may lead to unexpected behavior like what if we already have event listener on that element  

document.addEventListener('DOMContentLoaded', function () {
    const config = {
        polling_delay_in_ms: 3000,
        intersection_threshold: 0.5
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // these variable only holds array of Clickable Nodes 
    // That are rendered on FCP
    const clickableElements = Array.from(document.querySelectorAll('[data-clickable]'));

    // viewed Clickable Nodes, this will sent for recording an impression
    var clickableElementsViewed = Array();

    var clickableElementsViewedAndImpressionsAreRecorded = Array();

    const handleIntersection = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                clickableElementsViewed.push(entry.target)
                observer.unobserve(entry.target);
            }
        });
    };

    const intersectionObserver = new IntersectionObserver(handleIntersection, {
        root: null,
        threshold: config.intersection_threshold
    });

    observeAndListen(clickableElements)

    function observeAndListen(allElements) {
        allElements.forEach(element => {
            intersectionObserver.observe(element);
        });

        allElements.forEach(element => {
            element.addEventListener('click', handleClick);
        });
    }

    function handleClick(event) {
        // enable open in new tab
        if (event.button !== 0 || event.ctrlKey || event.metaKey) {
            // todo record click
            return;
        }

        event.preventDefault();

        let currentLink = null;

        // ?should i add ony support for links
        // we do not have to bother with other type of elements
        if (event.target.tagName === 'A') {
            currentLink = event.target
        } else {
            currentLink = findParentLinkNode(event.target)
            if (currentLink == null) {
                console.log('Did not find link upward')
                return;
            }
        }

        console.log(currentLink)

        recordClick(currentLink)

        window.location.href = currentLink.href;
    }

    function getClickableForTrackingImpressions() {
        // discard already tracked elements
        let elementToSendForImpressions = clickableElementsViewed.filter(function (element) {
            return !clickableElementsViewedAndImpressionsAreRecorded.includes(element)
        })

        // get unique clickable data attributes
        let clickAbles = elementToSendForImpressions.map((item) => item.dataset.clickable)

        clickAbles = Array.from(new Set(clickAbles))

        return [
            elementToSendForImpressions, // all remaining elements 
            clickAbles // unique clickables
        ]
    }


    function recordImpressions() {
        let elementToSendForImpressions, clickAbles;

        [elementToSendForImpressions, clickAbles] = getClickableForTrackingImpressions();

        if (clickAbles.length == 0) {
            // Not sending impressions as their are no new impressions
            return;
        }

        // todo this should be dynamic as user can change the route path
        fetch('/lara-click-insights', {
            method: 'POST',
            body: JSON.stringify({
                clickables: clickAbles
            }),
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        clickableElementsViewedAndImpressionsAreRecorded.push(...elementToSendForImpressions);
    }

    function recordClick(currentLink) {
        let elementToSendForImpressions, clickAbles;

        [elementToSendForImpressions, clickAbles] = getClickableForTrackingImpressions();

        // todo add time out of 500ms
        fetch('/lara-click-insights', {
            method: 'POST',
            body: JSON.stringify({
                clickables: clickAbles,
                clicked_on: currentLink.dataset.clickable
            }),
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        clickableElementsViewedAndImpressionsAreRecorded.push(...elementToSendForImpressions);
    }

    function findParentLinkNode(node) {
        if (!node || node === document.documentElement) {
            return null; // Reached the top of the document without finding a link
        }

        if (node.tagName === 'A') {
            return node; // Found a link, return its href attribute
        }

        return findParentLinkNode(node.parentNode); // Recursively check the parent node
    }

    setInterval(recordImpressions, config.polling_delay_in_ms);

    //* observe and listen events on newly added clickables after FCP
    window.syncNewlyAddedClickAbles = function () {
        var allClickAblesWithNewlyRendered = Array.from(document.querySelectorAll('[data-clickable]'));

        var newlyRenderedClickAbles = allClickAblesWithNewlyRendered.filter(function (item) {
            return !clickableElements.includes(item);
        });

        observeAndListen(newlyRenderedClickAbles);
    };

})
