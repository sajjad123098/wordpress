/**
 * Debounce for preventing multiple calls.
 *
 * @param {function} func
 * @param {int} wait
 */
export const debounce = function ( func, wait ) {
  let timeout;
  return function () {
    const context = this,
      args = arguments;
    const later = function () {
      timeout = null;
      func.apply( context, args );
    };
    clearTimeout( timeout );
    timeout = setTimeout( later, wait );
  };
}

/**
 * Http get request.
 *
 * @param {string} theUrl
 * @param {callback} callback
 * @param {Object} params
 */
export const httpGetAsync = (theUrl, callback, params) => {
  const xmlHttp = new XMLHttpRequest();
  xmlHttp.onload = () => {
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200)
      callback(xmlHttp.response);
  };
  xmlHttp.onerror = () => {};
  xmlHttp.open('POST', theUrl, true);
  xmlHttp.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
  xmlHttp.send(params);
};
