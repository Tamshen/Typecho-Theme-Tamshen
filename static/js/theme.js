(function () {
    'use strict';

    if (window.console && window.console.info) {
        window.console.info('TAMSHEN Theme - Design by TAMSHEN - https://github.com/Tamshen');
    }

    var themeScript = document.currentScript;
    var pageLoader = document.querySelector('[data-page-loader]');
    var pageLoaderDismissPending = false;

    function dismissPageLoader() {
        if (!pageLoader || pageLoaderDismissPending || pageLoader.classList.contains('leaving')) return;
        pageLoaderDismissPending = true;
        var now = window.performance ? window.performance.now() : Date.now();
        var startedAt = window.__pageLoaderStartedAt || now;
        var internalNavigation = document.documentElement.classList.contains('internal-navigation');
        var delay = internalNavigation ? 0 : Math.max(0, 360 - (now - startedAt));
        window.setTimeout(function () {
            document.body.classList.add('page-visible');
            pageLoader.classList.add('leaving');
            window.setTimeout(function () {
                document.body.classList.remove('page-entering', 'page-visible');
            }, 420);
        }, delay);
    }

    if (document.readyState !== 'loading') {
        dismissPageLoader();
    } else {
        document.addEventListener('DOMContentLoaded', dismissPageLoader, { once: true });
    }
    window.setTimeout(dismissPageLoader, 1000);

    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        var link = event.target.closest('a[href]');
        if (!link || link.target === '_blank' || link.hasAttribute('download')) return;
        var href = link.getAttribute('href');
        if (!href || href.charAt(0) === '#' || /^(mailto:|tel:|javascript:)/i.test(href)) return;
        var destination;
        try {
            destination = new URL(link.href, window.location.href);
        } catch (error) {
            return;
        }
        if (destination.origin !== window.location.origin) return;
        if (destination.pathname === window.location.pathname
            && destination.search === window.location.search
            && destination.hash) return;
        try {
            window.sessionStorage.setItem('themeInternalNavigation', '1');
        } catch (error) {}
    });

    var useVendorCdn = themeScript && themeScript.getAttribute('data-use-vendor-cdn') === '1';
    var codeHighlightEnabled = themeScript && themeScript.getAttribute('data-enable-code-highlight') === '1';
    var mermaidEnabled = themeScript && themeScript.getAttribute('data-enable-mermaid') === '1';
    var vendorUrls = {
        lenis: useVendorCdn ? 'https://cdn.jsdelivr.net/npm/lenis@1.3.25/dist/lenis.min.js' : themeScript.getAttribute('data-lenis-local'),
        lenisFallback: useVendorCdn ? themeScript.getAttribute('data-lenis-local') : '',
        lenisCss: useVendorCdn ? 'https://cdn.jsdelivr.net/npm/lenis@1.3.25/dist/lenis.css' : themeScript.getAttribute('data-lenis-css-local'),
        lenisCssFallback: useVendorCdn ? themeScript.getAttribute('data-lenis-css-local') : '',
        highlight: useVendorCdn ? 'https://cdn.jsdelivr.net/npm/@highlightjs/cdn-assets@11.11.1/highlight.min.js' : themeScript.getAttribute('data-highlight-local'),
        highlightFallback: useVendorCdn ? themeScript.getAttribute('data-highlight-local') : '',
        mermaid: useVendorCdn ? 'https://cdn.jsdelivr.net/npm/mermaid@11.16.0/dist/mermaid.min.js' : themeScript.getAttribute('data-mermaid-local'),
        mermaidFallback: useVendorCdn ? themeScript.getAttribute('data-mermaid-local') : ''
    };

    function loadOptionalScript(source, fallback, callback) {
        var completed = false;
        var fallbackStarted = false;
        var timeoutId = null;

        function complete() {
            if (completed) return;
            completed = true;
            if (timeoutId) window.clearTimeout(timeoutId);
            callback();
        }

        function load(url, isFallback) {
            if (!url || (isFallback && fallbackStarted)) return;
            if (isFallback) fallbackStarted = true;
            var script = document.createElement('script');
            script.src = url;
            script.async = true;
            script.onload = complete;
            script.onerror = function () {
                if (!isFallback && fallback) load(fallback, true);
            };
            document.head.appendChild(script);
        }

        load(source, false);
        if (fallback) timeoutId = window.setTimeout(function () { load(fallback, true); }, 2500);
    }

    function loadOptionalStylesheet(source, fallback) {
        var completed = false;
        var fallbackStarted = false;
        var timeoutId = null;
        function load(url, isFallback) {
            if (!url || (isFallback && fallbackStarted)) return;
            if (isFallback) fallbackStarted = true;
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = url;
            link.onload = function () {
                if (completed) return;
                completed = true;
                if (timeoutId) window.clearTimeout(timeoutId);
            };
            link.onerror = function () { if (!isFallback && fallback) load(fallback, true); };
            document.head.appendChild(link);
        }
        load(source, false);
        if (fallback) timeoutId = window.setTimeout(function () { load(fallback, true); }, 2500);
    }

    var toggle = document.querySelector('.menu-toggle');
    var nav = document.querySelector('.primary-nav');
    var siteMenu = document.querySelector('.site-menu');
    var menuBackdrop = document.querySelector('.menu-backdrop');
    var searchTrigger = document.querySelector('.search-trigger');
    var searchPanel = document.querySelector('.search-panel');
    var topButton = document.querySelector('.back-to-top');
    var commentsButton = document.querySelector('.jump-to-comments');
    var progress = document.querySelector('.reading-progress span');
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var lenis = null;

    document.querySelectorAll('.archive-layout').forEach(function (layout) {
        if (!layout.querySelector('.site-sidebar')) layout.classList.add('no-sidebar');
    });

    loadOptionalStylesheet(vendorUrls.lenisCss, vendorUrls.lenisCssFallback);
    loadOptionalScript(vendorUrls.lenis, vendorUrls.lenisFallback, function () {
        if (!lenis && window.Lenis && !reducedMotion.matches) lenis = new window.Lenis({ autoRaf: true });
    });

    function scrollToTarget(target, block) {
        if (lenis) {
            var targetTop = target.getBoundingClientRect().top + window.scrollY;
            var offset = block === 'center'
                ? Math.min(0, (window.innerHeight - target.offsetHeight) / -2)
                : -88;
            lenis.scrollTo(targetTop, { offset: offset });
            return;
        }
        target.scrollIntoView({
            behavior: reducedMotion.matches ? 'auto' : 'smooth',
            block: block || 'start'
        });
    }

    document.addEventListener('click', function (event) {
        if (event.defaultPrevented) return;
        var link = event.target.closest('a[href^="#"]');
        if (!link) return;
        var hash = link.getAttribute('href');
        if (!hash || hash === '#') return;
        var target;
        try {
            target = document.getElementById(decodeURIComponent(hash.slice(1)));
        } catch (error) {
            return;
        }
        if (!target) return;
        event.preventDefault();
        scrollToTarget(target, 'start');
        if (window.history && window.history.pushState) {
            window.history.pushState(null, '', hash);
        }
    });

    var pendingCovers = Array.prototype.slice.call(document.querySelectorAll('.card-image.cover-pending'));

    function loadCardCover(card) {
        var url = card.getAttribute('data-cover');
        var cover = new Image();

        function keepPlaceholder() {
            card.classList.remove('cover-pending');
            card.removeAttribute('data-cover');
        }

        cover.addEventListener('load', function () {
            card.style.backgroundImage = 'url(' + JSON.stringify(url) + ')';
            card.classList.remove('is-placeholder', 'cover-pending');
            card.removeAttribute('data-cover');
        });
        cover.addEventListener('error', keepPlaceholder);
        if (url) cover.src = url;
        else keepPlaceholder();
    }

    if (window.matchMedia('(max-width: 768px)').matches) {
        pendingCovers.forEach(function (card) {
            card.classList.remove('cover-pending');
            card.removeAttribute('data-cover');
        });
    } else if ('IntersectionObserver' in window) {
        var coverObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                coverObserver.unobserve(entry.target);
                loadCardCover(entry.target);
            });
        }, { rootMargin: '320px 0px' });
        pendingCovers.forEach(function (card) { coverObserver.observe(card); });
    } else {
        pendingCovers.forEach(loadCardCover);
    }

    if (toggle && nav) {
        function setMenu(open, restoreFocus) {
            toggle.setAttribute('aria-expanded', String(open));
            toggle.setAttribute('aria-label', open ? '关闭导航' : '打开导航');
            nav.classList.toggle('open', open);
            document.body.classList.toggle('menu-open', open);
            if (lenis) {
                if (open) lenis.stop();
                else lenis.start();
            }
            if (!open && restoreFocus) toggle.focus();
        }

        toggle.addEventListener('click', function () {
            setMenu(toggle.getAttribute('aria-expanded') !== 'true', false);
        });
        if (menuBackdrop) menuBackdrop.addEventListener('click', function () { setMenu(false, false); });
        nav.addEventListener('click', function (event) {
            if (event.target.closest('a')) setMenu(false, false);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') setMenu(false, true);
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768 && toggle.getAttribute('aria-expanded') === 'true') setMenu(false, false);
        });
    }

    if (siteMenu) {
        function updateMenuState() { siteMenu.classList.toggle('is-scrolled', window.scrollY > 8); }
        updateMenuState();
        window.addEventListener('scroll', updateMenuState, { passive: true });
    }

    if (searchTrigger && searchPanel) {
        var searchInput = searchPanel.querySelector('input[type="search"]');
        var searchClose = searchPanel.querySelector('.search-close');
        var searchBackdrop = searchPanel.querySelector('.search-backdrop');

        function setSearch(open) {
            searchPanel.hidden = !open;
            searchTrigger.setAttribute('aria-expanded', String(open));
            document.body.classList.toggle('search-open', open);
            if (lenis) {
                if (open) lenis.stop();
                else lenis.start();
            }
            if (open) window.setTimeout(function () { searchInput.focus(); }, 30);
            else searchTrigger.focus();
        }

        searchTrigger.addEventListener('click', function () { setSearch(true); });
        searchClose.addEventListener('click', function () { setSearch(false); });
        searchBackdrop.addEventListener('click', function () { setSearch(false); });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !searchPanel.hidden) setSearch(false);
        });
    }

    document.querySelectorAll('.pagination').forEach(function (pagination) {
        var totalPages = parseInt(pagination.getAttribute('data-total-pages'), 10) || 1;
        if (totalPages < 2) return;

        function pageUrl(page) {
            var links = Array.prototype.slice.call(pagination.querySelectorAll('li a')).filter(function (link) {
                return /^\d+$/.test(link.textContent.trim());
            });
            var source = null;
            links.some(function (link) {
                if (parseInt(link.textContent, 10) > 1) {
                    source = link;
                    return true;
                }
                return false;
            });
            if (!source) return '';

            var sourcePage = parseInt(source.textContent, 10);
            var url = new URL(source.href, window.location.href);
            var matchedParameter = false;
            url.searchParams.forEach(function (value, key) {
                if (parseInt(value, 10) === sourcePage && String(sourcePage) === value) {
                    url.searchParams.set(key, String(page));
                    matchedParameter = true;
                }
            });
            if (matchedParameter) return url.href;
            if (/\/page\/\d+(?=\/|$)/.test(url.pathname)) {
                url.pathname = url.pathname.replace(/\/page\/\d+(?=\/|$)/, '/page/' + page);
                return url.href;
            }
            var segment = new RegExp('/' + sourcePage + '(?=/|$)');
            if (segment.test(url.pathname)) {
                url.pathname = url.pathname.replace(segment, '/' + page);
                return url.href;
            }
            return '';
        }

        pagination.querySelectorAll('li > span').forEach(function (split) {
            if (split.textContent.trim() !== '...') return;
            var item = split.parentElement;
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'pagination-jump-button';
            button.textContent = '...';
            button.setAttribute('aria-label', '输入页码跳转');
            split.replaceWith(button);

            button.addEventListener('click', function () {
                var form = document.createElement('form');
                form.className = 'pagination-jump-form';
                form.innerHTML = '<label class="visually-hidden">跳转页码</label><input type="number" min="1" max="' + totalPages + '" inputmode="numeric" required>';
                item.innerHTML = '';
                item.appendChild(form);
                var input = form.querySelector('input');
                input.focus();
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    var page = Math.min(totalPages, Math.max(1, parseInt(input.value, 10) || 0));
                    var target = pageUrl(page);
                    if (target) window.location.href = target;
                });
            });
        });
    });

    function commentErrorMessage(html, fallback) {
        var documentFromResponse = new DOMParser().parseFromString(html, 'text/html');
        var message = documentFromResponse.querySelector('.message, .typecho-option-message, .notice, main h1, body > h1');
        return message && message.textContent.trim() ? message.textContent.trim() : fallback;
    }

    function bindAjaxComments(root) {
        if (!window.fetch) return;
        var form = root.querySelector('#comment-form');
        if (!form || form.dataset.ajaxBound === 'true') return;

        var commentsRoot = form.closest('#comments');
        var commentModal = commentsRoot.querySelector('[data-comment-modal]');
        var commentDialog = commentModal ? commentModal.querySelector('.comment-modal-dialog') : null;
        var commentModalHeading = commentModal ? commentModal.querySelector('.comment-modal-heading') : null;
        var lastCommentOpener = null;

        function openCommentModal(opener) {
            if (!commentModal) return;
            lastCommentOpener = opener || document.activeElement;
            if (commentDialog) {
                dragX = 0;
                dragY = 0;
                commentDialog.style.transform = '';
            }
            commentModal.hidden = false;
            document.body.classList.add('comment-modal-open');
            window.setTimeout(function () {
                form.querySelector('textarea[name="text"]').focus();
            }, 60);
        }

        if (commentDialog && commentModalHeading) {
            var dragX = 0;
            var dragY = 0;
            var dragStartX = 0;
            var dragStartY = 0;
            var dialogStartRect = null;

            commentModalHeading.addEventListener('pointerdown', function (event) {
                if (window.matchMedia('(max-width: 768px)').matches
                    || event.button !== 0
                    || event.target.closest('button, a, input, textarea')) return;
                dragStartX = event.clientX;
                dragStartY = event.clientY;
                dialogStartRect = commentDialog.getBoundingClientRect();
                commentModalHeading.setPointerCapture(event.pointerId);
                commentDialog.classList.add('is-dragging');
                event.preventDefault();
            });

            commentModalHeading.addEventListener('pointermove', function (event) {
                if (!dialogStartRect || !commentModalHeading.hasPointerCapture(event.pointerId)) return;
                var nextX = dragX + event.clientX - dragStartX;
                var nextY = dragY + event.clientY - dragStartY;
                var margin = 12;
                nextX = Math.max(nextX, margin - dialogStartRect.left + dragX);
                nextX = Math.min(nextX, window.innerWidth - margin - dialogStartRect.right + dragX);
                nextY = Math.max(nextY, margin - dialogStartRect.top + dragY);
                nextY = Math.min(nextY, window.innerHeight - margin - dialogStartRect.bottom + dragY);
                commentDialog.style.transform = 'translate(' + nextX + 'px, ' + nextY + 'px)';
            });

            function finishCommentDrag(event) {
                if (!dialogStartRect) return;
                var transform = commentDialog.style.transform.match(/translate\(([-\d.]+)px, ([-\d.]+)px\)/);
                if (transform) {
                    dragX = parseFloat(transform[1]);
                    dragY = parseFloat(transform[2]);
                }
                dialogStartRect = null;
                commentDialog.classList.remove('is-dragging');
                if (commentModalHeading.hasPointerCapture(event.pointerId)) {
                    commentModalHeading.releasePointerCapture(event.pointerId);
                }
            }

            commentModalHeading.addEventListener('pointerup', finishCommentDrag);
            commentModalHeading.addEventListener('pointercancel', finishCommentDrag);
        }

        function closeCommentModal() {
            if (!commentModal || form.dataset.submitting === 'true') return;
            commentModal.hidden = true;
            document.body.classList.remove('comment-modal-open');
            if (lastCommentOpener && lastCommentOpener.isConnected) lastCommentOpener.focus();
        }

        function clearReply() {
            var parentInput = form.querySelector('input[name="parent"]');
            var responseTitle = commentsRoot.querySelector('#response');
            var cancel = form.closest('.comment-respond').querySelector('#cancel-comment-reply-link');
            if (parentInput) parentInput.remove();
            if (responseTitle) responseTitle.textContent = '发表评论';
            if (cancel) cancel.style.display = 'none';
        }

        if (commentsRoot && commentsRoot.dataset.replyBound !== 'true') {
            commentsRoot.dataset.replyBound = 'true';
            commentsRoot.addEventListener('click', function (event) {
                var reply = event.target.closest('.comment-reply a:not(.comment-delete)');
                var cancel = event.target.closest('#cancel-comment-reply-link');
                var compose = event.target.closest('[data-comment-compose]');
                var close = event.target.closest('[data-comment-modal-close]');

                if (compose) {
                    event.preventDefault();
                    clearReply();
                    openCommentModal(compose);
                    return;
                }

                if (close) {
                    event.preventDefault();
                    closeCommentModal();
                    return;
                }

                if (cancel) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    clearReply();
                    form.querySelector('textarea[name="text"]').focus();
                    return;
                }

                if (!reply) return;
                event.preventDefault();
                event.stopImmediatePropagation();

                var match = (reply.getAttribute('onclick') || '').match(/TypechoComment\.reply\([^,]+,\s*(\d+)/);
                var href = new URL(reply.href, window.location.href);
                var parentId = match ? match[1] : href.searchParams.get('replyTo');
                if (!parentId) return;

                var parentInput = form.querySelector('input[name="parent"]');
                if (!parentInput) {
                    parentInput = document.createElement('input');
                    parentInput.type = 'hidden';
                    parentInput.name = 'parent';
                    form.appendChild(parentInput);
                }
                parentInput.value = parentId;

                var comment = reply.closest('.comment-body');
                var author = comment ? comment.querySelector('.comment-author') : null;
                var responseTitle = commentsRoot.querySelector('#response');
                var cancelLink = form.closest('.comment-respond').querySelector('#cancel-comment-reply-link');
                if (responseTitle) responseTitle.textContent = author ? '回复 ' + author.textContent.trim() : '回复评论';
                if (cancelLink) cancelLink.style.display = '';

                openCommentModal(reply);
            }, true);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && commentModal && !commentModal.hidden) closeCommentModal();
            });
        }

        form.dataset.ajaxBound = 'true';
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            if (form.dataset.submitting === 'true') return;
            form.dataset.submitting = 'true';

            var button = form.querySelector('button[type="submit"]');
            var buttonText = button.querySelector('span');
            var status = form.querySelector('.comment-form-status');
            var originalText = buttonText.textContent;
            var knownCommentIds = {};
            commentsRoot.querySelectorAll('.comment-body[id]').forEach(function (comment) {
                knownCommentIds[comment.id] = true;
            });

            button.disabled = true;
            button.classList.add('is-loading');
            form.setAttribute('aria-busy', 'true');
            buttonText.textContent = '正在提交';
            status.className = 'comment-form-status visible loading';
            status.textContent = '正在提交评论，请稍候...';

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (response) {
                return response.text().then(function (html) {
                    if (!response.ok) {
                        throw new Error(commentErrorMessage(html, '提交失败，请检查填写内容后重试。'));
                    }
                    return html;
                });
            }).then(function (html) {
                var nextDocument = new DOMParser().parseFromString(html, 'text/html');
                var nextComments = nextDocument.querySelector('#comments');
                var currentComments = document.querySelector('#comments');
                if (!nextComments) {
                    throw new Error(commentErrorMessage(html, '提交失败，请检查填写内容后重试。'));
                }
                if (!currentComments) throw new Error('评论区刷新失败，请重新载入页面。');

                var formTop = form.getBoundingClientRect().top;
                var currentHeading = currentComments.querySelector('.comments-heading');
                var nextHeading = nextComments.querySelector('.comments-heading');
                var currentList = currentComments.querySelector(':scope > .comment-list');
                var nextList = nextComments.querySelector(':scope > .comment-list');
                var currentPagination = currentComments.querySelector(':scope > .comment-pagination');
                var nextPagination = nextComments.querySelector(':scope > .comment-pagination');
                var respond = currentComments.querySelector('.comment-respond');
                var modal = currentComments.querySelector('[data-comment-modal]');
                var newCommentId = '';

                if (nextList) {
                    nextList.querySelectorAll('.comment-body[id]').forEach(function (comment) {
                        if (!knownCommentIds[comment.id]) newCommentId = comment.id;
                    });
                }

                if (currentHeading && nextHeading) currentHeading.replaceWith(nextHeading);
                if (currentList && nextList) {
                    currentList.replaceWith(nextList);
                } else if (!currentList && nextList && modal) {
                    modal.before(nextList);
                } else if (currentList && !nextList) {
                    currentList.remove();
                }
                if (currentPagination && nextPagination) {
                    currentPagination.replaceWith(nextPagination);
                } else if (!currentPagination && nextPagination && modal) {
                    var insertedList = currentComments.querySelector(':scope > .comment-list');
                    (insertedList || modal).after(nextPagination);
                } else if (currentPagination && !nextPagination) {
                    currentPagination.remove();
                }

                window.scrollBy(0, form.getBoundingClientRect().top - formTop);
                clearReply();
                form.querySelector('textarea[name="text"]').value = '';
                status.textContent = '评论已提交。';
                status.className = 'comment-form-status visible success';
                commentModal.hidden = true;
                document.body.classList.remove('comment-modal-open');
                if (newCommentId) {
                    var newComment = document.getElementById(newCommentId);
                    if (newComment) {
                        newComment.classList.add('comment-new');
                        window.requestAnimationFrame(function () {
                            scrollToTarget(newComment, 'center');
                        });
                        window.setTimeout(function () {
                            newComment.classList.remove('comment-new');
                        }, 2600);
                    }
                }
            }).catch(function (error) {
                status.textContent = error.message || '提交失败，请稍后重试。';
                status.className = 'comment-form-status visible error';
            }).finally(function () {
                if (button.isConnected) {
                    delete form.dataset.submitting;
                    form.removeAttribute('aria-busy');
                    button.disabled = false;
                    button.classList.remove('is-loading');
                    buttonText.textContent = originalText;
                }
            });
        });
    }

    bindAjaxComments(document);

    document.querySelectorAll('[data-comment-carousel]').forEach(function (carousel) {
        var slides = Array.prototype.slice.call(carousel.querySelectorAll('.sidebar-comment-slide'));
        var current = 0;
        var counter = carousel.querySelector('.sidebar-comment-controls b');
        var typingTimer = null;
        var autoplayTimer = null;
        var paused = false;
        var hovered = false;
        var focused = false;
        var autoplayStartedAt = 0;
        var autoplayRemaining = 4200;
        var transitioning = false;
        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        slides.forEach(function (slide) {
            var paragraph = slide.querySelector('p');
            if (paragraph) paragraph.dataset.fullText = paragraph.textContent;
        });

        function scheduleAutoplay() {
            window.clearTimeout(autoplayTimer);
            if (paused || slides.length <= 1) return;
            autoplayRemaining = 4200;
            autoplayStartedAt = Date.now();
            carousel.classList.remove('comment-counting', 'comment-paused');
            void carousel.offsetWidth;
            carousel.classList.add('comment-counting');
            autoplayTimer = window.setTimeout(function () {
                autoplayStartedAt = 0;
                changeComment(current + 1);
            }, autoplayRemaining);
        }

        function updateAutoplayPause() {
            var shouldPause = hovered || focused;
            if (shouldPause === paused) return;
            paused = shouldPause;

            if (paused) {
                if (autoplayStartedAt) {
                    autoplayRemaining = Math.max(0, autoplayRemaining - (Date.now() - autoplayStartedAt));
                }
                autoplayStartedAt = 0;
                window.clearTimeout(autoplayTimer);
                carousel.classList.add('comment-paused');
            } else if (slides.length > 1 && !transitioning) {
                autoplayStartedAt = Date.now();
                if (!carousel.classList.contains('comment-counting')) {
                    carousel.classList.add('comment-counting');
                }
                carousel.classList.remove('comment-paused');
                autoplayTimer = window.setTimeout(function () {
                    autoplayStartedAt = 0;
                    changeComment(current + 1);
                }, autoplayRemaining);
            }
        }

        function typeComment(slide) {
            var paragraph = slide.querySelector('p');
            if (!paragraph) return;
            var fullText = paragraph.dataset.fullText || '';
            var characters = fullText.match(/[\uD800-\uDBFF][\uDC00-\uDFFF]|[\s\S]/g) || [];
            window.clearTimeout(typingTimer);

            if (reduceMotion) {
                paragraph.textContent = fullText;
                paragraph.classList.remove('typing');
                transitioning = false;
                scheduleAutoplay();
                return;
            }

            var characterIndex = 0;
            paragraph.textContent = '';
            paragraph.classList.add('typing');

            function typeNextCharacter() {
                paragraph.textContent += characters[characterIndex];
                characterIndex++;
                if (characterIndex < characters.length) {
                    typingTimer = window.setTimeout(typeNextCharacter, 28);
                } else {
                    paragraph.classList.remove('typing');
                    transitioning = false;
                    scheduleAutoplay();
                }
            }

            if (characters.length) {
                typeNextCharacter();
            } else {
                paragraph.classList.remove('typing');
                transitioning = false;
                scheduleAutoplay();
            }
        }

        function showComment(index) {
            current = (index + slides.length) % slides.length;
            slides.forEach(function (slide, slideIndex) {
                slide.hidden = slideIndex !== current;
            });
            if (counter) counter.textContent = current < 9 ? '0' + (current + 1) : String(current + 1);
            typeComment(slides[current]);
        }

        function changeComment(index) {
            if (transitioning) return;
            window.clearTimeout(autoplayTimer);
            autoplayStartedAt = 0;
            carousel.classList.remove('comment-counting', 'comment-paused');

            if (reduceMotion) {
                showComment(index);
                return;
            }

            var paragraph = slides[current].querySelector('p');
            var characters = paragraph ? (paragraph.textContent.match(/[\uD800-\uDBFF][\uDC00-\uDFFF]|[\s\S]/g) || []) : [];
            transitioning = true;
            if (paragraph) paragraph.classList.add('typing');

            function deletePreviousCharacter() {
                characters.pop();
                if (paragraph) paragraph.textContent = characters.join('');
                if (characters.length) {
                    typingTimer = window.setTimeout(deletePreviousCharacter, 12);
                } else {
                    if (paragraph) paragraph.classList.remove('typing');
                    showComment(index);
                }
            }

            if (characters.length) {
                deletePreviousCharacter();
            } else {
                showComment(index);
            }
        }

        var previous = carousel.querySelector('[data-comment-prev]');
        var next = carousel.querySelector('[data-comment-next]');
        if (slides.length <= 1) {
            if (previous) previous.disabled = true;
            if (next) next.disabled = true;
        }
        if (previous) previous.addEventListener('click', function () { changeComment(current - 1); });
        if (next) next.addEventListener('click', function () { changeComment(current + 1); });
        carousel.addEventListener('mouseenter', function () {
            hovered = true;
            updateAutoplayPause();
        });
        carousel.addEventListener('mouseleave', function () {
            hovered = false;
            updateAutoplayPause();
        });
        carousel.addEventListener('focusin', function () {
            focused = true;
            updateAutoplayPause();
        });
        carousel.addEventListener('focusout', function () {
            focused = false;
            updateAutoplayPause();
        });
        showComment(0);
    });

    var postContent = document.querySelector('#post-content');
    var toc = document.querySelector('.post-toc');

    if (postContent) {
        var protectedForm = postContent.querySelector('form.protected');
        if (protectedForm) {
            var protectedPassword = protectedForm.querySelector('input[type="password"]');
            if (protectedPassword) {
                protectedPassword.setAttribute('aria-label', '文章访问密码');
                protectedPassword.setAttribute('placeholder', '输入访问密码');
                protectedPassword.setAttribute('autocomplete', 'current-password');
            }
        }

        var contentImages = Array.prototype.slice.call(postContent.querySelectorAll('img'));
        contentImages.forEach(function (contentImage) {
            contentImage.loading = 'lazy';
            contentImage.decoding = 'async';
            var imageFrame = document.createElement('span');
            imageFrame.className = 'post-image-frame is-loading';
            var imageLoader = document.createElement('span');
            imageLoader.className = 'post-image-loader';
            imageLoader.setAttribute('aria-hidden', 'true');
            imageLoader.innerHTML = '<svg class="carbon-icon" width="22" height="22" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.4" stroke-dasharray="26 12"/></svg>';
            contentImage.parentNode.insertBefore(imageFrame, contentImage);
            imageFrame.appendChild(contentImage);
            imageFrame.appendChild(imageLoader);

            function finishImageLoading() {
                imageFrame.classList.remove('is-loading');
                if (imageLoader.parentNode) imageLoader.parentNode.removeChild(imageLoader);
            }

            contentImage.addEventListener('load', finishImageLoading);
            contentImage.addEventListener('error', finishImageLoading);
            if (contentImage.complete) finishImageLoading();
        });

        var imageLightbox = document.querySelector('[data-image-lightbox]');
        if (imageLightbox && contentImages.length) {
            var lightboxImage = imageLightbox.querySelector('.image-lightbox-stage img');
            var lightboxCaption = imageLightbox.querySelector('[data-lightbox-caption]');
            var lightboxCount = imageLightbox.querySelector('[data-lightbox-count]');
            var lightboxPrevious = imageLightbox.querySelector('[data-lightbox-prev]');
            var lightboxNext = imageLightbox.querySelector('[data-lightbox-next]');
            var lightboxClose = imageLightbox.querySelector('.image-lightbox-close');
            var lightboxIndex = 0;
            var lightboxOpener = null;
            var lightboxScale = 1;
            var lightboxX = 0;
            var lightboxY = 0;
            var lightboxDragX = 0;
            var lightboxDragY = 0;
            var lightboxDragging = false;

            function renderLightboxTransform() {
                lightboxImage.style.transform = 'translate(' + lightboxX + 'px, ' + lightboxY + 'px) scale(' + lightboxScale + ')';
                lightboxImage.classList.toggle('is-moved', lightboxScale > 1 || lightboxX !== 0 || lightboxY !== 0);
            }

            function resetLightboxTransform() {
                lightboxScale = 1;
                lightboxX = 0;
                lightboxY = 0;
                renderLightboxTransform();
            }

            function showLightboxImage(index) {
                lightboxIndex = (index + contentImages.length) % contentImages.length;
                var sourceImage = contentImages[lightboxIndex];
                lightboxImage.src = sourceImage.currentSrc || sourceImage.src;
                lightboxImage.alt = sourceImage.alt || '';
                lightboxCaption.textContent = sourceImage.alt || '';
                lightboxCaption.hidden = !sourceImage.alt;
                lightboxCount.textContent = (lightboxIndex + 1) + ' / ' + contentImages.length;
                resetLightboxTransform();
            }

            function openLightbox(image) {
                lightboxOpener = image;
                showLightboxImage(contentImages.indexOf(image));
                imageLightbox.hidden = false;
                document.body.classList.add('image-lightbox-open');
                if (lenis) lenis.stop();
                lightboxClose.focus();
            }

            function closeLightbox() {
                if (imageLightbox.hidden) return;
                imageLightbox.hidden = true;
                lightboxImage.removeAttribute('src');
                document.body.classList.remove('image-lightbox-open');
                if (lenis) lenis.start();
                if (lightboxOpener && lightboxOpener.isConnected) lightboxOpener.focus();
            }

            contentImages.forEach(function (contentImage) {
                contentImage.tabIndex = 0;
                contentImage.setAttribute('role', 'button');
                contentImage.setAttribute('aria-label', (contentImage.alt ? contentImage.alt + '，' : '') + '查看大图');
                contentImage.addEventListener('click', function (event) {
                    event.preventDefault();
                    openLightbox(contentImage);
                });
                contentImage.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        openLightbox(contentImage);
                    }
                });
            });

            imageLightbox.addEventListener('click', function (event) {
                if (event.target.closest('[data-lightbox-close]')) closeLightbox();
                else if (event.target.closest('[data-lightbox-prev]')) showLightboxImage(lightboxIndex - 1);
                else if (event.target.closest('[data-lightbox-next]')) showLightboxImage(lightboxIndex + 1);
            });
            if (contentImages.length < 2) {
                lightboxPrevious.hidden = true;
                lightboxNext.hidden = true;
            }
            lightboxImage.draggable = false;
            lightboxImage.addEventListener('load', resetLightboxTransform);
            lightboxImage.addEventListener('wheel', function (event) {
                event.preventDefault();
                var previousScale = lightboxScale;
                lightboxScale = Math.max(1, Math.min(5, lightboxScale + (event.deltaY < 0 ? .2 : -.2)));
                if (lightboxScale === 1) {
                    lightboxX = 0;
                    lightboxY = 0;
                } else if (previousScale > 0) {
                    lightboxX *= lightboxScale / previousScale;
                    lightboxY *= lightboxScale / previousScale;
                }
                renderLightboxTransform();
            }, { passive: false });
            lightboxImage.addEventListener('pointerdown', function (event) {
                if (event.button !== 0) return;
                lightboxDragging = true;
                lightboxDragX = event.clientX - lightboxX;
                lightboxDragY = event.clientY - lightboxY;
                lightboxImage.setPointerCapture(event.pointerId);
                lightboxImage.classList.add('is-dragging');
                event.preventDefault();
            });
            lightboxImage.addEventListener('pointermove', function (event) {
                if (!lightboxDragging || !lightboxImage.hasPointerCapture(event.pointerId)) return;
                lightboxX = event.clientX - lightboxDragX;
                lightboxY = event.clientY - lightboxDragY;
                renderLightboxTransform();
            });
            function finishLightboxDrag(event) {
                if (!lightboxDragging) return;
                lightboxDragging = false;
                lightboxImage.classList.remove('is-dragging');
                if (lightboxImage.hasPointerCapture(event.pointerId)) lightboxImage.releasePointerCapture(event.pointerId);
            }
            lightboxImage.addEventListener('pointerup', finishLightboxDrag);
            lightboxImage.addEventListener('pointercancel', finishLightboxDrag);
            lightboxImage.addEventListener('dblclick', resetLightboxTransform);
            document.addEventListener('keydown', function (event) {
                if (imageLightbox.hidden) return;
                if (event.key === 'Escape') closeLightbox();
                else if (event.key === 'ArrowLeft') showLightboxImage(lightboxIndex - 1);
                else if (event.key === 'ArrowRight') showLightboxImage(lightboxIndex + 1);
            });
        }

        postContent.querySelectorAll('li').forEach(function (item) {
            Array.prototype.slice.call(item.children).forEach(function (child) {
                if (child.tagName !== 'P' || !child.firstChild || child.firstChild.nodeType !== 3
                    || !/^ {2,}\S/.test(child.firstChild.nodeValue)) return;

                var lines = [''];
                Array.prototype.forEach.call(child.childNodes, function (node) {
                    if (node.nodeName === 'BR') {
                        lines.push('');
                    } else {
                        lines[lines.length - 1] += node.textContent;
                    }
                });
                var code = document.createElement('code');
                code.textContent = lines.map(function (line) { return line.replace(/^ {2}/, ''); }).join('\n');
                var pre = document.createElement('pre');
                pre.appendChild(code);
                child.parentNode.replaceChild(pre, child);
            });
        });

        postContent.querySelectorAll('li').forEach(function (item) {
            var existingCheckbox = item.querySelector(':scope > input[type="checkbox"], :scope > p > input[type="checkbox"]');
            if (existingCheckbox) {
                item.classList.add('task-list-item');
                item.parentElement.classList.add('task-list');
                if (existingCheckbox.checked) item.classList.add('task-completed');
                return;
            }

            var textNode = null;
            Array.prototype.some.call(item.childNodes, function (node) {
                if (node.nodeType === 3 && /^\s*\[[ xX]\]\s*/.test(node.nodeValue)) {
                    textNode = node;
                    return true;
                }
                if (node.nodeType === 1 && node.tagName === 'P' && node.firstChild
                    && node.firstChild.nodeType === 3 && /^\s*\[[ xX]\]\s*/.test(node.firstChild.nodeValue)) {
                    textNode = node.firstChild;
                    return true;
                }
                return false;
            });
            if (!textNode) return;

            var marker = textNode.nodeValue.match(/^\s*\[([ xX])\]\s*/);
            textNode.nodeValue = textNode.nodeValue.replace(/^\s*\[[ xX]\]\s*/, '');
            var checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.disabled = true;
            checkbox.checked = marker[1].toLowerCase() === 'x';
            checkbox.setAttribute('aria-label', checkbox.checked ? '已完成' : '未完成');
            textNode.parentNode.insertBefore(checkbox, textNode);
            item.classList.add('task-list-item');
            if (checkbox.checked) item.classList.add('task-completed');
            item.parentElement.classList.add('task-list');

            var paragraph = checkbox.parentElement;
            if (paragraph.tagName === 'P' && paragraph.parentElement === item) {
                while (paragraph.firstChild) item.insertBefore(paragraph.firstChild, paragraph);
                item.removeChild(paragraph);
            }
        });

        var headings = Array.prototype.slice.call(postContent.querySelectorAll('h2, h3, h4'));
        var usedHeadingIds = {};

        headings.forEach(function (heading, index) {
            var baseId = heading.id || heading.textContent.trim().toLowerCase()
                .replace(/[^\w\u4e00-\u9fff-]+/g, '-')
                .replace(/^-+|-+$/g, '') || 'section-' + (index + 1);
            var headingId = baseId;
            var suffix = 2;
            var existingHeading = document.getElementById(headingId);
            while (usedHeadingIds[headingId] || (existingHeading && existingHeading !== heading)) {
                headingId = baseId + '-' + suffix++;
                existingHeading = document.getElementById(headingId);
            }
            heading.id = headingId;
            usedHeadingIds[headingId] = true;
        });

        var tocMarkers = Array.prototype.filter.call(postContent.children, function (element) {
            return element.textContent.trim().toUpperCase() === '[TOC]';
        });

        function bindTocNavigation(container) {
            container.addEventListener('click', function (event) {
                var link = event.target.closest('a');
                if (!link) return;
                var targetId = decodeURIComponent(link.getAttribute('href').slice(1));
                var target = document.getElementById(targetId);
                if (!target) return;
                event.preventDefault();
                scrollToTarget(target, 'start');
                if (window.history && window.history.pushState) {
                    window.history.pushState(null, '', link.getAttribute('href'));
                }
            });
        }

        tocMarkers.forEach(function (marker) {
            if (!headings.length) {
                marker.remove();
                return;
            }

            var inlineToc = document.createElement('nav');
            inlineToc.className = 'post-inline-toc';
            inlineToc.setAttribute('aria-label', '本文目录');

            var title = document.createElement('div');
            title.className = 'post-inline-toc-title';
            title.textContent = '目录';
            inlineToc.appendChild(title);

            var rootList = document.createElement('ol');
            inlineToc.appendChild(rootList);
            var currentList = rootList;
            var listStack = [rootList];
            var baseLevel = parseInt(headings[0].tagName.slice(1), 10);
            var currentLevel = baseLevel;
            var lastItem = null;

            headings.forEach(function (heading) {
                var headingLevel = parseInt(heading.tagName.slice(1), 10);
                headingLevel = Math.max(baseLevel, Math.min(headingLevel, currentLevel + 1));

                if (headingLevel > currentLevel && lastItem) {
                    currentList = document.createElement('ol');
                    lastItem.appendChild(currentList);
                    listStack.push(currentList);
                    currentLevel++;
                } else {
                    while (headingLevel < currentLevel && listStack.length > 1) {
                        listStack.pop();
                        currentLevel--;
                    }
                    currentList = listStack[listStack.length - 1];
                }

                var item = document.createElement('li');
                var link = document.createElement('a');
                link.href = '#' + encodeURIComponent(heading.id);
                link.textContent = heading.textContent.trim();
                item.appendChild(link);
                currentList.appendChild(item);
                lastItem = item;
            });

            bindTocNavigation(inlineToc);
            marker.replaceWith(inlineToc);
        });

        if (toc && headings.length > 1) {
            var tocNav = toc.querySelector('nav');
            var tocLinks = headings.map(function (heading) {
                var link = document.createElement('a');
                link.href = '#' + encodeURIComponent(heading.id);
                link.className = 'toc-level-' + heading.tagName.slice(1);
                link.textContent = heading.textContent.trim();
                tocNav.appendChild(link);
                return link;
            });
            toc.hidden = false;

            tocNav.addEventListener('click', function (event) {
                var link = event.target.closest('a');
                if (!link) return;
                var targetIndex = tocLinks.indexOf(link);
                if (targetIndex < 0) return;
                event.preventDefault();
                scrollToTarget(headings[targetIndex], 'start');
                if (window.history && window.history.pushState) {
                    window.history.pushState(null, '', link.getAttribute('href'));
                }
            });

            function updateActiveToc() {
                var activeIndex = 0;
                headings.forEach(function (heading, index) {
                    if (heading.getBoundingClientRect().top <= 120) activeIndex = index;
                });
                tocLinks.forEach(function (link, index) {
                    link.classList.toggle('active', index === activeIndex);
                    if (index === activeIndex) link.setAttribute('aria-current', 'location');
                    else link.removeAttribute('aria-current');
                });
            }

            window.addEventListener('scroll', updateActiveToc, { passive: true });
            updateActiveToc();

            if (window.location.hash) {
                var hashTarget = document.getElementById(decodeURIComponent(window.location.hash.slice(1)));
                if (hashTarget && headings.indexOf(hashTarget) !== -1) {
                    window.requestAnimationFrame(function () { scrollToTarget(hashTarget, 'start'); });
                }
            }
        }

        var mermaidCodes = Array.prototype.slice.call(
            postContent.querySelectorAll('pre > code.language-mermaid, pre > code.lang-mermaid')
        );
        if (mermaidEnabled && mermaidCodes.length) {
            var mermaidNodes = mermaidCodes.map(function (code, index) {
                var diagram = document.createElement('div');
                diagram.className = 'mermaid';
                diagram.id = 'mermaid-diagram-' + (index + 1);
                diagram.textContent = code.textContent;
                var wrapper = document.createElement('div');
                wrapper.className = 'mermaid-diagram';
                wrapper.appendChild(diagram);
                code.parentElement.parentNode.replaceChild(wrapper, code.parentElement);
                return diagram;
            });

            function renderMermaid() {
                window.mermaid.initialize({
                    startOnLoad: false,
                    securityLevel: 'strict',
                    theme: 'neutral',
                    fontFamily: 'inherit'
                });
                window.mermaid.run({ nodes: mermaidNodes }).catch(function () {
                    mermaidNodes.forEach(function (diagram) {
                        if (!diagram.querySelector('svg')) diagram.parentElement.classList.add('mermaid-error');
                    });
                });
            }

            if (window.mermaid) {
                renderMermaid();
            } else {
                loadOptionalScript(vendorUrls.mermaid, vendorUrls.mermaidFallback, renderMermaid);
            }
        }

        var codeBlocks = Array.prototype.slice.call(postContent.querySelectorAll('pre code'));
        codeBlocks.forEach(function (code) {
            var pre = code.parentElement;
            var languageClass = Array.prototype.find.call(code.classList, function (name) {
                return name.indexOf('language-') === 0 || name.indexOf('lang-') === 0;
            });
            var language = languageClass ? languageClass.replace(/^language-|^lang-/, '') : (code.result && code.result.language ? code.result.language : 'text');
            var label = document.createElement('span');
            label.className = 'code-language';
            label.textContent = language;
            pre.appendChild(label);

            var copyButton = document.createElement('button');
            copyButton.type = 'button';
            copyButton.className = 'code-copy';
            copyButton.textContent = '复制';
            copyButton.addEventListener('click', function () {
                if (!navigator.clipboard) return;
                navigator.clipboard.writeText(code.textContent).then(function () {
                    copyButton.textContent = '已复制';
                    window.setTimeout(function () { copyButton.textContent = '复制'; }, 1600);
                });
            });
            pre.appendChild(copyButton);
        });

        if (codeHighlightEnabled && codeBlocks.length) {
            loadOptionalScript(vendorUrls.highlight, vendorUrls.highlightFallback, function () {
                if (!window.hljs) return;
                codeBlocks.forEach(function (code) {
                    if (code.isConnected) window.hljs.highlightElement(code);
                });
            });
        }
    }

    document.querySelectorAll('.post-content table').forEach(function (table) {
        if (!table.parentElement.classList.contains('table-wrap')) {
            var wrapper = document.createElement('div');
            wrapper.className = 'table-wrap';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });

    function updateScrollUi() {
        var scrollTop = window.scrollY || document.documentElement.scrollTop;
        var scrollable = document.documentElement.scrollHeight - window.innerHeight;
        if (progress) progress.style.width = (scrollable > 0 ? scrollTop / scrollable * 100 : 0) + '%';
        if (topButton) topButton.classList.toggle('visible', scrollTop > 360);
    }

    window.addEventListener('scroll', updateScrollUi, { passive: true });
    window.addEventListener('resize', updateScrollUi);
    updateScrollUi();

    if (topButton) {
        topButton.addEventListener('click', function () {
            if (lenis) {
                lenis.scrollTo(0);
                return;
            }
            window.scrollTo({ top: 0, behavior: reducedMotion.matches ? 'auto' : 'smooth' });
        });
    }

    if (commentsButton) {
        commentsButton.addEventListener('click', function () {
            var comments = document.getElementById('comments');
            if (comments) scrollToTarget(comments, 'start');
        });
    }
}());
