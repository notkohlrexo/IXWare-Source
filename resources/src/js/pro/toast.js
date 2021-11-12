import { getjQuery, typeCheckConfig, isVisible } from '../mdb/util/index';
import EventHandler from '../mdb/dom/event-handler';
import Manipulator from '../mdb/dom/manipulator';
import SelectorEngine from '../mdb/dom/selector-engine';
import BSToast from '../bootstrap/src/toast';
/**
 * ------------------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------------------
 */
const NAME = 'toast';
const SELECTOR_TOAST = '.toast';
const SELECTOR_HEADER = '.toast-header';

const DefaultType = {
  position: '(string|null)',
  animation: 'boolean',
  autohide: 'boolean',
  width: '(string || null)',
  color: '(string|null)',
  delay: '(boolean|number)',
  offset: 'number',
  appendToBody: 'boolean',
  stacking: 'boolean',
};

const Default = {
  position: null,
  animation: true,
  autohide: true,
  width: null,
  color: null,
  delay: 500,
  offset: 10,
  appendToBody: false,
  stacking: true,
};

class Toast extends BSToast {
  constructor(element, data = {}) {
    super(element, data);
    this._options = this._getConfig(data);
    this._setup();
  }

  // Getters

  get parent() {
    const [parent] = SelectorEngine.parents(this._element, this._options.container);
    return parent;
  }

  get position() {
    const [y, x] = this._options.position.split('-');
    return { y, x };
  }

  get verticalOffset() {
    if (!this._options.stacking) return 0;
    const offset = SelectorEngine.find(SELECTOR_TOAST)
      .filter((toast) => toast !== this._element && isVisible(toast))
      .map((toast) => ({ el: toast, instance: Toast.getInstance(toast) }))
      .filter(({ instance }) => {
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

  // Public

  update(updatedData = {}) {
    this._options = this._getConfig(updatedData);
    this._setupColor();
    if (!this._options.position) {
      return;
    }
    this._setupPosition();
    this._setupAlignment();
  }

  // Private

  _setup() {
    this._setupColor();
    if (this._options.width) {
      this._setupWidth();
    }
    if (!this._options.position) {
      return;
    }
    this._setupPosition();
    this._setupDisplay();
    if (!this._options.container && this._options.appendToBody) {
      this._appendToBody();
    }
  }

  _setupColor() {
    if (!this._options.color) {
      return;
    }

    const header = SelectorEngine.findOne(SELECTOR_HEADER, this._element);

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
      this._element.classList.remove(`bg-${color}`);
      if (header) header.classList.remove(`bg-${color}`);
    });

    Manipulator.addClass(this._element, `bg-${color}`);
    if (header) Manipulator.addClass(header, `bg-${color}`);
  }

  _setupWidth() {
    Manipulator.style(this._element, {
      width: this._options.width,
    });
  }

  _setupPosition() {
    if (this._options.container) {
      Manipulator.addClass(this.parent, 'parent-toast-relative');
      Manipulator.addClass(this._element, 'toast-absolute');
    } else {
      Manipulator.addClass(this._element, 'toast-fixed');
    }
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

  _setupDisplay() {
    if (!this._element.classList.contains('show')) {
      Manipulator.style(this._element, {
        display: 'none',
      });
    }

    EventHandler.on(this._element, 'hidden.bs.toast', () => {
      Manipulator.style(this._element, {
        display: 'none',
      });
    });
    EventHandler.on(this._element, 'show.bs.toast', () => {
      this._setupAlignment();
      Manipulator.style(this._element, {
        display: 'block',
      });
    });
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

  _appendToBody() {
    this._element.parentNode.removeChild(this._element);
    document.body.appendChild(this._element);
  }
}

/**
 * ------------------------------------------------------------------------
 * Data Api implementation - auto initialization
 * ------------------------------------------------------------------------
 */

SelectorEngine.find(SELECTOR_TOAST).forEach((toast) => {
  let instance = Toast.getInstance(toast);
  if (!instance) {
    instance = new Toast(toast);
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
  $.fn[NAME] = Toast.jQueryInterface;
  $.fn[NAME].Constructor = Toast;
  $.fn[NAME].noConflict = () => {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Toast.jQueryInterface;
  };
}

export default Toast;
