<?php
/**
 * Filter and format content.
 *
 */
class CTextFilter
{
    /**
     * Call each filter.
     *
     * @param string $text    the text to filter.
     * @param string $filters as comma separated list of filter.
     *
     * @return string the formatted text.
     */
    public function doFilter($text, $filters)
    {
        // Define all valid filters with their callback function.
        $callbacks = array(
            'bbcode'    => 'bbcode2html',
            'clickable' => 'makeClickable',
            'markdown'  => 'markdown',
            'nl2br'     => 'nl2br',
            'shortcode' => 'shortCode',
        );
        // Make an array of the comma separated string $filters
        $filter = preg_replace('/\s/', '', explode(',', $filters));
        // For each filter, call its function with the $text as parameter.
        foreach ($filter as $key) {
            if (isset($callbacks[$key])) {
                $text = call_user_func_array(array($this, $callbacks[$key]), array($text));
            } else {
                throw new Exception("The filter '$filters' is not a valid filter string. Its the key '$key' that is unknown and it does not match a valid callback.");
            }
        }
        return $text;
    }
    /**
     * Helper, BBCode formatting converting to HTML.
     *
     * @param string $text The text to be converted.
     *
     * @return string the formatted text.
     *
     * @link http://dbwebb.se/coachen/reguljara-uttryck-i-php-ger-bbcode-formattering
     */
    public function bbcode2html($text)
    {
        $search = array(
            '/\[b\](.*?)\[\/b\]/is',
            '/\[i\](.*?)\[\/i\]/is',
            '/\[u\](.*?)\[\/u\]/is',
            '/\[img\](https?.*?)\[\/img\]/is',
            '/\[url\](https?.*?)\[\/url\]/is',
            '/\[url=(https?.*?)\](.*?)\[\/url\]/is'
        );
        $replace = array(
            '<strong>$1</strong>',
            '<em>$1</em>',
            '<u>$1</u>',
            '<img src="$1" />',
            '<a href="$1">$1</a>',
            '<a href="$1">$2</a>'
        );
        return preg_replace($search, $replace, $text);
    }
    /**
     * Make clickable links from URLs in text.
     *
     * @param string $text the text that should be formatted.
     *
     * @return string with formatted anchors.
     *
     * @link http://dbwebb.se/coachen/lat-php-funktion-make-clickable-automatiskt-skapa-klickbara-lankar
     */
    public function makeClickable($text)
    {
        return preg_replace_callback(
            '#\b(?<![href|src]=[\'"])https?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#',
            function ($matches) {
                return "<a href='{$matches[0]}'>{$matches[0]}</a>";
            },
            $text
        );
    }
    /**
     * Format text according to Markdown syntax.
     *
     * @param string $text the text that should be formatted.
     *
     * @return string as the formatted html-text.
     *
     * @link http://dbwebb.se/coachen/skriv-for-webben-med-markdown-och-formattera-till-html-med-php
     */
    public function markdown($text)
    {
        require_once __DIR__ . "/php-markdown/Michelf/MarkdownExtra.inc.php";
        return \Michelf\MarkdownExtra::defaultTransform($text);
    }
    /**
     * For convenience access to nl2br
     *
     * @param string $text text to be converted.
     *
     * @return string the formatted text.
     */
    public function nl2br($text)
    {
        return nl2br($text);
    }
    /**
     * Shortcode to to quicker format text as HTML.
     *
     * @param string $text text to be converted.
     *
     * @return string the formatted text.
     */
    public function shortCode($text)
    {
        $patterns = array(
            '/\[(FIGURE)[\s+](.+)\]/',
        );
        return preg_replace_callback(
            $patterns,
            function ($matches) {
                switch ($matches[1]) {
                    case 'FIGURE':
                        return CTextFilter::ShortCodeFigure($matches[2]);
                        break;
                    default:
                        return "{$matches[1]} is unknown shortcode.";
                }
            },
            $text
        );
    }
    /**
    * Init shortcode handling by preparing the option list to an array, for those using arguments.
    *
    * @param string $options for the shortcode.
    *
    * @return array with all the options.
    */
    public static function shortCodeInit($options)
    {
        preg_match_all('/[a-zA-Z0-9]+="[^"]+"|\S+/', $options, $matches);
        $res = array();
        foreach ($matches[0] as $match) {
            $pos = strpos($match, '=');
            if ($pos == false) {
                $res[$match] = true;
            } else {
                $key = substr($match, 0, $pos);
                $val = trim(substr($match, $pos+1), '"');
                $res[$key] = $val;
            }
        }
        return $res;
    }
    /**
     * Shortcode for <figure>.
     *
     * Usage example: [FIGURE src="img/home/me.jpg" caption="Me" alt="Bild på mig" nolink="nolink"]
     *
     * @param string $options for the shortcode.
     *
     * @return array with all the options.
     */
    public static function shortCodeFigure($options)
    {
        extract(
            array_merge(
                array(
                    'id' => null,
                    'class' => null,
                    'src' => null,
                    'title' => null,
                    'alt' => null,
                    'caption' => null,
                    'href' => null,
                    'nolink' => false,
                ),
                CTextFilter::ShortCodeInit($options)
            ),
            EXTR_SKIP
        );
        $id = $id ? " id='$id'" : null;
        $class = $class ? " class='figure $class'" : " class='figure'";
        $title = $title ? " title='$title'" : null;
        if (!$alt && $caption) {
            $alt = $caption;
        }
        if (!$href) {
            $pos = strpos($src, '?');
            $href = $pos ? substr($src, 0, $pos) : $src;
        }
        $a_start = null;
        $a_end = null;
        if (!$nolink) {
            $a_start = "<a href='{$href}'>";
            $a_end = "</a>";
        }
        $html = <<<EOD
<figure{$id}{$class}>
{$a_start}<img src='{$src}' alt='{$alt}'{$title}/>{$a_end}
<figcaption markdown=1>{$caption}</figcaption>
</figure>
EOD;
        return $html;
    }
}
