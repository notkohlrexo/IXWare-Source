import { getjQuery, typeCheckConfig, isVisible } from '../mdb/util/index';
import EventHandler from '../mdb/dom/event-handler';
import Manipulator from '../mdb/dom/manipulator';
import SelectorEngine from '../mdb/dom/selector-engine';
import BSAlert from '../bootstrap/src/alert';

/**
 * ------------------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------------------
 */

const NAME = 'alert';
const SELECTOR_ALERT = '.alert';
const DefaultType = {
  position: '(string || null)',
  delay: 'number',
  autohide: 'boolean',
  width: '(string || null)',
  offset: 'number',
  stacking: 'boolean',
  hidden: 'boolean',
  appendToBody: 'boolean',
  color: '(string || null)',
};

const Default = {
  position: null,
  delay: 1000,
  autohide: false,
  width: null,
  offset: 10,
  stacking: false,
  hidden: false,
  appendToBody: false,
  color: null,
};

class Alert extends BSAlert {
  constructor(element, data = {}) {
    super(element, data);
    this._options = this._getConfig(data);
    this._init();
  }

  // Getters

  get verticalOffset() {
    if (!this._options.stacking) return 0;
    const offset = SelectorEngine.find(SELECTOR_ALERT)
      .filter((alert) => alert !== this._element && isVisible(alert))
      .map((alert) => ({ el: alert, instance: Alert.getInstance(alert) }))
      .filter(({ instance }) => {
        if (!instance) return false;
        return (
          instance._options.container === this._options.container &&
          instance._options.position === this._options.position
        );
      })
      .map(({ el, instance }) => {
        const { y, height } = el.getBoundingClientRect();

        if (this.position.y === 'bottom') {
          return y - (y - height - instance._options.offset * 2);
        }

        return instance._options.offset * 2 + height;
      })
      .reduce((a, b) => a + b, 0);
    return offset;
  }

  get parent() {
    const [parent] = SelectorEngine.parents(this._element, this._options.container);
    return parent;
  }

  get position() {
    const [y, x] = this._options.position.split('-');
    return { y, x };
  }

  // Public

  update(updatedData = {}) {
    if (this._timeout !== null) {
      clearTimeout(this._timeout);
      this._timeout = null;
    }
    this._options = this._getConfig(updatedData);
    this._setup();
  }

  hide() {
    if (this._element.classList.contains('show')) {
      Manipulator.toggleClass(this._element, 'show');
      const handler = (e) => {
        Manipulator.style(e.target, {
          display: 'none',
        });
        if (this._timeout !== null) {
          clearTimeout(this._timeout);
          this._timeout = null;
        }
        EventHandler.off(e.target, 'transitionend', handler);
      };
      EventHandler.on(this._element, 'transitionend', handler);
    }
  }

  show() {
    if (this._options.autohide) {
      this._setupAutohide();
    }
    if (!this._element.classList.contains('show')) {
      Manipulator.style(this._element, {
        display: 'block',
      });
      if (isVisible(this._element)) {
        const handler = (e) => {
          Manipulator.style(e.target, {
            display: 'block',
          });
          EventHandler.off(e.target, 'transitionend', handler);
        };
        Manipulator.toggleClass(this._element, 'show');
        if (this._options.position) {
          this._setupAlignment();
        }
        EventHandler.on(this._element, 'transitionend', handler);
      }
    }
  }

  // Private

  _init() {
    if (this._options.hidden) {
      Manipulator.style(this._element, {
        display: 'none',
      });
    }
    if (this._options.color) {
      this._setColor();
    }
    this._setup();
  }

  _setup() {
    if (this._options.autohide) {
      this._setupAutohide();
    }
    if (this._options.width) {
      this._setupWidth();
    }
    if (this._options.appendToBody) {
      this._appendToBody();
    }
    if (!this._options.position) {
      return;
    }
    this._setupAlignment();
    this._setupPosition();
  }

  _setColor() {
    const colors = [
      'primary',
      'secondary',
      'success',
      'info',
      'warning',
      'danger',
      'light',
      'dark',
    ];
    const color = colors.includes(this._options.color) ? this._options.color : 'primary';
    colors.forEach((color) => {
      this._element.classList.remove(`alert-${color}`);
    });
    Manipulator.addClass(this._element, `alert-${color}`);
  }

  _setupWidth() {
    Manipulator.style(this._element, {
      width: this._options.width,
    });
  }

  _setupAutohide() {
    this._timeout = setTimeout(() => {
      this.hide();
    }, this._options.delay);
  }

  _setupAlignment() {
    const oppositeY = this.position.y === 'top' ? 'bottom' : 'top';
    const oppositeX = this.position.x === 'left' ? 'right' : 'left';
    if (this.position.x === 'center') {
      Manipulator.style(this._element, {
        [this.position.y]: `${this.verticalOffset + this._options.offset}px`,
        [oppositeY]: 'unset',
        left: '50%',
        transform: 'translate(-50%)',
      });
    } else {
      Manipulator.style(this._element, {
        [this.position.y]: `${this.verticalOffset + this._options.offset}px`,
        [this.position.x]: `${this._options.offset}px`,
        [oppositeY]: 'unset',
        [oppositeX]: 'unset',
        transform: 'unset',
      });
    }
  }

  _setupPosition() {
    if (this._options.container) {
      Manipulator.addClass(this.parent, 'parent-alert-relative');
      Manipulator.addClass(this._element, 'alert-absolute');
    } else {
      Manipulator.addClass(this._element, 'alert-fixed');
    }
  }

  _appendToBody() {
    this._element.parentNode.removeChild(this._element);
    document.body.appendChild(this._element);
  }

  _getConfig(options) {
    const config = {
      ...Default,
      ...Manipulator.getDataAttributes(this._element),
      ...options,
    };
    typeCheckConfig(NAME, config, DefaultType);
    return config;
  }
}

/**
 * ------------------------------------------------------------------------
 * Data Api implementation - auto initialization
 * ------------------------------------------------------------------------
 */

SelectorEngine.find(SELECTOR_ALERT).forEach((alert) => {
  let instance = Alert.getInstance(alert);
  if (!instance) {
    instance = new Alert(alert);
  }
  return instance;
});

/**
 * ------------------------------------------------------------------------
 * jQuery
 * ------------------------------------------------------------------------
 */

const $ = getjQuery();

if ($) {
  const JQUERY_NO_CONFLICT = $.fn[NAME];
  $.fn[NAME] = Alert.jQueryInterface;
  $.fn[NAME].Constructor = Alert;
  $.fn[NAME].noConflict = () => {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Alert.jQueryInterface;
  };
}

export default Alert;
