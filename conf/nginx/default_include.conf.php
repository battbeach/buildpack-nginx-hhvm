location / {
    index  index.php index.html index.htm;
    try_files $uri $uri/ /index.php?q=$request_uri;
}

# for people with app root as doc root, restrict access to a few things
location ~ ^/(composer\.|Procfile$|<?=getenv('COMPOSER_VENDOR_DIR')?>/|<?=getenv('COMPOSER_BIN_DIR')?>/) {
    deny all;
}

# BEGIN BWP Minify WP Rules
# BEGIN BWP Minify Headers
location ~ /wp-content/plugins/bwp-minify/cache/.*\.(js|css)$ {
    add_header Cache-Control "public, max-age=86400";
    add_header Vary "Accept-Encoding";
    etag off;
}
location ~ /wp-content/plugins/bwp-minify/cache/.*\.js\.gz$ {
    gzip off;
    types {}
    default_type application/x-javascript;
    add_header Cache-Control "public, max-age=86400";
    add_header Content-Encoding gzip;
    add_header Vary "Accept-Encoding";
    etag off;
}
location ~ /wp-content/plugins/bwp-minify/cache/.*\.css\.gz$ {
    gzip off;
    types {}
    default_type text/css;
    add_header Cache-Control "public, max-age=86400";
    add_header Content-Encoding gzip;
    add_header Vary "Accept-Encoding";
    etag off;
}
# END BWP Minify Headers
set $zip_ext "";
if ($http_accept_encoding ~* gzip) {
    set $zip_ext ".gz";
}
set $minify_static "";
if ($http_cache_control = false) {
    set $minify_static "C";
    set $http_cache_control "";
}
if ($http_cache_control !~* no-cache) {
    set $minify_static "C";
}
if ($http_if_modified_since = false) {
    set $minify_static "${minify_static}M";
}
if (-f $request_filename$zip_ext) {
    set $minify_static "${minify_static}E";
}
if ($minify_static = CME) {
    rewrite (.*) $1$zip_ext break;
}
rewrite ^/wp-content/plugins/bwp-minify/cache/minify-b(\d+)-([a-zA-Z0-9-_.]+)\.(css|js)$ /index.php?blog=$1&min_group=$2&min_type=$3 last;

# END BWP Minify WP Rules
