"use strict";

import Vue from 'vue';
import VueI18n from 'vue-i18n';

Vue.use(VueI18n);

/* import available lang files to vue */
export const language = window.settings.language;

import en from '../../lang/en.json';
import ar from '../../lang/ar.json';
import de from '../../lang/de.json';
import es from '../../lang/es.json';
import fr from '../../lang/fr.json';
import it from '../../lang/it.json';
import ms from '../../lang/ms.json';
import no from '../../lang/no.json';
import sv from '../../lang/sv.json';
import th from '../../lang/th.json';
import zh from '../../lang/zh.json';
import nl from '../../lang/nl.json';
import pt from '../../lang/pt.json';

/* import end */

export var messages = {
    'en' : en,
    'ar' : ar,
    'de' : de,
    'es' : es,
    'fr' : fr,
    'it' : it,
    'ms' : ms,
    'no' : no,
    'sv' : sv,
    'th' : th,
    'zh' : zh,
    'nl' : nl,
    'pt' : pt,
}

// Create VueI18n instance with options
export const i18n = new VueI18n({
    locale: language, // set locale
    messages, // set locale messages
    silentTranslationWarn : true,
}); 

