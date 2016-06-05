<?php
/**
 * Theme related functions. 
 *
 */
 
/**
 * Get title for the webpage by concatenating page specific title with site-wide title.
 *
 * @param string $title for this page.
 * @return string/null wether the favicon is defined or not.
 */
function get_title($title) {
  global $mithridates;
  return $title . (isset($mithridates['title_append']) ? $mithridates['title_append'] : null);
}

/**
 * Get manu for the webpage by concatenating nav-element with links from menu-items array.
 *
 * 
 * 
 */
function generateMenu($items, $class) {
  $html = "<nav class='$class'>\n";
  foreach($items as $item) {
    $selected = basename($_SERVER['PHP_SELF']) == $item['url'] ? 'selected' : null;
    $html .= "<a href='{$item['url']}' class='{$selected}'>{$item['text']}</a>\n";
  }
  $html .= "</nav>\n";
  return $html;
}
