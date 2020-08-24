<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorWooCommerceProductsByFilter extends N2GeneratorAbstract {

    protected $layout = 'product';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'Filter', n2_('Filter'));

        $_filter = new N2ElementGroup($filter, 'filter', n2_('Filter'));
        new N2ElementWoocommerceCategories($_filter, 'categories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));

        new N2ElementWoocommerceTags($_filter, 'tags', n2_('Tag'), 0, array(
            'isMultiple' => true
        ));

        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'));
        new N2ElementFilter($limit, 'featured', n2_('Featured'), 0);
        new N2ElementFilter($limit, 'instock', n2_('In stock'), 1);
        new N2ElementFilter($limit, 'downloadable', n2_('Downloadable'), 0);
        new N2ElementFilter($limit, 'virtual', n2_('Virtual'), 0);

        new N2ElementTextarea($filter, 'textvars', n2_('Create text variables (one per line)'), 'variable||equalvalue||true||false&#xA;meta_stock_status||instock||In stock||Out of stock&#xA;meta_downloadable||yes||Downloadable||Not Downloadable&#xA;meta_virtual||yes||Virtual||Not Virtual', array(
            'fieldStyle' => 'width:600px;height: 100px;',
            'tip'        => 'variable||equalvalue||true||false:' . n2_('if \'variable\' equals to \'equalvalue\', then you will get the text \'true\', but if it\'s not equal, the text will be \'false\'. The new variable\'s name will be textvariable (the \'variable\' replaced by your variable\'s name).')
        ));

        new N2ElementList($filter, 'fraction', n2_('Round rating'), 1, array(
            'options' => array(
                '1' => n2_('No'),
                '2' => n2_('Half'),
                '3' => n2_('Third'),
                '4' => n2_('Quarter')
            )
        ));
        new N2ElementList($filter, 'visibility', n2_('Visibility'), 'default', array(
            'options' => array(
                'all'     => n2_('All'),
                'default' => n2_('Shop and search results'),
                'catalog' => n2_('Shop only'),
                'search'  => n2_('Search results only')
            )
        ));
        
        $timestamps = new N2ElementGroup($filter, 'timestamps', n2_('Timestamps'));
        new N2ElementText($timestamps, 'timestampvariables', 'Replace these timestamp variables', '', array(
            'tip' => 'Separate them with comma.'
        ));
        new N2ElementText($timestamps, 'timestampformat', 'Date format', 'm-d-Y', array(
            'tip' => 'Any PHP date format can be used: http://php.net/manual/en/function.date.php'
        ));

        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'categoryorder', n2_('Order'), 'post_date|*|desc');
        new N2ElementList($order, 'categoryorder-1', n2_('Field'), 'post_date', array(
            'options' => array(
                'default'        => n2_('Default'),
                'post_date'      => n2_('Creation date'),
                'post_modified'  => n2_('Modification date'),
                '_total_sales'   => n2_('Total sales'),
                '_regular_price' => n2_('Regular price'),
                '_sale_price'    => n2_('Sale price'),
                '_price'         => n2_('Price'),
                'ID'             => 'ID',
                'title'          => n2_('Title')
            )
        ));

        new N2ElementRadio($order, 'categoryorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));
    }

    public function filterName($name) {
        return $name . N2Translation::getCurrentLocale() . get_woocommerce_currency();
    }

    public static function cacheKey($params) {
        return get_woocommerce_currency();
    }

    public static function floorToFraction($number, $denominator = 1) {
        if ((is_numeric($number) && $number != 0) && (is_numeric($denominator) && $denominator != 0)) {
            $x = $number * $denominator;
            $x = round($x);
            $x = $x / $denominator;

            return $x;
        }

        return $number;
    }

    protected function _getData($count, $startIndex) {
        $data = array();

        $categories = explode('||', $this->data->get('categories', ''));
        $fraction   = $this->data->get('fraction', 1);
        $visibility = $this->data->get('visibility', 0);

        $tax_query = array();
        if (!in_array(0, $categories)) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $categories
            );
        }

        $tags = explode('||', $this->data->get('tags', ''));

        if (!in_array(0, $tags)) {
            $tax_query[] = array(
                'taxonomy' => 'product_tag',
                'field'    => 'term_id',
                'terms'    => $tags
            );
        }

        switch ($this->data->get('featured', '0')) {
            case 1:
                $tax_query[] = array(
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => 'featured',
                    'operator' => 'IN',
                );
                break;
            case -1:
                $tax_query[] = array(
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => 'featured',
                    'operator' => 'NOT IN',
                );
                break;
        }

        $meta_query = array();

        $order = explode("|*|", $this->data->get('categoryorder', 'creation_date|*|desc'));
        if (substr($order[0], 0, 1) == '_') {
            $orderBy      = 'meta_value_num'; //meta_value = strval, meta_value_num = intval
            $meta_query[] = array(
                'key'     => $order[0],
                'compare' => 'LIKE'
            );
        } else {
            $orderBy = $order[0];
        }

        switch ($this->data->get('instock', '1')) {
            case 1:
                $meta_query[] = array(
                    'key'   => '_stock_status',
                    'value' => 'instock'
                );
                break;
            case -1:
                $meta_query[] = array(
                    'key'   => '_stock_status',
                    'value' => 'outofstock'
                );
                break;
        }

        switch ($this->data->get('downloadable', '0')) {
            case 1:
                $meta_query[] = array(
                    'key'   => '_downloadable',
                    'value' => 'yes'
                );
                break;
            case -1:
                $meta_query[] = array(
                    'key'   => '_downloadable',
                    'value' => 'no'
                );
                break;
        }

        switch ($this->data->get('virtual', '0')) {
            case 1:
                $meta_query[] = array(
                    'key'   => '_virtual',
                    'value' => 'yes'
                );
                break;
            case -1:
                $meta_query[] = array(
                    'key'   => '_virtual',
                    'value' => 'no'
                );
                break;
        }

        $args = array(
            'offset'           => $startIndex,
            'posts_per_page'   => $count,
            'order'            => $order[1],
            'post_type'        => 'product',
            'tax_query'        => $tax_query,
            'meta_query'       => $meta_query,
            'suppress_filters' => 0
        );
		
		if($order[0] != 'default'){
			$args += array(
				'orderby'            => $orderBy,
				'ignore_custom_sort' => true
			);
		}

        $textVars = $this->data->get('textvars', '');

        $productFactory = new WC_Product_Factory();
        $i              = 0;
        $posts          = get_posts($args);
		    $timestampFormat = $this->data->get('timestampformat', 'Y-m-d');
		    $timestampVariables = array_map('trim', explode(',', $this->data->get('timestampvariables', '')));
    
    
        for ($j = 0; $j < count($posts); $j++) {
            $product = $productFactory->get_product($posts[$j]);


            if ($visibility == "default") {
                $is_visible = $product->is_visible();
            } else if ($product->get_catalog_visibility() == "catalog" && $visibility == "catalog") {
                $is_visible = true;
            } else if ($product->get_catalog_visibility() == "search" && $visibility == "search") {
                $is_visible = true;
            } else if ($visibility == "all") {
                $is_visible = true;
            } else {
                $is_visible = false;
            }
            if ($product && $is_visible) {
                $product_id = $product->get_id();
				$thumbnail_id = get_post_thumbnail_id($product_id);
                $image      = wp_get_attachment_url($thumbnail_id);
                $thumbnail  = wp_get_attachment_image_src($thumbnail_id);
                if ($thumbnail[0] != null) {
                    $thumbnail = $thumbnail[0];
                } else {
                    $thumbnail = $image;
                }
                $rating   = $product->get_average_rating();
                $data[$i] = array(
                    'title'                  => $product->get_title(),
                    'url'                    => $product->get_permalink(),
                    'description'            => get_post($product_id)->post_content,
                    'short_description'      => get_post($product_id)->post_excerpt,
                    'image'                  => N2ImageHelper::dynamic($image),
                    'thumbnail'              => N2ImageHelper::dynamic($thumbnail),
                    'price'                  => $product->get_price_html(),
                    'price_without_currency' => $product->get_price(),
                    'regular_price'          => wc_price($product->get_regular_price()),
                    'price_with_tax'         => wc_price(wc_get_price_including_tax($product)),
                    'rating'                 => $rating,
                    'rating_rounded'         => $this->floorToFraction($rating, $fraction),
                    'tags'                   => wc_get_product_tag_list($product_id)
                );
				
				$image_sizes = get_intermediate_image_sizes();
				foreach($image_sizes AS $image_size){
					$image_data = wp_get_attachment_image_src($thumbnail_id, $image_size);
					$data[$i] += array(
						'image_' . $image_size => N2ImageHelper::dynamic($image_data[0])
					);
				}

                $seller = get_user_by("id", $posts[$j]->post_author);
                if( is_object( $seller ) ){
                    $data[$i]['seller_display_name'] = $seller->display_name;
                    $data[$i]['seller_user_nicename'] = $seller->user_nicename;
                }

                if (!class_exists('gpls_woo_rfq_CART')) {
                    $data[$i]['add_to_cart_text'] = $product->add_to_cart_text();
                }

                $product_type = $product->get_type();
                if ($product->has_child() && $product_type != "grouped") {
                    $data[$i] += array(
                        'variation_min_price'                  => $product->get_variation_regular_price('min'),
                        'variation_min_price_currency'         => wc_price($product->get_variation_regular_price('min')),
                        'variation_min_regular_price'          => $product->get_variation_sale_price('min'),
                        'variation_min_regular_price_currency' => wc_price($product->get_variation_sale_price('min')),
                        'variation_min_sale_price'             => $product->get_variation_price('min'),
                        'variation_min_sale_price_currency'    => wc_price($product->get_variation_price('min')),
                        'variation_max_price'                  => $product->get_variation_regular_price('max'),
                        'variation_max_price_currency'         => wc_price($product->get_variation_regular_price('max')),
                        'variation_max_regular_price'          => $product->get_variation_sale_price('max'),
                        'variation_max_regular_price_currency' => wc_price($product->get_variation_sale_price('max')),
                        'variation_max_sale_price'             => $product->get_variation_price('max'),
                        'variation_max_sale_price_currency'    => wc_price($product->get_variation_price('max'))
                    );
                }

                if ($product->is_on_sale()) {
                    $data[$i]['sale_price'] = wc_price($product->get_sale_price());
                } else {
                    $data[$i]['sale_price'] = $data[$j]['price'];
                }

                $product_gallery = get_post_meta($product_id, "_product_image_gallery", true);
                if(!empty($product_gallery)){
                    $product_gallery = explode(',', $product_gallery);
                    for ($fora=0; $fora < count($product_gallery); $fora++) { 
                        $data[$i]['product_gallery_'.$fora.'_image'] = wp_get_attachment_url($product_gallery[$fora]);
                        $data[$i]['product_gallery_'.$fora.'_thumbnail'] = wp_get_attachment_image_src($product_gallery[$fora])[0];
                        if( empty( $data[$i]['product_gallery_'.$fora.'_thumbnail'] ) ){
                            $data[$i]['product_gallery_'.$fora.'_thumbnail'] = $data[$i]['product_gallery_'.$fora.'_image'];
                        }
                    }
                }

                $product_meta = get_post_meta($product_id);
                foreach ($product_meta as $meta => $value) {
                    if (substr($meta, 0, 1) == '_') {
                        $meta = 'meta' . $meta;
                    }
                    if (@unserialize($value[0]) !== false || $value[0] === 'b:0;') {
                        $product_attributes = unserialize($value[0]);
                        if (!empty($product_attributes) && is_array($product_attributes)) {
                            foreach ($product_attributes as $key => $value2) {
                                if (is_array($value2)) {
                                    foreach ($value2 as $k => $v) {
                                        if (is_string($v)) {
                                            if (strpos($v, '|') !== false) {
                                                $helper = explode("|", $v);
                                                for ($fv = 0; $fv < count($helper); $fv++) {
                                                    $data[$i][$key . '_' . $k . '_' . $fv] = $helper[$fv];
                                                }
                                            } else {
                                                $data[$i][$key . '_' . $k] = $v;
                                            }
                                        }
                                    }
                                } else {
                                    if (is_numeric($value2) || is_string($value2)) {
                                        if ($meta == 'dfiFeatured') {
                                            $parts = explode(',', $value2);
                                            if (isset($parts[1])) {
                                                $upload_dir = wp_upload_dir();
                                                $value2     = $upload_dir['baseurl'] . $parts[1];
                                            }
                                        }
                                        if (!array_key_exists($meta, $data[$i])) {
                                            $data[$i][$meta] = $value2;
                                        } else {
                                            $data[$i]['meta_' . $meta] = $value2;
                                        }
                                    }
                                }
                            }
                        } else {
                            if (!array_key_exists($meta, $data[$i])) {
                                $data[$i][$meta] = '';
                            } else {
                                $data[$i]['meta_' . $meta] = '';
                            }
                        }
                    } else {
                        if (is_array($value) && count($value) > 1) {
                            for ($fv = 0; $fv < count($value); $fv++) {
                                $v                           = $value[$fv];
                                $data[$i][$meta . "_" . $fv] = $v;
                            }
                        } else {
                            if (!array_key_exists($meta, $data[$i])) {
                                $data[$i][$meta] = $value[0];
                            } else {
                                $data[$i]['meta_' . $meta] = $value[0];
                            }
                        }
                    }

                }

                if (class_exists('acf')) {
                    $fields = get_fields($product_id);
                    if (count($fields) && is_array($fields) && !empty($fields)) {
                        foreach ($fields AS $k => $v) {
                            $type   = $this->getACFType($k,$product_id);
                            $k      = str_replace('-', '', $k);

                            while (isset($data[$i][$k])) {
                                $k  = 'acf_' . $k;
                            };
                                
                            if (!is_array($v) && !is_object($v)) {
                                if($type['type'] == "image" && is_numeric($type["value"])){
                                    $thumbnail_meta = wp_get_attachment_metadata($type["value"]);
                                    $src = wp_get_attachment_image_src($v, $thumbnail_meta['file']);
                                    $v = $src[0];
                                }
                                $data[$i][$k] = $v;
                            } else if (!is_object($v)) {
                                if(isset($v['url'])){
                                    $data[$i][$k] = $v['url'];
                                } else if(is_array($v)){
                                    foreach($v AS $v_v => $k_k){
                                        if(is_array($k_k) && isset($k_k['url'])){
                                            $data[$i][$k . $v_v] = $k_k['url'];
                                        }
                                    }
                                }
                            }
                            if($type['type'] == "image" && (is_numeric($type["value"]) || is_array($type['value']))){
                                if(is_array($type['value'])){
                                    $sizes              = $this->getImageSizes($type["value"]["id"], $type["value"]["sizes"], $k);
                                } else {
                                    $thumbnail_meta     = wp_get_attachment_metadata($type["value"]);
                                    $sizes              = $this->getImageSizes($type["value"], $thumbnail_meta['sizes'], $k);
                                }
                                $data[$i] = array_merge($data[$i], $sizes);
                            }
                        }
                    }
                }

                $categories = wp_get_post_terms($product_id, 'product_cat');

                if (!empty($categories) && is_array($categories)) {
                    $k = 1;
                    foreach ($categories as $category) {
                        $catId                                     = (int)$category->term_id;
                        $data[$i]['category' . $k]                 = $category->name;
                        $data[$i]['category' . $k . 'link']        = get_term_link($catId, 'product_cat');
                        $data[$i]['category' . $k . 'description'] = $category->description;
                        $data[$i]['category' . $k . 'ID']          = $catId;
                        $k++;
                    }
                }

                $data[$i]['ID'] = $product_id;

                if (!empty($textVars)) {
                    $lines = preg_split('/$\R?^/m', $textVars);
                    foreach ($lines AS $line) {
                        $variables = explode('||', $line);
                        if (count($variables) == 4 && isset($data[$i][$variables[0]])) {
                            if ($data[$i][$variables[0]] == $variables[1]) {
                                $data[$i]['text' . $variables[0]] = $variables[2];
                            } else {
                                $data[$i]['text' . $variables[0]] = $variables[3];
                            }
                        }
                    }
                }
                
				
        				if(!empty($timestampVariables)){
        					foreach($timestampVariables AS $timestampVariable){
        						if(isset($data[$i][$timestampVariable])){
        							$data[$i][$timestampVariable] = date($timestampFormat, intval($data[$i][$timestampVariable]));
        						}
        					}
        				}
        
                $i++;
            }
        }

        return $data;
    }

    protected function getImageSizes($thumbnail_id, $sizes, $prefix = false) {
        $data = array();
        if(!$prefix){
            $prefix = "";
        } else {
            $prefix = $prefix . "_";
        }
        foreach ($sizes AS $size => $image) {
            $imageSrc               = wp_get_attachment_image_src($thumbnail_id, $size);
            $data[$prefix.'image_' . $this->clearSizeName($size)] = $imageSrc[0];
        }
        return $data;
    }

    protected function clearSizeName ($size){
        return preg_replace("/-/", "_", $size);
    }

    protected function getACFType($key, $post_id) {
        $type = get_field_object($key, $post_id);
        return $type;
    }
}