"use strict";

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

export const dictionary = {
  en: {
    attributes: en.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  ar: {
    attributes: ar.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  de: {
    attributes: de.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  es: {
    attributes: es.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  fr: {
    attributes: fr.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  it: {
    attributes: it.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  ms: {
    attributes: ms.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  no: {
    attributes: no.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  sv: {
    attributes: sv.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  th: {
    attributes: th.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  zh: {
    attributes: zh.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  nl: {
    attributes: nl.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  },
  pt: {
    attributes: pt.attributes,
    messages: {
      required: (field) => field+ ` is required`,
      email:  (field) => `Provide a valid `+field,
      min: (field, params) => field+ ` must be at least ${params[0]} characters`,
      max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
      min_value: (field, params) => field+ ` must be more than ${params[0]}`,
      between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
      confirmed: (field) => `Passwords doesn't match`,
    }
  }
};