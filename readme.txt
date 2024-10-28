=== Plugin Name ===
Contributors: mandsconsulting
Donate link: mandsconsulting.com

Tags: comments, spam, links
Stable tag: 4.3
License: GPLv2
Tested up to: 4.1

Remove the URL field from the comments form to prevent linkback spam.

== Description ==


Some commentors try to abuse the Wordpress Comment's built in URL feature by submitting advertising, spam, or otherwise unwanted links.

Avoid Linkback Abuse tries to prevent this by providing two different modes of operation:
 
 * Strip all anchors from comment author's names as they appear
        in the comments list at the end of posts (<b>always on when 
        the plugin is activated</b>).
* In addition to the above, this mode will remove
        entirely the ability for commentors to leave a URL.
        Assuming you're using the default WP Comments plugin
        without any additional comment-modifying plugins, this
        mode still allows commentors to leave their name and email.


== Installation ==

1. Upload ‘ala’ folder to the /wp-content/plugins/ directory.
1. Activate plugin from plugin directory. 


== Frequently Asked Questions ==

= What does this do? =

This plugin simply removes the ability for users to provide a URL on comments to prevent link back spam. 


= Does this affect existing comments? =

This will remove the ability to click on a comment author's URL, but if you want to remove the URLs entirely, that will have to be removed manually.

== Screenshots ==


1. Comment section with ALA. No Website field.

== Changelog ==

=1.0=

* Initial release
