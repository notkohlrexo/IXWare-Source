// BOOTSTRAP CORE COMPONENTS
import Button from './bootstrap/src/button';
import Carousel from './bootstrap/src/carousel';
import Collapse from './bootstrap/src/collapse';
import Modal from './bootstrap/src/modal';
import Popover from './bootstrap/src/popover';
import ScrollSpy from './bootstrap/src/scrollspy';
import Tab from './bootstrap/src/tab';
import Tooltip from './bootstrap/src/tooltip';

// MDB FREE COMPONENTS
import Input from './free/input';
import Dropdown from './free/dropdown';
import Ripple from './free/ripple';

// MDB PRO COMPONENTS
import Animate from './pro/animate';
import Chart from './pro/charts';
import Lightbox from './pro/lightbox';
import Rating from './pro/rating';
import Sidenav from './pro/sidenav';
import Alert from './pro/alert';
import Toast from './pro/toast';
import Timepicker from './pro/timepicker';
import Navbar from './pro/navbar';
import InfiniteScroll from './pro/infiniteScroll';
import LazyLoad from './pro/lazyLoad';
import Datepicker from './pro/datepicker';
import Popconfirm from './pro/popconfirm';
import Datatable from './pro/datatable/index';
import Stepper from './pro/stepper';
import Sticky from './pro/sticky';
import Select from './pro/select';
import Touch from './pro/touch';
import SmoothScroll from './pro/smooth-scroll';
import PerfectScrollbar from './pro/perfectScrollbar';
import Loading from './pro/loading-management/index';
import Autocomplete from './pro/autocomplete';

// AUTO INIT
[...document.querySelectorAll('[data-toggle="tooltip"]')].map((tooltip) => new Tooltip(tooltip));
[...document.querySelectorAll('[data-toggle="popover"]')].map((popover) => new Popover(popover));

export {
  // FREE
  Button,
  Carousel,
  Collapse,
  Dropdown,
  Input,
  Modal,
  Popover,
  ScrollSpy,
  Ripple,
  Tab,
  Tooltip,
  // PRO
  Alert,
  Animate,
  Chart,
  Datepicker,
  Datatable,
  Lightbox,
  Navbar,
  Popconfirm,
  Rating,
  Sidenav,
  SmoothScroll,
  Timepicker,
  Toast,
  InfiniteScroll,
  LazyLoad,
  Stepper,
  Sticky,
  Select,
  Touch,
  PerfectScrollbar,
  Loading,
  Autocomplete,
};
