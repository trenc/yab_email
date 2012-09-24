<?php

// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ("abc" is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'yab_email';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 1;

$plugin['version'] = '0.6';
$plugin['author'] = 'Tommy Schmucker';
$plugin['author_uri'] = 'http://www.yablo.de/';
$plugin['description'] = 'Simple Email Obfuscator/Defuscator (jQuery based)';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
$plugin['order'] = '5';

// Plugin 'type' defines where the plugin is loaded
// 0 = public       : only on the public side of the website (default)
// 1 = public+admin : on both the public and admin side
// 2 = library      : only when include_plugin() or require_plugin() is called
// 3 = admin        : only on the admin side
$plugin['type'] = '0';

// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

$plugin['flags'] = '0';

if (!defined('txpinterface'))
        @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
/**
 * This plugin is released under the GNU General Public License Version 2 and above
 * Version 2: http://www.gnu.org/licenses/gpl-2.0.html
 * Version 3: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Creates an obfuscated email adres
 *
 * @param array $atts
 * @return string
 */
function yab_email($atts)
{
	extract(
		lAtts(
			array(
				'email' => 'name@example.com',
				'text' => '',
				'class' => 'yab-email-link',
				'at' => 'at',
				'link' => 1,
				'set_js' => 1
			),$atts
		)
	);

	$tmp = explode('@', $email);
	$email = yab_email_encode($tmp[0]).'(%20'.$at.'%20)'.yab_email_encode($tmp[1]);

	if ($text)
	{
		$out = tag($text, 'a', ' class="'.$class.'" href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;'.$email.'"');
	}
	else
	{
		$out = tag($email, 'span', ' class="'.$class.'"');
	}

	if ($set_js)
	{
		return $out.n.yab_email_javascript(array('class' => $class, 'link' => $link), $set_js);
	}
	else
	{
		return $out;
	}
}

/**
 * Return jQuery Plugin »Email Defuscator«
 * jQuery Plugin »Email Defuscator« 1.0 alpha by Joakim Stai
 * <http://www.ia-stud.hiof.no/~joakims/projects/defuscator/>
 * Dual licensed under the MIT and GPL licenses:
 * <http://www.opensource.org/licenses/mit-license.php>
 * <http://www.gnu.org/licenses/gpl.html>
 * 24-09-12, modified for jQuery >= 1.8.0, Tommy Schmucker
 *
 * @param array class from $atts
 * @param boolean $set_js
 * @return string HTML element <script>
 */
function yab_email_javascript($atts, $set_js)
{
	extract(
		lAtts(
			array(
				'class' => 'yab-email-link',
				'link' => 1
			),$atts
		)
	);

	$js = <<<EOF
<script>
jQuery.fn.defuscate = function(settings){settings = jQuery.extend({link: true},settings);
var regex = /\b([A-Z0-9._%-]+)\([^)]+\)((?:[A-Z0-9-]+\.)+[A-Z]{2,6})\b/gi;
return this.each(function(){if($(this).is('a[href]')){ $(this).attr('href', $(this).attr('href').replace(regex, '$1@$2'));var is_link = true;}
$(this).html($(this).html().replace(regex, (settings.link && !is_link ? '<a href="mailto:$1@$2">$1@$2</a>' : '$1@$2')));});}
</script>
EOF;

	if ($set_js)
	{
		$js1 = <<<EOF0
<script>$('.$class').defuscate({link:$link});</script>
EOF0;
	}
	else
	{
		$js1 = <<<EOF0
<script>$(document).ready(function(){ $('.$class').defuscate({link:$link}); });</script>
EOF0;
	}

	return $js.$js1;
}

/**
 * Enocde chars to html entities
 *
 * @param string $string Chars to be encoded
 * @return string HTML encoded chars
 */
function yab_email_encode($string)
{
	$encode = '';
	for ($i = 0; $i < strlen($string);)
	{
		$encode .= '&#'.ord(substr($string,$i)).';';
		$i++;
	}
	return $encode;
}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
h1. yab_email

p. A simple email obfuscator/defuscator (jQuery based)

p. *Version:* 0.5

h2. Table of contents

# "Plugin requirements":#help-section02
# "Tags":#help-section05
# "Examples":#help-section09
# "License":#help-section10
# "Author contact":#help-section11

h2(#help-section02). Plugin requirements

p. yab_email’s minimum requirements:

* Textpattern 4.x
* jQuery 1.3.x

h2(#help-section05). Tags

h3. yab_email

p. Place this in your site to hide your email address from harvesters and bots.

*email:* a valid email address
Default: name@example.com
An email address, you want to be obfuscated.

*class:* class name
Default: yab-email-link
A class name for generated @<span />@ or @<a />@ element.

*at:* word between parenthesis
Default: at
A text string, that will be in parenthesis in obfuscated email.

*link:* boolean
Default: 1
Generate a link or show email as span element.

*text:* a link text
Default: __not set__
A text string, that will be the link text. Overwrite a @link="0"@.

*set_js:* boolean
Default: 1
Generate Javascript for this very tag.

h3. yab_email_javascript

p. If you have more email addresses you want to be obfuscated or your jQuery inlcude is at the bottom of your site, you can place this tag after the jQuery include.

*class:* class name
Default: yab-email-link
A class name for generated @<span />@ or @<a />@ element. Has to be the same as in @<txp:yab_email />@

*link:* boolean
Default: 1
Generate a link or show as span element.

h2(#help-section09). Examples

h3. Example 1

Example with a jQuery inlcude in the head and only one address to obfuscate.

bc.. <!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Your tilte</title>
	<link rel="stylesheet" href="/path/to/your/stylesheet.css" />
	<script src="/path/to/your/jQuery.js"></script>
</head>
<body>

<p><txp:yab_email email="contact@example.com" /></p>

</body>
</html>

h3. Example 2

Example with a jQuery inlcude at the bottom and probably more address to obfuscate.

bc.. <!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Your tilte</title>
	<link rel="stylesheet" href="/path/to/your/stylesheet.css" />
</head>
<body>

<p><txp:yab_email email="contact@example.com" set_js="0" /></p>
<p><txp:yab_email email="ceo@example.com" set_js="0"  /></p>

</body>
<script src="/path/to/your/jQuery.js"></script>
<txp:yab_email_javascript />
</html>

h2(#help-section10). Licence

This plugin is released under the GNU General Public License Version 2 and above
* Version 2: "http://www.gnu.org/licenses/gpl-2.0.html":http://www.gnu.org/licenses/gpl-2.0.html
* Version 3: "http://www.gnu.org/licenses/gpl-3.0.html":http://www.gnu.org/licenses/gpl-3.0.html

h2(#help-section11). Author contact

* "Plugin on author's site":http://www.yablo.de/article/408/yab_email-email-obfuscate-defuscate-as-textpattern-plugin
* "Plugin on textpattern forum":http://forum.textpattern.com/viewtopic.php?id=31846
* "Plugin on textpattern.org":http://textpattern.org/plugins/1123/yab_email
# --- END PLUGIN HELP ---
-->
<?php
}
?>
