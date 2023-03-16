# NAME:

Snoopy - the PHP net client v2.0.0 (adapted to PHP 8.2)

## SYNOPSIS:

```php
$snoopy = new \Purc\Snoopy\Snoopy();

$snoopy->fetchtext('https://www.google.com/');
echo 'fetchtext: ' . print_r($snoopy->results, true);

$snoopy->fetchlinks('https://www.phpbuilder.com/');
echo 'fetchlinks: ' . print_r($snoopy->results, true);

$url = 'https://www.php.net/search.php';
$vars = [
    'show' => 'quickref',
    'pattern' => 'PHP',
];
$snoopy->submit($url, $vars);
echo 'submit: ' . print_r($snoopy->results, true);

$snoopy->fetchform('https://www.altavista.com');
echo 'fetchform: ' . print_r($snoopy->results, true);
```

## DESCRIPTION:

What is Snoopy?

Snoopy is a PHP class that simulates a web browser. It automates the
task of retrieving web page content and posting forms, for example.

Some of Snoopy's features:

* easily fetch the contents of a web page
* easily fetch the text from a web page (strip html tags)
* easily fetch the links from a web page
* supports proxy hosts
* supports basic user/pass authentication
* supports setting user_agent, referer, cookies and header content
* supports browser redirects, and controlled depth of redirects
* expands fetched links to fully qualified URLs (default)
* easily submit form data and retrieve the results
* supports following html frames (added v0.92)
* supports passing cookies on redirects (added v0.92)


## REQUIREMENTS:

Snoopy requires PHP with PCRE (Perl Compatible Regular Expressions),
and the OpenSSL extension for fetching HTTPS requests.

## CLASS METHODS:

### `fetch($uri)`

This is the method used for fetching the contents of a web page.
`$uri` is the fully qualified URL of the page to fetch.
The results of the fetch are stored in $this->results.
If you are fetching frames, then $this->results
contains each frame fetched in an array.

### `fetchtext($uri)`

This behaves exactly like `fetch()` except that it only returns
the text from the page, stripping out html tags and other
irrelevant data.

### `fetchform($uri)`

This behaves exactly like `fetch()` except that it only returns
the form elements from the page, stripping out html tags and other
irrelevant data.

### `fetchlinks($uri)`

This behaves exactly like `fetch()` except that it only returns
the links from the page. By default, relative links are
converted to their fully qualified URL form.

### `submit($uri, $formVars)`

This submits a form to the specified `$uri`. `$formVars` is an
array of the form variables to pass.

### `submittext($uri, $formVars)`

This behaves exactly like `submit()` except that it only returns
the text from the page, stripping out html tags and other
irrelevant data.

### `submitlinks($uri)`

This behaves exactly like `submit()` except that it only returns
the links from the page. By default, relative links are
converted to their fully qualified URL form.


## CLASS VARIABLES:    (default value in parenthesis)

```txt
$host             the host to connect to
$port             the port to connect to
$proxy_host       the proxy host to use, if any
$proxy_port       the proxy port to use, if any
                  proxy can only be used for http URLs, but not https
$agent            the user agent to masqerade as (Snoopy v0.1)
$referer          referer information to pass, if any
$cookies          cookies to pass if any
$rawheaders       other header info to pass, if any
$maxredirs        maximum redirects to allow. 0=none allowed. (5)
$offsiteok        whether or not to allow redirects off-site. (true)
$expandlinks      whether or not to expand links to fully qualified URLs (true)
$user             authentication username, if any
$pass             authentication password, if any
$accept           http accept types (image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, */*)
$error            where errors are sent, if any
$response_code    responde code returned from server
$headers          headers returned from server
$maxlength        max return data length
$read_timeout     timeout on read operations (requires PHP 4 Beta 4+)
                  set to 0 to disallow timeouts
$timed_out        true if a read operation timed out (requires PHP 4 Beta 4+)
$maxframes        number of frames we will follow
$status           http status of fetch
$temp_dir         temp directory that the webserver can write to. (/tmp)
$curl_path        system path to cURL binary, set to false if none
                  (this variable is ignored as of Snoopy v1.2.6)
$cafile           name of a file with CA certificate(s)
$capath           name of a correctly hashed directory with CA certificate(s)
                  if either $cafile or $capath is set, SSL certificate
                  verification is enabled
```

## EXAMPLES:

### Example: fetch a web page and display the return headers and the contents of the page (html-escaped):

```php
$snoopy = new \Purc\Snoopy\Snoopy();

$snoopy->user = "joe";
$snoopy->pass = "bloe";

if ($snoopy->fetch("https://www.slashdot.org/"))
{
    echo "response code: " . $snoopy->response_code . "<br>\n";
    while (list($key, $val) = each($snoopy->headers))
    {
        echo $key . ": " . $val . "<br>\n";
    }
    echo "<p>\n";

    echo "<pre>" . htmlspecialchars($snoopy->results) . "</pre>\n";
}
else
{
    echo "error fetching document: " . $snoopy->error . "\n";
}
```


### Example:    submit a form and print out the result headers and html-escaped page:

```php
$snoopy = new \Purc\Snoopy\Snoopy();

$submit_url = "https://lnk.ispi.net/texis/scripts/msearch/netsearch.html";

$submit_vars["q"] = "amiga";
$submit_vars["submit"] = "Search!";
$submit_vars["searchhost"] = "Altavista";

if ($snoopy->submit($submit_url, $submit_vars))
{
    while (list($key, $val) = each($snoopy->headers))
    {
        echo $key . ": " . $val . "<br>\n";
    }
    echo "<p>\n";

    echo "<pre>" . htmlspecialchars($snoopy->results) . "</pre>\n";
}
else
{
    echo "error fetching document: " . $snoopy->error . "\n";
}
```


### Example:    showing functionality of all the variables:

```php
$snoopy = new \Purc\Snoopy\Snoopy();

$snoopy->proxy_host = "my.proxy.host";
$snoopy->proxy_port = "8080";

$snoopy->agent = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows 98)";
$snoopy->referer = "https://www.microsnot.com/";

$snoopy->cookies["SessionID"] = 238472834723489l;
$snoopy->cookies["favoriteColor"] = "RED";

$snoopy->rawheaders["Pragma"] = "no-cache";

$snoopy->maxredirs = 2;
$snoopy->offsiteok = false;
$snoopy->expandlinks = false;

$snoopy->user = "joe";
$snoopy->pass = "bloe";

if ($snoopy->fetchtext("https://www.phpbuilder.com"))
{
    while (list($key, $val) = each($snoopy->headers))
    {
        echo $key . ": " . $val . "<br>\n";
    }
    echo "<p>\n";

    echo "<pre>" . htmlspecialchars($snoopy->results) . "</pre>\n";
}
else
{
    echo "error fetching document: " . $snoopy->error . "\n";
}
```


### Example: fetched framed content and display the results

```php
$snoopy = new \Purc\Snoopy\Snoopy();

$snoopy->maxframes = 5;

if ($snoopy->fetch("https://www.ispi.net/"))
{
    echo "<pre>" . htmlspecialchars($snoopy->results[0]) . "</pre>\n";
    echo "<pre>" . htmlspecialchars($snoopy->results[1]) . "</pre>\n";
    echo "<pre>" . htmlspecialchars($snoopy->results[2]) . "</pre>\n";
}
else
{
    echo "error fetching document: " . $snoopy->error . "\n";
}
```


## COPYRIGHT:

Copyright(c) 1999,2000 ispi. All rights reserved.
This software is released under the GNU General Public License.
Please read the disclaimer at the top of the Snoopy.class.php file.


## THANKS:

Special Thanks to:
* Peter Sorger <sorgo@cool.sk> help fixing a redirect bug
* Andrei Zmievski <andrei@ispi.net> implementing time out functionality
* Patric Sandelin <patric@kajen.com> help with fetchform debugging
* Carmelo <carmelo@meltingsoft.com> misc bug fixes with frames
