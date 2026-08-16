/**
 * Nestable jQuery Plugin (Full Hierarchical Drag & Drop)
 */
;(function($, window, document, undefined) {
    var hasTouch = 'ontouchstart' in document;

    var defaults = {
        listNodeName    : 'ol',
        itemNodeName    : 'li',
        rootClass       : 'dd',
        listClass       : 'dd-list',
        itemClass       : 'dd-item',
        dragClass       : 'dd-dragel',
        handleClass     : 'dd-handle',
        collapsedClass  : 'dd-collapsed',
        placeClass      : 'dd-placeholder',
        noDragClass     : 'dd-nodrag',
        emptyClass      : 'dd-empty',
        expandBtnHTML   : '<button data-action="expand" type="button" class="dd-btn-expand"><i class="bi bi-chevron-right text-xs"></i></button>',
        collapseBtnHTML : '<button data-action="collapse" type="button" class="dd-btn-collapse"><i class="bi bi-chevron-down text-xs"></i></button>',
        group           : 0,
        maxDepth        : 2,
        threshold       : 15
    };

    function Plugin(element, options) {
        this.w  = $(document);
        this.el = $(element);
        this.options = $.extend({}, defaults, options);
        this.init();
    }

    Plugin.prototype = {
        init: function() {
            var plugin = this;
            plugin.reset();
            plugin.el.data('nestable-id', new Date().getTime());

            plugin.w.on('resize', function() {
                plugin.reset();
            });

            var onStartEvent = function(e) {
                var handle = $(e.target);
                if (!handle.hasClass(plugin.options.handleClass)) {
                    if (handle.closest('.' + plugin.options.noDragClass).length) {
                        return;
                    }
                    handle = handle.closest('.' + plugin.options.handleClass);
                }
                if (!handle.length || plugin.dragEl) {
                    return;
                }
                plugin.isTouch = /^touch/.test(e.type);
                if (plugin.isTouch && e.touches.length !== 1) {
                    return;
                }
                e.preventDefault();
                plugin.dragStart(e.touches ? e.touches[0] : e);
            };

            var onMoveEvent = function(e) {
                if (plugin.dragEl) {
                    e.preventDefault();
                    plugin.dragMove(e.touches ? e.touches[0] : e);
                }
            };

            var onEndEvent = function(e) {
                if (plugin.dragEl) {
                    e.preventDefault();
                    plugin.dragStop(e.touches ? e.touches[0] : e);
                }
            };

            if (hasTouch) {
                plugin.el[0].addEventListener('touchstart', onStartEvent, false);
                window.addEventListener('touchmove', onMoveEvent, false);
                window.addEventListener('touchend', onEndEvent, false);
                window.addEventListener('touchcancel', onEndEvent, false);
            }

            plugin.el.on('mousedown', onStartEvent);
            plugin.w.on('mousemove', onMoveEvent);
            plugin.w.on('mouseup', onEndEvent);
        },

        serialize: function() {
            var data,
                depth = 0,
                list  = this;
                step  = function(level, depth) {
                    var array = [ ],
                        items = level.children(list.options.itemNodeName);
                    items.each(function() {
                        var li   = $(this),
                            item = $.extend({}, li.data()),
                            sub  = li.children(list.options.listNodeName);
                        if (sub.length) {
                            item.children = step(sub, depth + 1);
                        }
                        array.push(item);
                    });
                    return array;
                };
            data = step(list.el.find(list.options.listNodeName).first(), depth);
            return data;
        },

        reset: function() {
            this.mouse = {
                offsetX   : 0,
                offsetY   : 0,
                startX    : 0,
                startY    : 0,
                lastX     : 0,
                lastY     : 0,
                nowX      : 0,
                nowY      : 0,
                distX     : 0,
                distY     : 0,
                dirAx     : 0,
                dirX      : 0,
                dirY      : 0,
                lastDirX  : 0,
                lastDirY  : 0,
                distAxX   : 0,
                distAxY   : 0
            };
            this.isTouch    = false;
            this.moving     = false;
            this.dragEl     = null;
            this.dragDepth  = 0;
            this.hasNewRoot = false;
            this.pointEl    = null;
        },

        dragStart: function(e) {
            var mouse    = this.mouse,
                target   = $(e.target),
                dragItem = target.closest(this.options.itemNodeName);

            this.placeEl = $('<div class="' + this.options.placeClass + '"/>');

            mouse.offsetX = e.pageX - dragItem.offset().left;
            mouse.offsetY = e.pageY - dragItem.offset().top;
            mouse.startX = mouse.lastX = e.pageX;
            mouse.startY = mouse.lastY = e.pageY;

            this.dragRootEl = this.el;

            this.dragEl = $(document.createElement(this.options.listNodeName)).addClass(this.options.listClass + ' ' + this.options.dragClass);
            this.dragEl.css('width', dragItem.outerWidth());

            // Calculate depth of dragItem
            this.dragDepth = dragItem.parents(this.options.listNodeName).length;

            dragItem.after(this.placeEl);
            dragItem[0].parentNode.removeChild(dragItem[0]);
            dragItem.appendTo(this.dragEl);

            $(document.body).append(this.dragEl);
            this.dragEl.css({
                'left' : e.pageX - mouse.offsetX,
                'top'  : e.pageY - mouse.offsetY
            });

            // Calculate total depth
            var depth = this.dragEl.find(this.options.listNodeName).length;
            this.dragDepth += depth;
        },

        dragStop: function(e) {
            var el = this.dragEl.children(this.options.itemNodeName).first();
            el[0].parentNode.removeChild(el[0]);
            this.placeEl.replaceWith(el);

            this.dragEl.remove();
            this.el.trigger('change');
            this.reset();
        },

        dragMove: function(e) {
            var list, parent, prev,
                opt   = this.options,
                mouse = this.mouse;

            this.dragEl.css({
                'left' : e.pageX - mouse.offsetX,
                'top'  : e.pageY - mouse.offsetY
            });

            // mouse position last frame
            mouse.lastX = mouse.nowX;
            mouse.lastY = mouse.nowY;
            // mouse position this frame
            mouse.nowX  = e.pageX;
            mouse.nowY  = e.pageY;
            // distance mouse moved between frames
            mouse.distX = mouse.nowX - mouse.lastX;
            mouse.distY = mouse.nowY - mouse.lastY;
            // direction of movement
            mouse.dirX  = mouse.distX === 0 ? 0 : mouse.distX > 0 ? 1 : -1;
            mouse.dirY  = mouse.distY === 0 ? 0 : mouse.distY > 0 ? 1 : -1;
            // total distance mouse moved since last direction change
            var newAx = Math.abs(mouse.distX) > 0 ? 1 : 0;
            if (mouse.dirX !== mouse.lastDirX) {
                mouse.distAxX = 0;
            } else {
                mouse.distAxX += Math.abs(mouse.distX);
            }
            mouse.lastDirX = mouse.dirX;

            // Horizontal drag: check if moving right to nest as child
            if (mouse.distAxX >= opt.threshold) {
                // reset dist
                mouse.distAxX = 0;

                // Move right: NEST into previous sibling
                if (mouse.distX > 0) {
                    prev = this.placeEl.prev(opt.itemNodeName);
                    // check depth
                    var currentDepth = this.placeEl.parents(opt.listNodeName).length;
                    if (prev.length && currentDepth < opt.maxDepth) {
                        list = prev.children(opt.listNodeName);
                        if (!list.length) {
                            list = $('<' + opt.listNodeName + '/>').addClass(opt.listClass);
                            prev.append(list);
                        }
                        list.append(this.placeEl);
                    }
                }

                // Move left: UN-NEST to parent list
                if (mouse.distX < 0) {
                    var parentList = this.placeEl.parent(opt.listNodeName);
                    var parentItem = parentList.parent(opt.itemNodeName);
                    if (parentItem.length) {
                        parentItem.after(this.placeEl);
                        if (!parentList.children().length) {
                            parentList.remove();
                        }
                    }
                }
            }

            var target = $(document.elementFromPoint(e.clientX, e.clientY));
            if (!target.length) return;

            var pointEl = target.closest('.' + opt.itemClass);
            if (!pointEl.length) return;

            // Don't target elements inside the dragged item itself
            if (pointEl.closest('.' + opt.dragClass).length) return;

            var isBefore = (e.pageY - pointEl.offset().top) < (pointEl.height() / 2);
            if (isBefore) {
                pointEl.before(this.placeEl);
            } else {
                pointEl.after(this.placeEl);
            }
        }
    };

    $.fn.nestable = function(params) {
        var lists  = this,
            retval = this;

        lists.each(function() {
            var plugin = $(this).data("nestable");
            if (!plugin) {
                $(this).data("nestable", new Plugin(this, params));
                $(this).data("nestable-id", new Date().getTime());
            } else {
                if (typeof params === 'string' && typeof plugin[params] === 'function') {
                    retval = plugin[params]();
                }
            }
        });

        return retval || lists;
    };

})(window.jQuery || window.Zepto, window, document);
