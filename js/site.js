$(document).ready(function () {

    function makeDescriptionUrlLinks(textNode) {
        var text = textNode.nodeValue;
        var urlPattern = /\b(?:https?:\/\/|www\.)[^\s<>"']+/gi;
        var match;
        var lastIndex = 0;
        var fragment = document.createDocumentFragment();
        var foundUrl = false;

        while ((match = urlPattern.exec(text)) !== null) {
            var fullMatch = match[0];
            var linkText = fullMatch;
            var trailingPunctuation = "";

            while (/[.,!?;:)\]}]$/.test(linkText)) {
                trailingPunctuation = linkText.slice(-1) + trailingPunctuation;
                linkText = linkText.slice(0, -1);
            }

            if (linkText === "") continue;

            fragment.appendChild(document.createTextNode(text.slice(lastIndex, match.index)));

            var link = document.createElement("a");
            link.href = /^www\./i.test(linkText) ? "https://" + linkText : linkText;
            link.target = "_blank";
            link.rel = "noopener noreferrer";
            link.textContent = linkText;
            fragment.appendChild(link);

            if (trailingPunctuation !== "") {
                fragment.appendChild(document.createTextNode(trailingPunctuation));
            }

            lastIndex = match.index + fullMatch.length;
            foundUrl = true;
        }

        if (!foundUrl) return;

        fragment.appendChild(document.createTextNode(text.slice(lastIndex)));
        textNode.parentNode.replaceChild(fragment, textNode);
    }

    document.querySelectorAll(".description-modal .description, .event-description").forEach(function (description) {
        description.querySelectorAll("a[href]").forEach(function (link) {
            var linkUrl = new URL(link.href, window.location.href);
            if (linkUrl.protocol === "http:" || linkUrl.protocol === "https:") {
                link.target = "_blank";
                link.rel = "noopener noreferrer";
            }
        });

        var textWalker = document.createTreeWalker(description, NodeFilter.SHOW_TEXT, {
            acceptNode: function (textNode) {
                if (!/\b(?:https?:\/\/|www\.)/i.test(textNode.nodeValue)) {
                    return NodeFilter.FILTER_REJECT;
                }
                if (textNode.parentElement && textNode.parentElement.closest("a, script, style")) {
                    return NodeFilter.FILTER_REJECT;
                }
                return NodeFilter.FILTER_ACCEPT;
            }
        });
        var textNodes = [];
        while (textWalker.nextNode()) {
            textNodes.push(textWalker.currentNode);
        }
        textNodes.forEach(makeDescriptionUrlLinks);
    });

    // More Button toggles description
    $('.more-button').on('click', function () {
        $(this).parent().parent().find('.description-container').toggle();
    });
    $('.close').on('click', function () {
        $(this).parent().parent().parent().toggle();
    });
    $('.button-close').on('click', function () {
        $(this).parent().parent().parent().toggle();
    });

});

const isIOSWebApp =
    ('standalone' in window.navigator) && window.navigator.standalone ||
    window.matchMedia('(display-mode: standalone)').matches;

if (isIOSWebApp) {
    document.documentElement.classList.add('is-webapp');
}

// Add additional recipient fields on the booking page
$(document).on('click', '#add-email', function () {
    var row = $('<p class="email-row"></p>');
    row.append('<input type="email" name="extra_emails[]" placeholder="Weitere Email-Adresse" />');
    row.append($('<button type="button" class="remove-email-button" title="Empfänger entfernen" aria-label="Empfänger entfernen">&minus;</button>'));
    $('#email-list').append(row);
    row.find('input').trigger('focus');
});

$(document).on('click', '.remove-email-button', function () {
    $(this).closest('.email-row').remove();
});


// Fade out the success banner after 5 seconds and drop the notice from the URL
$(function () {
    var banner = $('.notice-flash');
    if (!banner.length) return;

    var url = new URL(window.location.href);
    if (url.searchParams.has('notice')) {
        url.searchParams.delete('notice');
        window.history.replaceState(null, '', url.toString());
    }

    setTimeout(function () {
        banner.addClass('hide');
        setTimeout(function () { banner.remove(); }, 500);
    }, 5000);
});
