import './bootstrap';
import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
Chart.defaults.font.family = "'Instrument Sans', ui-sans-serif, system-ui, sans-serif";
Chart.defaults.font.size = 12;
window.Chart = Chart;

window.Alpine = Alpine;
Alpine.start();
