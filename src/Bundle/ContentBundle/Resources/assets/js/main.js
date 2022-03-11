import $ from 'jquery';
jQuery = $;
global.$ = global.jQuery = $;
window.$ = window.jQuery = $;

import Handlebars from 'handlebars';
global.Handlebars = Handlebars;

import 'bootstrap-sass';

import 'bootstrap-sass/assets/javascripts/bootstrap';

import 'typeahead.js/dist/typeahead.jquery';

import Bloodhound from 'typeahead.js/dist/bloodhound';
global.Bloodhound = Bloodhound;

import 'jquery-placeholder';

import moment from 'moment';
global.moment = moment;

import 'select2/dist/js/select2.full';

import './scripts'
