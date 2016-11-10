# Testing

## TODO: Automated testing

At the moment, this extension does not have any automated tests. However, we
can still track manual test scripts.

## Manual Test: Tokens

This extension defines a TinyMCE plugin called "civicrmtoken".  To test this
plugin, create a mailing with a block of content. Then try each of the following:

 1. Edit a paragraph. In the toolbar, click the token dropdown and see a hotlist of 3-5 tokens. Pick one. Observe the new token in the paragraph.
 2. Edit a paragraph. In the toolbar, click the token icon and see a dialog. Pick one. Observe the new token in the paragraph.
 3. Edit a paragraph. Press Ctrl-Shift-T and see a dialog. Enter a filter and pick a token using the keyboard. Observe the new token in the paragraph.
 4. Edit a heading or button. Ensure that the the token icon/dropdown/hotkey work as expected. Observe the new token in the heading or button.
