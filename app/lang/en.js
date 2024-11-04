if (typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
  console.error('Class ss.i18n not defined');
} else {
  ss.i18n.addDictionary('en', {
    "LinkField.ARCHIVE": "Clear",
    "LinkField.DELETE": "Clear",
    "LinkField.SAVE_RECORD_FIRST": "You can create a link after you save this item for the first time"
  });
}
