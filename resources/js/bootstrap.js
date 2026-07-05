import Alpine from "alpinejs";
window.Alpine = Alpine;

import Swal from "sweetalert2";
window.Swal = Swal;

import { saveAs } from 'file-saver';

import axios from "axios";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
