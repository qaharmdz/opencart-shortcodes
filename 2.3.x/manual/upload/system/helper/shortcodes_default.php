<?php
/**
 * @package     OpenCart Shortcodes
 * @author      EchoThemes, http://www.echothemes.com
 * @copyright   Copyright (c) EchoThemes
 * @license     GPLv3 or later, http://www.gnu.org/licenses/gpl-3.0.html
 */

class ShortcodesDefault extends Controller
{
    /**
     * Generate product link with it variant of category and manufacture link.
     *
     * @example [link_product id="x" path="x_x" brand="x" ssl="0" title="xyz" /]
     * @example [link_product id="x" path="x_x" brand="x" ssl="0" title="xyz"]custom text[/link_product]
     */
    public function link_product($atts, $content='')
    {
        extract($this->shortcodes->shortcode_atts(array(
            'id'    => 0,
            'path'  => 0,
            'brand' => 0,
            'ssl'   => 0,
            'title' => ''
        ), $atts));

        if ($id) {
            $ssl = ($ssl) ? true : false;

            $this->load->model('catalog/product');
            $product = $this->model_catalog_product->getProduct($id);

            if ($product) {
                $title = ($title) ? 'title="' . $title . '"' : 'title="' . $product['name'] . '"';

                if (!$content) {
                    if(!$path && !$brand) {
                        return '<a href="' . $this->url->link('product/product', 'product_id=' . $id, $ssl) . '" ' . $title . '>' . $product['name'] . '</a>';
                    } elseif ($path && (!$brand || $brand)) {
                        return '<a href="' . $this->url->link('product/product', 'path=' . $path . '&product_id=' . $id, $ssl) . '" ' . $title . '>' . $product['name'] . '</a>';
                    } elseif (!$path && $brand) {
                        return '<a href="' . $this->url->link('product/product', 'manufacturer_id=' . $brand . '&product_id=' . $id, $ssl) . '" ' . $title . '>' . $product['name'] . '</a>';
                    }
                } elseif ($content) {
                    if(!$path && !$brand) {
                        return '<a href="' . $this->url->link('product/product', 'product_id=' . $id, $ssl) . '" ' . $title . '>' . $content . '</a>';
                    } elseif ($path && (!$brand || $brand)) {
                        return '<a href="' . $this->url->link('product/product', 'path=' . $path . '&product_id=' . $id, $ssl) . '" ' . $title . '>' . $content . '</a>';
                    } elseif (!$path && $brand) {
                        return '<a href="' . $this->url->link('product/product', 'manufacturer_id=' . $brand . '&product_id=' . $id, $ssl) . '" ' . $title . '>' . $content . '</a>';
                    }
                }
            } elseif (!$product && $content) {
                return $content;
            }
        }
    }

    /**
     * Generate category link.
     *
     * @example [link_category path="x_y" ssl="0" title="xyz" /]
     * @example [link_category path="x_y" ssl="1" title="xyz"]custom text[/link_category]
     */
    public function link_category($atts, $content='')
    {
        extract($this->shortcodes->shortcode_atts(array(
            'path'  => 0,
            'ssl'   => 0,
            'title' => ''
        ), $atts));

        if ($path) {
            $ssl = ($ssl) ? true : false;

            $this->load->model('catalog/category');
            $category = $this->model_catalog_category->getCategory($path);

            if ($category) {
                if (!$content) {
                    return '<a href="' . $this->url->link('product/category', 'path=' . $path, $ssl) . '"' . ' title="' . ($title ? $title : $category['name']) . '">' . $category['name'] . '</a>';
                } elseif ($content) {
                    return '<a href="' . $this->url->link('product/category', 'path=' . $path, $ssl) . '"' . ' title="' . ($title ? $title : $content) . '">' . $content . '</a>';
                }
            } elseif (!$category && $content) {
                return $content;
            }
        }
    }

    /**
     * Generate brand/ manufacturer link.
     *
     * @example [link_brand ssl="0" title="xyz" /]
     * @example [link_brand ssl="1" title="xyz"]custom text[/link_brand]
     * @example [link_brand brand="x" ssl="0" title="xyz" /]
     * @example [link_brand brand="x" ssl="1" title="xyz"]custom text[/link_brand]
     */
    public function link_brand($atts, $content='')
    {
        extract($this->shortcodes->shortcode_atts(array(
            'brand' => 0,
            'ssl'   => 0,
            'title' => ''
        ), $atts));

        $ssl    = ($ssl) ? true : false;
        $title  = ($title) ? 'title="' . $title . '"' : "";

        if ($brand) {
            $this->load->model('catalog/manufacturer');
            $manufacturer = $this->model_catalog_manufacturer->getManufacturer($brand);

            if ($manufacturer) {
                if (!$content) {
                    return '<a href="' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $brand, $ssl) . '" ' . $title . '>' . $manufacturer['name'] . '</a>';
                } else {
                    return '<a href="' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $brand, $ssl) . '" ' . $title . '>' . $content . '</a>';
                }
            } elseif (!$manufacturer && $content) {
                return $content;
            }
        } else {
            if (!$content) {
                $this->load->language('product/manufacturer');

                return '<a href="' . $this->url->link('product/manufacturer', '', $ssl) . '" ' . $title . '>' . $this->language->get('text_brand') . '</a>';
            } else {
                return '<a href="' . $this->url->link('product/manufacturer', '', $ssl) . '" ' . $title . '>' . $content . '</a>';
            }
        }
    }

    /**
     * Generate information link.
     *
     * @example [link_info id="x" ssl="0" title="xyz" /]
     * @example [link_info id="x" ssl="0" title="xyz"]custom text[/link_info]
     */
    public function link_info($atts, $content='')
    {
        extract($this->shortcodes->shortcode_atts(array(
            'id'    => 0,
            'ssl'   => 0,
            'title' => ''
        ), $atts));

        if ($id) {
            $ssl    = ($ssl) ? true : false;
            $title  = ($title) ? 'title="' . $title . '"' : "";

            $this->load->model('catalog/information');
            $information = $this->model_catalog_information->getInformation($id);

            if ($information) {
                if (!$content) {
                    return '<a href="' . $this->url->link('information/information', 'information_id=' . $id, $ssl) . '" ' . $title . '>' . $information['title'] . '</a>';
                } elseif ($content) {
                    return '<a href="' . $this->url->link('information/information', 'information_id=' . $id, $ssl) . '" ' . $title . '>' . $content . '</a>';
                }
            } elseif (!$information && $content) {
                return $content;
            }
        }
    }

    /**
     * Generate custom link based on OpenCart API Url format
     *
     * @example [link_custom route="foo" args="bar" ssl="0" title="xyz"]custom text[/link_custom]
     */
    public function link_custom($atts, $content='')
    {
        extract($this->shortcodes->shortcode_atts(array(
            'route' => '',
            'args'  => '',
            'ssl'   => 0,
            'title' => ''
        ), $atts));

        $ssl = ($ssl) ? true : false;

        if($route && $content) {
            return '<a href="' . $this->url->link($route, $args, $ssl) . '" ' . 'title="' . $title . '">' . $content . '</a>';
        }
    }

    /**
     * Generate custom link for multi-store site
     *
     * @example [link_store store="x" route="foo" args="bar=3" ssl="0" title="xyz"]custom text[/link_custom]
     */
    public function link_store($atts, $content='')
    {
        extract($this->shortcodes->shortcode_atts(array(
            'store' => 0,
            'route' => '',
            'args'  => '',
            'ssl'   => 0,
            'title' => ''
        ), $atts));

        $ssl = ($ssl) ? true : false;

        if ($route && $content) {
            $current_store = $this->config->get('config_url');

            if ($store) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store . "' AND `key` = 'config_url'");
                if ($query->num_rows) {
                    $url = str_replace($current_store, $query->row['value'], $this->url->link($route, $args, $ssl));

                    return '<a href="' . $url . '" ' . 'title="' . $title . '">' . $content . '</a>';
                } else {
                    return $content;
                }
            } else {
                return '<a href="' . str_replace($current_store, HTTP_SERVER, $this->url->link($route, $args, $ssl)) . '" ' . 'title="' . $title . '">' . $content . '</a>';
            }
        }
    }

    /**
     * Load module type product (featured, latest, bestseller, special) anywhere!
     *
     * @example [module_product type="latest" limit="4" img_w="100" img_h="100" /]
     */
    public function module_product($atts)
    {
        extract($this->shortcodes->shortcode_atts(array(
            'type'    => '',
            'limit'   => 4,
            'img_w'   => 200,
            'img_h'   => 200,
            'product' => ''
        ), $atts));

        if ($type) {
            $module = $this->load->controller('extension/module/' . $type, array(
                        'limit'   => $limit,
                        'width'   => $img_w,
                        'height'  => $img_h,
                        'product' => explode(',', $product)
                    ));

            $html = '<div class="shortcode-module sc-' . $type . '">' . $module . '</div>';

            return $html;
        }
    }

    /**
     * Load module slideshow
     *
     * @example [module_slideshow id="7" img_w="1200" img_h="300" /]
     */
    public function module_slideshow($atts)
    {
        extract($this->shortcodes->shortcode_atts(array(
            'id'    => 0,
            'img_w' => 400,
            'img_h' => 300
        ), $atts));

        if ($id) {
            $module = $this->load->controller('extension/module/slideshow', array(
                'banner_id' => $id,
                'width'     => $img_w,
                'height'    => $img_h
            ));

            $html = '<div class="shortcode-module sc-slideshow">' . $module . '</div>';

            return $html;
        }
    }

    /**
     * User required to login to read the rest of the content.
     * Able to restrict user to read based on their group.
     *
     * @example [login msg_login='Silahkan <a href="%s">login</a> untuk melihat halaman ini.']content[/login]
     */
    public function login($atts, $content='')
    {
        extract($this->shortcodes->shortcode_atts(array(
            'msg_login' => $this->language->get('login_message'),
            'msg_group' => $this->language->get('login_group'),
            'suffix'    => '',
            'group'     => ''
        ), $atts));

        $this->language->load('common/shortcodes_default');

        if ($content && $this->customer->isLogged()) {
            if($group) {
                if ($group == $this->customer->getGroupId()) {
                    return $this->shortcodes->do_shortcode($content);
                } else {
                    return '<div class="' . $suffix . '">' . sprintf($msg_group, $this->url->link('information/contact')) . '</div>';
                }
            } else {
                return $this->shortcodes->do_shortcode($content);
            }
        } else {
            return '<div class="' . $suffix . '">' . sprintf($msg_login, $this->url->link('account/login')) . '</div>';
        }
    }

    /**
     * Embed video: youtube and vimeo
     *
     * @example [video type="vimeo" id="xxx" ratio="16by9" /]
     */
    public function video($atts)
    {
        extract($this->shortcodes->shortcode_atts(array(
            'type'      => 'youtube',
            'id'        => 0,
            'ratio'     => '16by9', // 4by3
            'autoplay'  => 0
        ), $atts));

        if ($id) {
            if ($type == 'youtube') {
                $video  = '<div class="embed-responsive embed-responsive-' . $ratio . '">';
                $video .= '<iframe class="embed-responsive-item" src="http://youtube.com/embed/' . $id . '?rel=0&autoplay=' . $autoplay . '" allowfullscreen></iframe>';
                $video .= '</div>';

                $html = '<div class="shortcode-video sc-' . $type . '">' . $video . '</div>';

                return $html;

            } elseif ($type == 'vimeo') {
                $video  = '<div class="embed-responsive embed-responsive-' . $ratio . '">';
                $video .= '<iframe class="embed-responsive-item" src="//player.vimeo.com/video/' . $id . '?autoplay=' . $autoplay . '" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
                $video .= '</div>';

                $html = '<div class="shortcode-video sc-' . $type . '">' . $video . '</div>';

                return $html;
            }
        }
    }

    /**
     * Embed image directly or with cache
     *
     * @example [image src="" img_w="450" img_h="280" title="" alt="" align="" /]
     */
    public function image($atts, $content='')
    {
        extract($this->shortcodes->shortcode_atts(array(
            'src'    => '',
            'img_w'  => 200,
            'img_h'  => 200,
            'title'  => '',
            'alt'    => '',
            'align'  => '',     // left, right, center
            'cache'  => 1
        ), $atts));

        if (!$src && $content) { $src = $content; }
        if (!$alt && $title) { $alt = $title; }
        if ($align == 'right') {
            $align_style = 'float:right;margin:0 0 10px 10px;';
        } elseif ($align == 'center') {
            $align_style = 'display:block;margin:0 auto 15px;';
        } else {
            $align_style = 'float:left;margin:0 10px 0 10px;';
        }

        if (is_file(DIR_IMAGE . $src)) {
            if ($cache) {
                $this->load->model('tool/image');
                $src = $this->model_tool_image->resize($src, $img_w, $img_h);
            }

            return '<img class="shortcode-image" src="' . $src . '" width="' . $img_w.'px' . '" height="' . $img_h.'px' . '" alt="' . $alt . '" title="' . $title . '" style="' . $align_style . '">';
        }
    }

    /**
     * Embed image with modalbox feature
     *
     * @example [image_modal src="catalog/your_image.jpg" /]
     * @example [image_modal src="catalog/your_image.jpg" img_w="450" img_h="280" title="" alt="" align="" caption="" /]
     */
    public function image_modal($atts, $content='')
    {
        static $sc_image_modal_script = false;

        extract($this->shortcodes->shortcode_atts(array(
            'src'       => '',
            'img_w'     => 200,
            'img_h'     => 200,
            'title'     => '',
            'alt'       => '',
            'align'     => 'left', // left, right, center
            'caption'   => $this->language->get('imgModal_caption'),
            'cache'     => 1
        ), $atts));

        $this->language->load('common/shortcodes_default');

        if (!$src && $content) { $src = $content; }
        if (!$alt & $title) { $alt = $title; }
        if ($align == 'right') {
            $align_style = 'float:right;margin:0 0 10px 10px;';
        } elseif ($align == 'center') {
            $align_style = 'display:block;margin:0 auto 15px;';
        } else {
            $align_style = 'float:left;margin:0 10px 10px 0;';
        }

        $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
        $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
        $script_load = '<script type="text/javascript">
            $(document).ready(function() {
                $(".sc-image-modal").magnificPopup({
                    type:"image"
                });
            });
            </script> ';

        if (is_file(DIR_IMAGE . $src)) {
            if ($cache) {
                $this->load->model('tool/image');
                $src_thumb  = $this->model_tool_image->resize($src, $img_w, $img_h);
            } else {
                $src_thumb  = $src;
            }

            $html  = '<div style="' . $align_style . '">';
            $html .= '<a href="image/' . $src . '" title="' . $title . '" class="sc-image-modal" style="text-decoration:none; outline:0;">';
            $html .= '<img class="shortcode-image-modal" src="' . $src_thumb . '" width="' . $img_w.'px' . '" height="' . $img_h.'px' . '" alt="' . $alt . '" title="' . $title . '">';
            $html .= '<div style="font-style:italic;text-align:center;font-size:12px;">' . $caption . '</div>';
            $html .= '</a>';
            if (!$sc_image_modal_script) {
                $html .= $script_load;
            }
            $html .= '</div>';

            $sc_image_modal_script = true;

            return $html;
        }
    }
}
