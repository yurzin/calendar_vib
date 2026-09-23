// Адреса главного сайта и админки вычисляются из текущего хоста браузера,
// а не хардкодятся — домен уже менялся несколько раз (calendar.local →
// властьибизнес.local), и хардкод каждый раз тихо ломал редиректы.

function buildUrl(hostname) {
  const { protocol, port } = window.location;
  return `${protocol}//${hostname}${port ? ':' + port : ''}`;
}

export function getMainSiteUrl() {
  return buildUrl(window.location.hostname.replace(/^admin\./, ''));
}

export function getAdminSiteUrl() {
  const { hostname } = window.location;
  return buildUrl(hostname.startsWith('admin.') ? hostname : `admin.${hostname}`);
}
