<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {mailto} function plugin
 *
 * Type:     function<br>
 * Name:     mailto<br>
 * Date:     May 21, 2002
 * Purpose:  automate mailto address link creation, and optionally encode them.<br>
 * Params:
 * <pre>
 * - address    - (required) - e-mail address
 * - text       - (optional) - text to display, default is address
 * - encode     - (optional) - can be one of:
 *                             * none : no encoding (default)
 *                             * javascript : encode with javascript
 *                             * javascript_charcode : encode with javascript charcode
 *                             * hex : encode with hexidecimal (no javascript)
 * - cc         - (optional) - address(es) to carbon copy
 * - bcc        - (optional) - address(es) to blind carbon copy
 * - subject    - (optional) - e-mail subject
 * - newsgroups - (optional) - newsgroup(s) to post to
 * - followupto - (optional) - address(es) to follow up to
 * - extra      - (optional) - extra tags for the href link
 * </pre>
 * Examples:
 * <pre>
 * {mailto address="me@domain.com"}
 * {mailto address="me@domain.com" encode="javascript"}
 * {mailto address="me@domain.com" encode="hex"}
 * {mailto address="me@domain.com" subject="Hello to you!"}
 * {mailto address="me@domain.com" cc="you@domain.com,they@domain.com"}
 * {mailto address="me@domain.com" extra='class="mailto"'}
 * </pre>
 *
 * @link http://www.smarty.net/manual/en/language.function.mailto.php {mailto}
 *          (Smarty online manual)
 * @version 1.2
 * @author Monte Ohrt <monte at ohrt dot com>
 * @author credits to Jason Sweat (added cc, bcc and subject functionality)
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 * @return string
 */
function smarty_function_breadcrumb($params, $template)
{
    $html = '';
    if (!$params['args']) {
        return $html;
    }

    $args = $params['args'];

    if ($args['baseGroup']) {
        $html .= '<i class="ico-nav ico-nav-'.$args['baseGroup'].'"></i>';
    }

    if ($args['urls']) {
        $urlArr = array();
        $total = count($args['urls']);
        foreach ($args['urls'] as $k => $url) {
            if ($k == ($total - 1)) {
                $urlArr[] = $url['title'];
            } else {
                $urlArr[] = '<span class="lnk">'.$url['title'].'</span>';

                // if (!$url['url']) {
                //     $urlArr[] = '<span>'.$url['title'].'</span>';
                // } else {
                //     $urlArr[] = '<a href="'.$url['url'].'">'.$url['title'].'</a>';
                // }
            }
        }
        $html .= implode('<span class="dv">/</span>', $urlArr);
    }

    if ($html) {
        $html = '<div class="breadcrumb">' . $html . '</div>';
    }

    return $html;
}

?>
