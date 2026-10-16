/*
 * ZED CMS behaviour. Everything is attached with addEventListener: the
 * Content-Security-Policy allows no inline scripts, handlers or styles.
 */
(function () {
    'use strict';

    var csrf = document.querySelector('meta[name="csrf-token"]');

    // --- confirm before destructive submits: <form data-confirm="...">
    document.addEventListener('submit', function (event) {
        var form = event.target;
        var message = form.getAttribute('data-confirm');
        if (message && !window.confirm(message)) {
            event.preventDefault();
        }
    });

    // --- "select all" checkbox in lists
    document.addEventListener('change', function (event) {
        var all = event.target.closest('[data-check-all]');
        if (!all) {
            return;
        }
        var table = all.closest('table');
        table.querySelectorAll('tbody input[type="checkbox"]').forEach(function (box) {
            box.checked = all.checked;
        });
    });

    // --- sidebar on small screens
    document.addEventListener('click', function (event) {
        var sidebar = document.getElementById('cms-sidebar');
        if (!sidebar) {
            return;
        }
        if (event.target.closest('[data-toggle-sidebar]')) {
            sidebar.classList.toggle('open');
        } else if (sidebar.classList.contains('open') && !event.target.closest('#cms-sidebar')) {
            sidebar.classList.remove('open');
        }
    });

    // --- fields shown only for some values of another field: data-show-if / data-show-values
    function applyShowIf() {
        document.querySelectorAll('[data-show-if]').forEach(function (wrap) {
            var control = document.querySelector('[name="' + wrap.getAttribute('data-show-if') + '"]');
            if (!control) {
                return;
            }
            var values = wrap.getAttribute('data-show-values').split(',');
            var visible = values.indexOf(String(control.value)) !== -1;
            wrap.classList.toggle('is-hidden', !visible);
            wrap.querySelectorAll('input, select, textarea').forEach(function (input) {
                // hidden fields are not posted, so the server ignores them too
                input.disabled = !visible;
            });
        });
    }
    document.addEventListener('change', function (event) {
        if (event.target.name && document.querySelector('[data-show-if="' + event.target.name + '"]')) {
            applyShowIf();
        }
    });
    applyShowIf();

    // --- ordered multi-select (menu pages)
    function orderedItem(container, value, label) {
        var name = container.getAttribute('data-ordered-select') + '[]';
        var li = document.createElement('li');
        li.className = 'list-group-item d-flex align-items-center gap-2';
        li.setAttribute('data-value', value);
        var text = document.createElement('span');
        text.className = 'me-auto';
        text.textContent = label;
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = name;
        hidden.value = value;
        li.appendChild(text);
        li.appendChild(hidden);
        [['up', 'btn-outline-secondary', 'bi-arrow-up', 'Move up'], ['down', 'btn-outline-secondary', 'bi-arrow-down', 'Move down'], ['remove', 'btn-outline-danger', 'bi-x-lg', 'Remove']].forEach(function (b) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-sm ' + b[1];
            button.setAttribute(b[0] === 'remove' ? 'data-remove' : 'data-move', b[0]);
            button.setAttribute('aria-label', b[3]);
            var icon = document.createElement('i');
            icon.className = 'bi ' + b[2];
            button.appendChild(icon);
            li.appendChild(button);
        });
        return li;
    }
    document.addEventListener('change', function (event) {
        var add = event.target.closest('[data-ordered-add]');
        if (!add || !add.value) {
            return;
        }
        var container = add.closest('[data-ordered-select]');
        var list = container.querySelector('[data-ordered-list]');
        if (!list.querySelector('[data-value="' + add.value + '"]')) {
            list.appendChild(orderedItem(container, add.value, add.options[add.selectedIndex].text));
        }
        add.value = '';
    });
    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-move], [data-remove]');
        if (!button || !button.closest('[data-ordered-list]')) {
            return;
        }
        var item = button.closest('li');
        if (button.hasAttribute('data-remove')) {
            item.remove();
        } else if (button.getAttribute('data-move') === 'up' && item.previousElementSibling) {
            item.parentNode.insertBefore(item, item.previousElementSibling);
        } else if (button.getAttribute('data-move') === 'down' && item.nextElementSibling) {
            item.parentNode.insertBefore(item.nextElementSibling, item);
        }
    });

    // --- image preview before upload
    document.addEventListener('change', function (event) {
        var input = event.target.closest('input[type="file"][data-preview]');
        if (!input || !input.files || !input.files[0] || input.accept !== 'image/*') {
            return;
        }
        var wrap = input.parentNode;
        var img = wrap.querySelector('img.current-image');
        if (!img) {
            img = document.createElement('img');
            img.className = 'img-thumbnail current-image mb-2';
            img.alt = 'New image preview';
            wrap.insertBefore(img, input);
        }
        img.src = URL.createObjectURL(input.files[0]);
    });

    // --- rich text editor (Trumbowyg) on <textarea data-editor>
    if (window.jQuery && jQuery.fn.trumbowyg) {
        jQuery.trumbowyg.svgPath = '/vendor/trumbowyg/ui/icons.svg';
        jQuery('textarea[data-editor]').trumbowyg({
            semantic: false,
            removeformatPasted: false,
            btnsDef: {
                image: { dropdown: ['insertImage', 'upload'], ico: 'insertImage' }
            },
            btns: [
                ['viewHTML'], ['undo', 'redo'], ['formatting'], ['fontsize'],
                ['strong', 'em', 'del'], ['foreColor', 'backColor'],
                ['link'], ['image'], ['table'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'], ['horizontalRule'], ['removeformat'], ['fullscreen']
            ],
            plugins: {
                upload: {
                    serverPath: '/cms/editor/upload',
                    fileFieldName: 'image',
                    headers: { 'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : '' },
                    urlPropertyName: 'url'
                }
            }
        });
    }
})();
