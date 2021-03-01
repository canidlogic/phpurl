<?php

/*
 * phpurl.php
 * ==========
 * 
 * The PHPURL script.  See the PHPURL specification for further
 * information.
 * 
 * To deploy this script, you just need to define a JSON data file in
 * the appropriate format (as described in the specification), and set
 * the following variable to the location of the data file on the
 * server, as well as uncommenting the definition:
 */
// $DATAFILE_PATH = '/home/user/example.json';
/*
 * If you wish to customize the error pages and the temporarily
 * unavailable page, change the embedded HTML pages below:
 */

/*
 * Resource temporarily unavailable.
 * 
 * This function does not return.
 */
function resourceUnavailable() {
  http_response_code(200);
  header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <title>Temporarily Unavailable</title>
    <meta name="viewport"
      content="width=device-width, initial-scale=1.0"/>
    <style>

body {
  text-align: center;
  font-family: sans-serif;
}

    </style>
  </head>
  <body>
    <h1>Temporarily Unavailable</h1>
    <p>Please try again later.</p>
  </body>
</html>
<?php
  exit;
}

/*
 * HTTP 404: Not Found
 * 
 * This function does not return.
 */
function httpNotFound() {
  http_response_code(404);
  header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <title>Not Found</title>
    <meta name="viewport"
      content="width=device-width, initial-scale=1.0"/>
    <style>

body {
  text-align: center;
  font-family: sans-serif;
}

    </style>
  </head>
  <body>
    <h1>Not Found</h1>
    <p>The requested resource could not be found.</p>
  </body>
</html>
<?php
  exit;
}

/*
 * HTTP 500: Internal Server Error
 * 
 * This function does not return.
 */
function httpError() {
  http_response_code(500);
  header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <title>Internal Server Error</title>
    <meta name="viewport"
      content="width=device-width, initial-scale=1.0"/>
    <style>

body {
  text-align: center;
  font-family: sans-serif;
}

    </style>
  </head>
  <body>
    <h1>Internal Server Error</h1>
    <p>Please try again later.</p>
  </body>
</html>
<?php
  exit;
}

/*
 * HTTP 503: Service Unavailable
 * 
 * This function does not return.
 */
function httpNoDeploy() {
  http_response_code(503);
  header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <title>Service Unavailable</title>
    <meta name="viewport"
      content="width=device-width, initial-scale=1.0"/>
    <style>

body {
  text-align: center;
  font-family: sans-serif;
}

    </style>
  </head>
  <body>
    <h1>Service Unavailable</h1>
    <p>This script has not been properly deployed.</p>
  </body>
</html>
<?php
  exit;
}

/*
 * Check that DATAFILE_PATH is defined
 * -----------------------------------
 */
if (is_null($DATAFILE_PATH)) {
  httpNoDeploy();
}

/*
 * Load the data file
 * ------------------
 */
$raw_file = file_get_contents($DATAFILE_PATH);
if (!is_string($raw_file)) {
  httpError();
}
$parse_data = json_decode($raw_file, true);
if (is_null($parse_data)) {
  httpError();
}
$raw_file = null;

/*
 * Get the link variable value
 * ---------------------------
 */
$link_var = $_GET['link'];
if (!is_string($link_var)) {
  httpNotFound();
}
$link_var = trim($link_var);
if (strlen($link_var) < 1) {
  httpNotFound();
}

/*
 * Look up the link value in the data
 * ----------------------------------
 */
$link_target = $parse_data[$link_var];
if (!is_string($link_target)) {
  httpNotFound();
}

/*
 * Determine type of link
 * ----------------------
 */
if (strpos($link_target, "phpurl://") === 0) {
  /* Special domain */
  if ($link_target === "phpurl://offline") {
    /* Temporarily unavailable */
    resourceUnavailable();
    
  } else {
    /* Unrecognized special domain */
    httpError();
  }
  
} else if ((strpos($link_target, "http://") === 0) ||
            (strpos($link_target, "https://") === 0)) {
  /* HTTP(S) link, so redirect there */
  http_response_code(302);
  header("Location: $link_target");
  exit;
  
} else {
  /* Unrecognized target type */
  httpError();
}

/* We shouldn't get here */
httpError();
exit;

?>
