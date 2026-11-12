function redirectLegacyHash() {
    var hash = window.location.hash;
    if (!hash) {
        return;
    }
    var rules = [
        [/^#\/(?:home|galery)$/, '/'],
        [/^#\/project_list$/, '/projects'],
        [/^#\/project_preview_item\/(\d+)/, '/projects/$1/preview'],
        [/^#\/project_item_image\/(\d+)\/(\d+)/, '/projects/$1?image=$2'],
        [/^#\/project_item_text\/(\d+)/, '/projects/$1'],
        [/^#\/news_list$/, '/news'],
        [/^#\/news_item_image\/(\d+)\/(\d+)/, '/news/$1/images?image=$2'],
        [/^#\/news_item(?:_mobile)?\/(\d+)/, '/news/$1'],
        [/^#\/blog_list$/, '/blog'],
        [/^#\/blog_item(?:_mobile)?\/(\d+)/, '/blog/$1'],
        [/^#\/about$/, '/about'],
        [/^#\/observatorium_item(?:_mobile)?\/(\d+)/, '/about/$1']
    ];
    for (var i = 0; i < rules.length; i++) {
        var match = hash.match(rules[i][0]);
        if (!match) {
            continue;
        }
        window.location.replace(rules[i][1].replace('$1', match[1] || '').replace('$2', match[2] || ''));
        return;
    }
}

redirectLegacyHash();
window.addEventListener('hashchange', redirectLegacyHash);

function toggleMenu() {
    var menu = document.querySelector('.main_nav_hamburger');
    if (!menu) {
        return;
    }
    menu.hidden = !menu.hidden;
}

document.addEventListener('DOMContentLoaded', function () {
    var button = document.getElementById('menu_toggle');
    if (button) {
        button.addEventListener('click', toggleMenu);
    }

    initProjectSlideshow();

    document.querySelectorAll('.galery_image_container').forEach(function (tile) {
        tile.addEventListener('mouseover', function () {
            var info = tile.querySelector('.galery_thumb_info');
            if (info) {
                info.style.display = 'block';
            }
        });
        tile.addEventListener('mouseout', function () {
            var info = tile.querySelector('.galery_thumb_info');
            if (info) {
                info.style.display = 'none';
            }
        });
    });
});

function initProjectSlideshow() {
    var slideshow = document.querySelector('.project_slideshow');
    if (!slideshow) {
        return;
    }

    var track = slideshow.querySelector('.project_slideshow_track');
    var originals = Array.prototype.slice.call(slideshow.querySelectorAll('.project_slide'));
    var count = originals.length;
    var index = parseInt(slideshow.getAttribute('data-index'), 10) || 0;
    var position = index;
    var busy = false;
    var counter = document.getElementById('prj_slides_fromto');
    var previous = document.getElementById('arrow_prj_prev_img');
    var next = document.getElementById('arrow_prj_next_img');

    if (!count) {
        return;
    }

    if (count > 1) {
        track.insertBefore(originals[count - 1].cloneNode(true), originals[0]);
        track.appendChild(originals[0].cloneNode(true));
        position = index + 1;
    }

    var slides = slideshow.querySelectorAll('.project_slide');

    function updateChrome() {
        if (counter) {
            counter.textContent = (index + 1) + ' / ' + count;
        }

        var url = new URL(window.location.href);
        if (index === 0) {
            url.searchParams.delete('image');
        } else {
            url.searchParams.set('image', String(index));
        }
        history.replaceState({ image: index }, '', url);
    }

    function layout(animate) {
        var width = slideshow.clientWidth;

        slides.forEach(function (slide) {
            slide.style.width = width + 'px';
        });

        track.style.transition = animate ? 'transform 0.45s ease' : 'none';
        track.style.transform = 'translate3d(' + (-position * width) + 'px, 0, 0)';

        if (!animate) {
            track.offsetHeight;
        }
    }

    function settle() {
        if (count < 2) {
            busy = false;
            return;
        }

        if (position === count + 1) {
            position = 1;
        } else if (position === 0) {
            position = count;
        } else {
            busy = false;
            return;
        }

        layout(false);
        busy = false;
    }

    function step(direction, event) {
        if (event) {
            event.preventDefault();
        }

        if (busy || count < 2) {
            return;
        }

        busy = true;
        index = (index + direction + count) % count;
        position += direction;
        updateChrome();
        layout(true);
    }

    track.addEventListener('transitionend', function (event) {
        if (event.target !== track || event.propertyName !== 'transform') {
            return;
        }
        settle();
    });

    updateChrome();
    layout(false);
    window.addEventListener('resize', function () {
        position = count > 1 ? index + 1 : index;
        busy = false;
        layout(false);
    });

    if (previous) {
        previous.addEventListener('click', function (event) {
            step(-1, event);
        });
    }

    if (next) {
        next.addEventListener('click', function (event) {
            step(1, event);
        });
    }

    slideshow.addEventListener('click', function (event) {
        if (event.target.closest('.viewer_image')) {
            step(1, event);
        }
    });
}
