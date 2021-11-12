import Popper from 'popper.js';
import { getjQuery, element, typeCheckConfig, getUID } from '../mdb/util/index';
import Data from '../mdb/dom/data';
import EventHandler from '../mdb/dom/event-handler';
import SelectorEngine from '../mdb/dom/selector-engine';
import Manipulator from '../mdb/dom/manipulator';
import { ESCAPE } from '../mdb/util/keycodes';

/**
 * ------------------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------------------
 */

const NAME = 'popconfirm';
const DATA_KEY = 'mdb.popconfirm';
const SELECTOR_POPCONFIRM = '.popconfirm-toggle';
const EVENT_KEY = `.${DATA_KEY}`;
const EVENT_CANCEL = `cancel${EVENT_KEY}`;
const EVENT_CONFIRM = `confirm${EVENT_KEY}`;

const DefaultType = {
  popconfirmMode: 'string',
  message: 'string',
  cancelText: 'string',
  okText: 'string',
  okClass: 'string',
  popconfirmIcon: 'string',
  cancelLabel: 'string',
  confirmLabel: 'string',
};

const Default = {
  popconfirmMode: 'inline',
  message: 'Are you sure?',
  cancelText: 'Cancel',
  okText: 'OK',
  okClass: 'btn-primary',
  popconfirmIcon: '',
  cancelLabel: 'Cancel',
  confirmLabel: 'Confirm',
};

/**
 * ------------------------------------------------------------------------
 * Class Definition
 * ------------------------------------------------------------------------
 */

class Popconfirm {
  constructor(element, options) {
    this._element = element;
    this._options = this._getConfig(options);
    this._cancelButtonTemplate = this._getCancelButtonTemplate();
    this._popper = null;
    this._cancelButton = '';
    this._confirmButton = '';
    this._isOpen = false;
    this._uid = getUID('popconfirm-');

    if (element) {
      Data.setData(element, DATA_KEY, this);
    }

    this._clickHandler = this.open.bind(this);
    EventHandler.on(this._element, 'click', this._clickHandler);
  }

  // Getters

  static get NAME() {
    return NAME;
  }

  get container() {
    return SelectorEngine.findOne(`#${this._uid}`);
  }

  // Public

  dispose() {
    if (this._isOpen || this.container !== null) {
      this.close();
    }
    Data.removeData(this._element, DATA_KEY);
    EventHandler.on(this._element, 'click', this._clickHandler);
    this._element = null;
  }

  open() {
    if (this._isOpen) {
      return;
    }
    if (this._options.popconfirmMode === 'inline') {
      this._openPopover(this._getPopoverTemplate());
    } else {
      this._openModal(this._getModalTemplate());
    }
    this._handleCancelButtonClick();
    this._handleConfirmButtonClick();
    this._listenToEscapeKey();
    this._isOpen = true;
    this._listenToOutsideClick();
  }

  close() {
    if (!this._isOpen) {
      return;
    }
    if (this._popper !== null || SelectorEngine.findOne('.popconfirm-popover') !== null) {
      this._popper.destroy();
      this._popper = null;
      document.body.removeChild(this.container);
    } else {
      const tempElement = SelectorEngine.findOne('.popconfirm-backdrop');
      document.body.removeChild(tempElement);
    }

    EventHandler.off(document, 'click', this._handleOutsideClick.bind(this));
    EventHandler.off(document, 'keydown', this._handleEscapeKey.bind(this));
    this._isOpen = false;
  }

  // Private

  _getPopoverTemplate() {
    const popover = element('div');
    const popconfirmTemplate = this._getPopconfirmTemplate();
    Manipulator.addClass(popover, 'popconfirm-popover');
    Manipulator.addClass(popover, 'shadow-3');
    popover.id = this._uid;
    popover.innerHTML = popconfirmTemplate;
    return popover;
  }

  _getModalTemplate() {
    const modal = element('div');
    const popconfirmTemplate = this._getPopconfirmTemplate();
    Manipulator.addClass(modal, 'popconfirm-modal');
    Manipulator.addClass(modal, 'shadow-3');
    modal.id = this._uid;
    modal.innerHTML = popconfirmTemplate;
    return modal;
  }

  _getPopconfirmTemplate() {
    return `<div class="popconfirm">
      <p class="popconfirm-message">
      ${this._getMessageIcon()}
      <span class="popconfirm-message-text">${this._options.message}</span>
      </p>
      <div class="popconfirm-buttons-container">
      ${this._cancelButtonTemplate}
      <button type="button" id="popconfirm-button-confirm" 
      aria-label="${this._options.confirmLabel}"
      class="btn ${this._options.okClass} btn-sm">${this._options.okText}</button>
      </div>
    </div>`;
  }

  _getConfig(config) {
    config = {
      ...Default,
      ...Manipulator.getDataAttributes(this._element),
      ...config,
    };
    typeCheckConfig(NAME, config, DefaultType);
    return config;
  }

  _getCancelButtonTemplate() {
    if (this._options.cancelText === '' || this._options.cancelText === ' ') {
      return '';
    }
    return `<button type="button" id="popconfirm-button-cancel" aria-label="${this._options.cancelLabel}" 
    class="btn btn-flat btn-sm">${this._options.cancelText}</button>`;
  }

  _getMessageIcon() {
    if (this._options.popconfirmIcon === '') {
      return '';
    }
    return `<span class="popconfirm-icon-container"><i class="${this._options.popconfirmIcon}"></i></span>`;
  }

  _openPopover(template) {
    this._popper = new Popper(this._element, template, {
      placement: this._translatePositionValue(),
    });
    document.body.appendChild(template);
  }

  _openModal(template) {
    const backdrop = element('div');
    Manipulator.addClass(backdrop, 'popconfirm-backdrop');
    document.body.appendChild(backdrop);
    backdrop.appendChild(template);
  }

  _handleCancelButtonClick() {
    const container = this.container;
    this._cancelButton = SelectorEngine.findOne('#popconfirm-button-cancel', container);
    if (this._cancelButton !== null) {
      EventHandler.on(this._cancelButton, 'click', () => {
        this.close();
        EventHandler.trigger(this._element, EVENT_CANCEL);
      });
    }
  }

  _handleConfirmButtonClick() {
    const container = this.container;
    this._confirmButton = SelectorEngine.findOne('#popconfirm-button-confirm', container);
    EventHandler.on(this._confirmButton, 'click', () => {
      this.close();
      EventHandler.trigger(this._element, EVENT_CONFIRM);
    });
  }

  _listenToEscapeKey() {
    EventHandler.on(document, 'keydown', this._handleEscapeKey.bind(this));
  }

  _handleEscapeKey(event) {
    if (event.keyCode === ESCAPE) {
      this.close();
    }
  }

  _listenToOutsideClick() {
    EventHandler.on(document, 'click', this._handleOutsideClick.bind(this));
  }

  _handleOutsideClick(event) {
    const container = this.container;
    const isContainer = event.target === container;
    const isContainerContent = container && container.contains(event.target);
    const isElement = event.target === this._element;
    const isElementContent = this._element && this._element.contains(event.target);
    if (!isContainer && !isContainerContent && !isElement && !isElementContent) {
      this.close();
    }
  }

  _translatePositionValue() {
    switch (this._options.position) {
      // left, right as default
      case 'top left':
        return 'top-end';
      case 'top':
        return 'top';
      case 'top right':
        return 'top-start';
      case 'bottom left':
        return 'bottom-end';
      case 'bottom':
        return 'bottom';
      case 'bottom right':
        return 'bottom-start';
      case 'left top':
        return 'left-end';
      case 'left bottom':
        return 'left-start';
      case 'right top':
        return 'right-end';
      case 'right bottom':
        return 'right-start';
      case undefined:
        return 'bottom';
      default:
        return this._options.position;
    }
  }

  // Static

  static jQueryInterface(config, options) {
    return this.each(function () {
      const data = Data.getData(this, DATA_KEY);
      const _config = typeof config === 'object' && config;

      if (!data && /dispose/.test(config)) {
        return;
      }

      if (!data) {
        // eslint-disable-next-line consistent-return
        return new Popconfirm(this, _config);
      }

      if (typeof config === 'string') {
        if (typeof data[config] === 'undefined') {
          throw new TypeError(`No method named "${config}"`);
        }

        data[config](options);
      }
    });
  }

  static getInstance(element) {
    return Data.getData(element, DATA_KEY);
  }
}

/**
 * ------------------------------------------------------------------------
 * Data Api implementation - auto initialization
 * ------------------------------------------------------------------------
 */

SelectorEngine.find(SELECTOR_POPCONFIRM).forEach((el) => {
  let instance = Popconfirm.getInstance(el);
  if (!instance) {
    instance = new Popconfirm(el);
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
  $.fn[NAME] = Popconfirm.jQueryInterface;
  $.fn[NAME].Constructor = Popconfirm;
  $.fn[NAME].noConflict = () => {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Popconfirm.jQueryInterface;
  };
}

export default Popconfirm;
