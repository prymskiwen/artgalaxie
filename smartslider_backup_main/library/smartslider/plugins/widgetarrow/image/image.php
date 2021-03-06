<?php

N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');

class N2SSPluginWidgetArrowImage extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-arrow-';

    var $_name = 'image';

    static function getDefaults() {
        return array(
            'widget-arrow-responsive-desktop'       => 1,
            'widget-arrow-responsive-tablet'        => 0.7,
            'widget-arrow-responsive-mobile'        => 0.5,
            'widget-arrow-previous-image'           => '',
            'widget-arrow-previous'                 => '$ss$/plugins/widgetarrow/image/image/previous/normal.svg',
            'widget-arrow-previous-color'           => 'ffffffcc',
            'widget-arrow-previous-hover'           => 0,
            'widget-arrow-previous-hover-color'     => 'ffffffcc',
            'widget-arrow-style'                    => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiMjB8KnwxMHwqfDIwfCp8MTB8KnxweCIsImJveHNoYWRvdyI6IjB8KnwwfCp8MHwqfDB8KnwwMDAwMDBmZiIsImJvcmRlciI6IjB8Knxzb2xpZHwqfDAwMDAwMGZmIiwiYm9yZGVycmFkaXVzIjoiNSIsImV4dHJhIjoiIn0seyJiYWNrZ3JvdW5kY29sb3IiOiIwMDAwMDBjZiJ9XX0=',
            'widget-arrow-previous-position-mode'   => 'simple',
            'widget-arrow-previous-position-area'   => 6,
            'widget-arrow-previous-position-offset' => 15,
            'widget-arrow-next-position-mode'       => 'simple',
            'widget-arrow-next-position-area'       => 7,
            'widget-arrow-next-position-offset'     => 15,
            'widget-arrow-animation'                => 'fade',
            'widget-arrow-mirror'                   => 1,
            'widget-arrow-next-image'               => '',
            'widget-arrow-next'                     => '$ss$/plugins/widgetarrow/image/image/next/normal.svg',
            'widget-arrow-next-color'               => 'ffffffcc',
            'widget-arrow-next-hover'               => 0,
            'widget-arrow-next-hover-color'         => 'ffffffcc'
        );
    }


    function onArrowList(&$list) {
        $list[$this->_name] = $this->getPath();
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR;
    }

    static function getPositions(&$params) {
        $positions = array();

        if (self::isRenderable('previous', $params)) {
            $positions['previous-position'] = array(
                self::$key . 'previous-position-',
                'previous'
            );
        }

        if (self::isRenderable('next', $params)) {
            $positions['next-position'] = array(
                self::$key . 'next-position-',
                'next'
            );
        }

        return $positions;
    }

    private static function isRenderable($side, &$params) {
        $arrow = $params->get(self::$key . $side . '-image');
        if (empty($arrow)) {
            $arrow = $params->get(self::$key . $side);
            if ($arrow == -1) {
                $arrow = null;
            }
        }

        return !!$arrow;
    }

    static function render($slider, $id, $params) {
        $return = array();

        $previous           = $params->get(self::$key . 'previous-image');
        $previousColor      = $params->get(self::$key . 'previous-color');
        $previousHover      = $params->get(self::$key . 'previous-hover');
        $previousHoverColor = $params->get(self::$key . 'previous-hover-color');
        if (empty($previous)) {
            $previous = $params->get(self::$key . 'previous');

            if ($previous == -1) {
                $previous = null;
            } elseif ($previous[0] != '$') {
                $previous = N2Uri::pathToUri(dirname(__FILE__) . '/image/previous/' . $previous);
            }
        }

        if ($params->get(self::$key . 'mirror')) {
            $next           = str_replace('image/previous/', 'image/next/', $previous);
            $nextColor      = $previousColor;
            $nextHover      = $previousHover;
            $nextHoverColor = $previousHoverColor;
        } else {
            $next           = $params->get(self::$key . 'next-image');
            $nextColor      = $params->get(self::$key . 'next-color');
            $nextHover      = $params->get(self::$key . 'next-hover');
            $nextHoverColor = $params->get(self::$key . 'next-hover-color');
            if (empty($next)) {
                $next = $params->get(self::$key . 'next');
                if ($next == -1) {
                    $next = null;
                } elseif ($next[0] != '$') {
                    $next = N2Uri::pathToUri(dirname(__FILE__) . '/image/next/' . $next);
                }
            }
        }
        if ($previous || $next) {

            N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR . 'style.n2less'), $slider->cacheId, array(
                "sliderid" => $slider->elementId
            ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
            N2JS::addFile(N2Filesystem::translate(dirname(__FILE__) . '/image/arrow.min.js'), $id);
        

            list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

            $animation = $params->get(self::$key . 'animation');

            if ($animation == 'none' || $animation == 'fade') {
                $styleClass = N2StyleRenderer::render($params->get(self::$key . 'style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');
            } else {
                $styleClass = N2StyleRenderer::render($params->get(self::$key . 'style'), 'heading-active', $slider->elementId, 'div#' . $slider->elementId . ' ');
            }

            if ($previous) {
                $return['previous'] = self::getHTML($id, $params, $animation, 'previous', $previous, $displayClass, $displayAttributes, $styleClass, $previousColor, $previousHover, $previousHoverColor);
            }

            if ($next) {
                $return['next'] = self::getHTML($id, $params, $animation, 'next', $next, $displayClass, $displayAttributes, $styleClass, $nextColor, $nextHover, $nextHoverColor);
            }

            N2JS::addInline('new N2Classes.SmartSliderWidgetArrowImage("' . $id . '", ' . n2_floatval($params->get(self::$key . 'responsive-desktop')) . ', ' . n2_floatval($params->get(self::$key . 'responsive-tablet')) . ', ' . n2_floatval($params->get(self::$key . 'responsive-mobile')) . ');');
        }

        return $return;
    }

    private static function getHTML($id, &$params, $animation, $side, $image, $displayClass, $displayAttributes, $styleClass, $color = 'ffffffcc', $hover = 0, $hoverColor = 'ffffffcc') {

        $isNormalFlow = self::isNormalFlow($params, self::$key . $side . '-');
        list($style, $attributes) = self::getPosition($params, self::$key . $side . '-');

        $imageHover = null;

        $ext = pathinfo($image, PATHINFO_EXTENSION);
        if (substr($image, 0, 1) == '$' && $ext == 'svg') {
            list($color, $opacity) = N2Color::colorToSVG($color);
            $content = N2Filesystem::readFile(N2ImageHelper::fixed($image, true));
            $image   = 'data:image/svg+xml;base64,' . n2_base64_encode(str_replace(array(
                    'fill="#FFF"',
                    'opacity="1"'
                ), array(
                    'fill="#' . $color . '"',
                    'opacity="' . $opacity . '"'
                ), $content));

            if ($hover) {
                list($color, $opacity) = N2Color::colorToSVG($hoverColor);
                $imageHover = 'data:image/svg+xml;base64,' . n2_base64_encode(str_replace(array(
                        'fill="#FFF"',
                        'opacity="1"'
                    ), array(
                        'fill="#' . $color . '"',
                        'opacity="' . $opacity . '"'
                    ), $content));
            }
        } else {
            $image = N2ImageHelper::fixed($image);
        }

        if ($imageHover === null) {
            $image = N2Html::image($image, 'Arrow', array(
                'class'        => 'n2-ow',
                'data-no-lazy' => '1',
                'data-hack'    => 'data-lazy-src'
            ));
        } else {
            $image = N2Html::image($image, 'Arrow', array(
                    'class'        => 'n2-arrow-normal-img n2-ow',
                    'data-no-lazy' => '1',
                    'data-hack'    => 'data-lazy-src'
                )) . N2Html::image($imageHover, 'Arrow', array(
                    'class'        => 'n2-arrow-hover-img n2-ow',
                    'data-no-lazy' => '1',
                    'data-hack'    => 'data-lazy-src'
                ));
        }

        $label = '';
        switch ($side) {
            case 'previous':
                $label = 'Previous slide';
                break;
            case 'next':
                $label = 'Next slide';
                break;
        }

        if ($animation == 'none' || $animation == 'fade') {
            return N2Html::tag('div', $displayAttributes + $attributes + array(
                    'id'         => $id . '-arrow-' . $side,
                    'class'      => $displayClass . $styleClass . 'nextend-arrow n2-ow nextend-arrow-' . $side . '  nextend-arrow-animated-' . $animation . ' ' . ($isNormalFlow ? '' : 'n2-ib'),
                    'style'      => $style . ($isNormalFlow ? 'margin-left:auto;margin-right:auto;' : ''),
                    'role'       => 'button',
                    'aria-label' => $label,
                    'tabindex'   => '0'
                ), $image);
        }


        return N2Html::tag('div', $displayAttributes + $attributes + array(
                'id'         => $id . '-arrow-' . $side,
                'class'      => $displayClass . 'nextend-arrow nextend-arrow-animated n2-ow nextend-arrow-animated-' . $animation . ' nextend-arrow-' . $side . ' ' . ($isNormalFlow ? '' : 'n2-ib'),
                'style'      => $style . ($isNormalFlow ? 'margin-left:auto;margin-right:auto;' : ''),
                'role'       => 'button',
                'aria-label' => $label,
                'tabindex'   => '0'
            ), N2Html::tag('div', array(
                'class' => $styleClass . ' n2-resize'
            ), $image) . N2Html::tag('div', array(
                'class' => $styleClass . ' n2-active n2-resize'
            ), $image));
    }

    public static function prepareExport($export, $params) {
        $export->addImage($params->get(self::$key . 'previous-image', ''));
        $export->addImage($params->get(self::$key . 'next-image', ''));

        $export->addVisual($params->get(self::$key . 'style'));
    }

    public static function prepareImport($import, $params) {

        $params->set(self::$key . 'previous-image', $import->fixImage($params->get(self::$key . 'previous-image', '')));
        $params->set(self::$key . 'next-image', $import->fixImage($params->get(self::$key . 'next-image', '')));

        $params->set(self::$key . 'style', $import->fixSection($params->get(self::$key . 'style', '')));
    }
}
N2Plugin::addPlugin('sswidgetarrow', 'N2SSPluginWidgetArrowImage');

class N2SSPluginWidgetArrowImageBigRectangle extends N2SSPluginWidgetArrowImage {

    var $_name = 'imageBigRectangle';

    static function getDefaults() {
        return array_merge(N2SSPluginWidgetArrowImage::getDefaults(), array(
            'widget-arrow-style'                    => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiMjB8KnwyMHwqfDIwfCp8MjB8KnxweCIsImJveHNoYWRvdyI6IjB8KnwwfCp8MHwqfDB8KnwwMDAwMDBmZiIsImJvcmRlciI6IjB8Knxzb2xpZHwqfDAwMDAwMGZmIiwiYm9yZGVycmFkaXVzIjoiMCIsImV4dHJhIjoiIn0seyJiYWNrZ3JvdW5kY29sb3IiOiIwMGMxYzRmZiJ9XX0=',
            'widget-arrow-animation'                => 'horizontal',
            'widget-arrow-previous-position-offset' => 0,
            'widget-arrow-next-position-offset'     => 0,
        ));
    }
}

N2Plugin::addPlugin('sswidgetarrow', 'N2SSPluginWidgetArrowImageBigRectangle');



class N2SSPluginWidgetArrowImageSmallRectangle extends N2SSPluginWidgetArrowImage {

    var $_name = 'imageSmallRectangle';

    static function getDefaults() {
        return array_merge(N2SSPluginWidgetArrowImage::getDefaults(), array(
            'widget-arrow-responsive-desktop' => 0.8,
            'widget-arrow-previous'           => '$ss$/plugins/widgetarrow/image/image/previous/full.svg',
            'widget-arrow-next'               => '$ss$/plugins/widgetarrow/image/image/next/full.svg',
            'widget-arrow-style'              => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiMnwqfDJ8KnwyfCp8MnwqfHB4IiwiYm94c2hhZG93IjoiMHwqfDB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYm9yZGVyIjoiMHwqfHNvbGlkfCp8MDAwMDAwZmYiLCJib3JkZXJyYWRpdXMiOiIzIiwiZXh0cmEiOiIifSx7ImJhY2tncm91bmRjb2xvciI6IjAxYWRkM2Q5In1dfQ=='
        ));
    }
}

N2Plugin::addPlugin('sswidgetarrow', 'N2SSPluginWidgetArrowImageSmallRectangle');


class N2SSPluginWidgetArrowImageEmpty extends N2SSPluginWidgetArrowImage {

    var $_name = 'imageEmpty';

    static function getDefaults() {
        return array_merge(N2SSPluginWidgetArrowImage::getDefaults(), array(
            'widget-arrow-previous' => '$ss$/plugins/widgetarrow/image/image/previous/thin-horizontal.svg',
            'widget-arrow-next'     => '$ss$/plugins/widgetarrow/image/image/next/thin-horizontal.svg',
            'widget-arrow-style'    => ''
        ));
    }
}

N2Plugin::addPlugin('sswidgetarrow', 'N2SSPluginWidgetArrowImageEmpty');
class N2SSPluginWidgetArrowImageVertical extends N2SSPluginWidgetArrowImage {

    var $_name = 'imageVertical';

    static function getDefaults() {
        return array_merge(N2SSPluginWidgetArrowImage::getDefaults(), array(
            'widget-arrow-previous'               => '$ss$/plugins/widgetarrow/image/image/previous/simple-vertical.svg',
            'widget-arrow-next'                   => '$ss$/plugins/widgetarrow/image/image/next/simple-vertical.svg',
            'widget-arrow-style'                  => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiMTB8KnwxMHwqfDEwfCp8MTB8KnxweCIsImJveHNoYWRvdyI6IjB8KnwwfCp8MHwqfDB8KnwwMDAwMDBmZiIsImJvcmRlciI6IjB8Knxzb2xpZHwqfDAwMDAwMGZmIiwiYm9yZGVycmFkaXVzIjoiMyIsImV4dHJhIjoiIn0seyJiYWNrZ3JvdW5kY29sb3IiOiIyZWNjNzFlMCJ9XX0=',
            'widget-arrow-previous-position-area' => 3,
            'widget-arrow-next-position-area'     => 10,
        ));
    }
}

N2Plugin::addPlugin('sswidgetarrow', 'N2SSPluginWidgetArrowImageVertical');


