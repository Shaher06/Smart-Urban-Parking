<?php
/**
 * URL HELPERS
 *
 * FIX: upload_url() now correctly maps the relative stored path to
 *      a browser-accessible URL under BASE_URL/uploads/.
 *
 *      The physical path is:  UPLOAD_PATH/{subdir}/{file}
 *      e.g.  /var/www/Smart_Parking/src/public/uploads/profile_images/file_abc.jpg
 *
 *      The web URL must be:   BASE_URL/uploads/{subdir}/{file}
 *      e.g.  /Smart_Parking/src/public/uploads/profile_images/file_abc.jpg
 *
 *      UploadService stores the relative path as: "profile_images/file_abc.jpg"
 *      So upload_url('profile_images/file_abc.jpg') returns the full web URL.
 */

/**
 * Return an absolute URL with BASE_URL prefix.
 *
 * @param string $path  Path relative to BASE_URL (no leading slash needed)
 * @return string
 */
function base_url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Build a URL to a page via index.php?page=...
 *
 * @param string $page    Route name (e.g. 'driver-dashboard')
 * @param array  $params  Additional GET parameters
 * @return string
 */
function page_url(string $page, array $params = []): string
{
    $url = BASE_URL . '/index.php?page=' . urlencode($page);
    foreach ($params as $key => $value) {
        $url .= '&' . urlencode($key) . '=' . urlencode($value);
    }
    return $url;
}

/**
 * Return URL to a static asset (CSS, JS, images in /assets/).
 *
 * @param string $path  Relative path under assets/ (e.g. 'css/style.css')
 * @return string
 */
function asset_url(string $path): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

/**
 * Return a browser-accessible URL for an uploaded file.
 *
 * UploadService stores a relative path like: "profile_images/file_abc123.jpg"
 * This function converts that to:            BASE_URL/uploads/profile_images/file_abc123.jpg
 *
 * FIX: Added null/empty check — returns placeholder if path is empty.
 *
 * @param string|null $relativePath  As stored in the database
 * @return string                    Full web URL
 */
function upload_url(?string $relativePath): string
{
    if (empty($relativePath)) {
        // Return a default placeholder avatar
        return BASE_URL . '/assets/img/default-avatar.png';
    }
    return BASE_URL . '/uploads/' . ltrim($relativePath, '/');
}