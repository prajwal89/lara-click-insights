/**
 * Tracks impressions and clicks on elements with data-clickable attribute
 */
document.addEventListener('DOMContentLoaded', function () {
    // Initialize the ClickTracker
    const tracker = new ClickTracker();
    tracker.initialize();
});

class ClickTracker {
    constructor() {
        this.currentScript = document.getElementById('lara-click-insights');
        this.config = JSON.parse(this.currentScript.dataset.config);
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Tracking arrays
        this.clickableElements = []; // All clickable elements being tracked
        this.viewedElements = []; // Elements that have been viewed
        this.recordedImpressionElements = []; // Elements with recorded impressions

        // Create observer
        this.intersectionObserver = new IntersectionObserver(
            (entries) => this.handleIntersection(entries),
            { root: null, threshold: this.config.intersection_threshold }
        );

        // Set up continuous monitoring for new elements
        this.mutationObserver = new MutationObserver(() => this.checkForNewElements());
    }

    /**
     * Initialize the tracking system
     */
    initialize() {
        // Find and track initial elements
        this.findAndTrackClickableElements();

        // Set up impression recording interval
        setInterval(() => this.recordImpressions(), this.config.polling_delay_in_sec * 1000);

        // Start monitoring DOM changes for new elements
        this.startMutationObserver();

        // Expose public method for manual sync
        window.syncNewlyAddedClickables = () => this.findAndTrackClickableElements();
    }

    /**
     * Find all clickable elements and set up tracking
     */
    findAndTrackClickableElements() {
        const allClickableElements = Array.from(document.querySelectorAll('[data-clickable]'));

        // Filter to only get new elements not already being tracked
        const newElements = allClickableElements.filter(element =>
            !this.clickableElements.includes(element)
        );

        if (newElements.length > 0) {
            // Add new elements to tracking array
            this.clickableElements.push(...newElements);

            // Set up tracking for each new element
            this.observeAndListenToElements(newElements);
        }
    }

    /**
     * Start observing elements and attach click listeners
     */
    observeAndListenToElements(elements) {
        elements.forEach(element => {
            // Observe for visibility
            this.intersectionObserver.observe(element);

            // Listen for clicks
            element.addEventListener('click', (event) => this.handleClick(event));
        });
    }

    /**
     * Handle intersection events when elements become visible
     */
    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                this.viewedElements.push(entry.target);
                this.intersectionObserver.unobserve(entry.target);
            }
        });
    }

    /**
     * Handle click events on tracked elements
     */
    handleClick(event) {
        // Enable open in new tab
        if (event.button !== 0 || event.ctrlKey || event.metaKey) {
            return;
        }

        event.preventDefault();

        const clickedLink = event.target.tagName === 'A'
            ? event.target
            : this.findParentLinkNode(event.target);

        if (!clickedLink) {
            return;
        }

        this.recordClick(clickedLink);
        window.location.href = clickedLink.href;
    }

    /**
     * Find parent <a> element from a child element
     */
    findParentLinkNode(node) {
        if (!node || node === document.documentElement) {
            return null;
        }

        if (node.tagName === 'A') {
            return node;
        }

        return this.findParentLinkNode(node.parentNode);
    }

    /**
     * Get elements that need impression tracking
     */
    getElementsForImpressionTracking() {
        // Get elements viewed but not yet recorded
        const elementsToTrack = this.viewedElements.filter(element =>
            !this.recordedImpressionElements.includes(element)
        );

        // Get unique clickable values
        const uniqueClickables = Array.from(
            new Set(elementsToTrack.map(element => element.dataset.clickable))
        );

        return {
            elements: elementsToTrack,
            clickables: uniqueClickables
        };
    }

    /**
     * Record impressions for viewed elements
     */
    recordImpressions() {
        const { elements, clickables } = this.getElementsForImpressionTracking();

        if (clickables.length === 0) {
            return; // No new impressions to record
        }

        this.sendTracking({
            clickables: clickables
        });

        // Mark these elements as recorded
        this.recordedImpressionElements.push(...elements);
    }

    /**
     * Record a click on an element
     */
    recordClick(clickedElement) {
        const { elements, clickables } = this.getElementsForImpressionTracking();

        this.sendTracking({
            clickables: clickables,
            clicked_on: clickedElement.dataset.clickable
        });

        // Mark viewed elements as recorded
        this.recordedImpressionElements.push(...elements);
    }

    /**
     * Send tracking data to the server
     */
    sendTracking(data) {
        fetch(this.config.endpoint, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            }
        });
    }

    /**
     * Start the mutation observer to watch for DOM changes
     */
    startMutationObserver() {
        this.mutationObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    /**
     * Check for new clickable elements when DOM changes
     */
    checkForNewElements() {
        // Use requestAnimationFrame to avoid performance issues
        // during rapid DOM changes
        requestAnimationFrame(() => {
            this.findAndTrackClickableElements();
        });
    }
}