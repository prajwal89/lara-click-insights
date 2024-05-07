// ! impressions count will be missed if user navigate to other page before impressions polling happens
// ! we can fix this with intercepting all "A" tag clicks
// ! but this may lead to unexpected behavior like what if we already have event listener on that element  

// todo user impressionable instead of clickables
const config = {
    polling_delay_in_ms: 3000,
    intersection_threshold: 0.5
}

// const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
            console.log('Element is now visible:', clickableElementsViewed);
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

    // this will still enable open in new tab 
    // if (event.button !== 0 || event.ctrlKey || event.metaKey) {
    //     // todo record click
    //     return;
    // }

    event.preventDefault();

    alert(event.target.dataset.clickable)

    let currentLink = null;

    // ?should i add ony support for links
    // we do not have to bother with other type of elements
    if (event.target.tagName === 'A') {
        currentLink = event.target
    } else {
        console.log('Clicked element is not link')
        alert('Clicked element is not link')
        return;
    }

    // alert(currentLink)

    recordClick(currentLink)

    // alert(currentLink.href)
    window.location.href = currentLink.href;
}

function getClickableForTrackingImpressions() {
    let elementToSendForImpressions = clickableElementsViewed.filter(function (element) {
        return !clickableElementsViewedAndImpressionsAreRecorded.includes(element)
    })

    // *get unique clickable data attributes
    let clickAbles = elementToSendForImpressions.map((item) => item.dataset.clickable)

    clickAbles = Array.from(new Set(clickAbles))

    return [
        // all remaining elements 
        elementToSendForImpressions,

        // unique clickables
        clickAbles
    ]
}


function recordImpressions() {
    let elementToSendForImpressions, clickAbles;

    [elementToSendForImpressions, clickAbles] = getClickableForTrackingImpressions();

    if (clickAbles.length == 0) {
        // console.log('Not sending impressions as their are no new impressions')
        return;
    }
    
    console.log(clickAbles);

    // todo this should be dynamic as user can change the route path
    fetch('/lara-click-insights', {
        method: 'POST',
        body: JSON.stringify({
            clickables: clickAbles
        }),
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            // 'X-CSRF-TOKEN': csrfToken
        }
    });

    clickableElementsViewedAndImpressionsAreRecorded.push(...elementToSendForImpressions);
}


function recordClick(currentLink) {
    // console.log(currentLink);
    // alert(currentLink.dataset.clickable)

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
            // 'X-CSRF-TOKEN': csrfToken
        }
    });

    clickableElementsViewedAndImpressionsAreRecorded.push(...elementToSendForImpressions);
}


setInterval(recordImpressions, config.polling_delay_in_ms);

//* observe and listen events on newly added clickables after FCP
function syncNewlyAddedClickAbles() {
    var allClickAblesWithNewlyRendered = Array.from(document.querySelectorAll('[data-clickable]'));

    var newlyRenderedClickAbles = allClickAblesWithNewlyRendered.filter(function (item) {
        return !clickableElements.includes(item);
    });

    observeAndListen(newlyRenderedClickAbles)
}
