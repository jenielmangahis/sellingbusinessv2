<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorPostsAllCustomPosts extends N2GeneratorAbstract {

    protected $layout = 'article';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'posts', n2_('Filter'));

        $group = new N2ElementGroup($filter, 'filter_group', n2_('Filter'));

        new N2ElementWordPressPostTypes($group, 'posttypes', n2_('Post types'), 0, array(
            'isMultiple' => true
        ));

        new N2ElementWordPressAllTaxonomies($group, 'taxonomies', n2_('Taxonomies'), 0, array(
            'postSeparator' => '|*|'
        ));

        new N2ElementTextarea($filter, 'ids', n2_('IDs (one per line)'), '', array(
            'fieldStyle' => 'width:300px;height: 200px;'
        ));

        $group = new N2ElementGroup($filter, 'post_meta', n2_('Post custom fields'), array(
            'tip' => n2_('Only show posts, where the given meta key is equal to the given meta value.')
        ));

        new N2ElementWordPressMetaKeys($group, 'postmetakey', n2_('Field name'), 0);

        new N2ElementList($group, 'postmetacompare', n2_('Compare method'), '=', array(
            'options' => array(
                '='           => '=',
                '!='          => '!=',
                '>'           => '>',
                '>='          => '>=',
                '<'           => '<',
                '<='          => '<=',
                'LIKE'        => 'LIKE',
                'NOT LIKE'    => 'NOT LIKE',
                'IN'          => 'IN',
                'NOT IN'      => 'NOT IN',
                'BETWEEN'     => 'BETWEEN',
                'NOT BETWEEN' => 'NOT BETWEEN',
                'REGEXP'      => 'REGEXP',
                'NOT REGEXP'  => 'NOT REGEXP',
                'RLIKE'       => 'RLIKE',
                'EXISTS'      => 'EXISTS',
                'NOT EXISTS'  => 'NOT EXISTS'
            )
        ));

        new N2ElementText($group, 'postmetavalue', n2_('Field value'));

        $group = new N2ElementGroup($filter, 'post_meta_more', n2_('More post custom fields'), array(
            'tip' => n2_('Same as the previous option, just you can write down more comparisons. Read the "Field name" and "Compare method" out from the previous option.')
        ));

        new N2ElementTextarea($group, 'postmetakeymore', n2_('One per line'), 'field_name||compare_method||field_value', array(
            'fieldStyle' => 'width:300px;height: 100px;',
            'tip'        => 'Example: published||=||yes'
        ));

        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'postsorder', n2_('Order'), 'post_date|*|desc');
        new N2ElementList($order, 'postsorder-1', n2_('Field'), '', array(
            'options' => array(
                'none'          => n2_('None'),
                'post_date'     => n2_('Post date'),
                'ID'            => 'ID',
                'title'         => n2_('Title'),
                'post_modified' => n2_('Modification date'),
                'rand'          => n2_('Random'),
                'post__in'      => n2_('Given IDs'),
                'menu_order'    => n2_('Menu order')
            )
        ));

        new N2ElementRadio($order, 'postsorder-2', n2_('Order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));
    }

    protected function _getData($count, $startIndex) {
        global $post, $wp_the_query;
        $tmpPost         = $post;
        $tmpWp_the_query = $wp_the_query;
        $wp_the_query    = null;
        if (has_filter('the_content', 'siteorigin_panels_filter_content')) {
            $siteorigin_panels_filter_content = true;
            remove_filter('the_content', 'siteorigin_panels_filter_content');
        } else {
            $siteorigin_panels_filter_content = false;
        }

        $taxonomies = array_diff(explode('||', $this->data->get('taxonomies', '')), array(
            '',
            0
        ));

        if (count($taxonomies)) {
            $tax_array = array();
            foreach ($taxonomies AS $tax) {
                $parts = explode('|*|', $tax);
                if (!is_array(@$tax_array[$parts[0]]) || !in_array($parts[1], $tax_array[$parts[0]])) {
                    $tax_array[$parts[0]][] = $parts[1];
                }
            }

            $tax_query = array();
            foreach ($tax_array AS $taxonomy => $terms) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'terms'    => $terms,
                    'field'    => 'id',
                    'relation' => 'OR'
                );
            }
        } else {
            $tax_query = '';
        }

        $compare       = array();
        $compare_value = $this->data->get('postmetacompare', '');
        if (!empty($compare_value)) {
            $compare = array( 'compare' => $compare_value );
        }

        $postMetaKey = $this->data->get('postmetakey', '0');
        if (!empty($postMetaKey)) {
            $postMetaValue = $this->data->get('postmetavalue', '');
            $getPostMeta   = array(
                'meta_query' => array(
                    array(
                        'key'   => $postMetaKey,
                        'value' => $postMetaValue,
                    ) + $compare
                )
            );
        } else {
            $getPostMeta = array();
        }

        $metaMore = $this->data->get('postmetakeymore', '');
        if (!empty($metaMore) && $metaMore != 'field_name||compare_method||field_value') {
            $metaMoreValues = explode(PHP_EOL, $metaMore);
            foreach ($metaMoreValues AS $metaMoreValue) {
                $metaMoreValue = preg_replace('/\s+/', '', $metaMoreValue);
                if ($metaMoreValue != 'field_name||compare_method||field_value') {
                    $metaMoreArray = explode('||', $metaMoreValue);
                    if (count($metaMoreArray) == 3) {
                        if (!empty($metaMoreArray[1])) {
                            $compare = array( 'compare' => $metaMoreArray[1] );
                        } else {
                            $compare = array( '' );
                        }
                        $getPostMeta['meta_query'][] =
                            array(
                                'key'   => $metaMoreArray[0],
                                'value' => $metaMoreArray[2],
                            ) + $compare;
                    }
                }
            }
        }

        list($orderBy, $order) = N2Parse::parse($this->data->get('postsorder', 'post_date|*|desc'));

        $getPosts = array(
            'include'          => '',
            'exclude'          => '',
            'meta_key'         => '',
            'meta_value'       => '',
            'post_mime_type'   => '',
            'post_parent'      => '',
            'post_status'      => 'publish',
            'suppress_filters' => false,
            'offset'           => $startIndex,
            'posts_per_page'   => $count,
            'tax_query'        => $tax_query
        );

        $postTypes = $this->data->get('posttypes', '');
        if (!empty($postTypes)) {
            $getPosts += array('post_type' => explode('||', $postTypes));
        } else {
            $getPosts += array('post_type' => 'any');
        }

        if ($orderBy != 'none') {
            $getPosts += array(
                'orderby'            => $orderBy,
                'order'              => $order,
                'ignore_custom_sort' => true
            );
        }

        $ids = $this->getIDs();

        if (count($ids) <> 1 || $ids[0] <> 0) {
            $getPosts += array(
                'post__in' => $ids
            );
        }

        $getPosts = array_merge($getPosts, $getPostMeta);

        $posts = get_posts($getPosts);

        $data = array();

        for ($i = 0; $i < count($posts); $i++) {
            $record = array();

            $post = $posts[$i];
            setup_postdata($post);

            $record['id'] = $post->ID;

            $record['url']                = get_permalink();
            $record['title']              = apply_filters('the_title', get_the_title());
            $record['content']            = get_the_content();
            $record['description']        = preg_replace('#\[[^\]]+\]#', '', $record['content']);
            $record['author_name']        = $record['author'] = get_the_author();
            $record['author_url']         = get_the_author_meta('url');
            $record['date']               = get_the_date();
            $record['modified']           = get_the_modified_date();
            $type_object                  = get_post_type_object(get_post_type());
            $record['type_singular_name'] = $type_object->labels->singular_name;
            $category                     = get_the_category();
            if (isset($category[0]->name)) {
                $record['category_name'] = $category[0]->name;
            }

            $thumbnail_id             = get_post_thumbnail_id($post->ID);
            $record['featured_image'] = wp_get_attachment_url($thumbnail_id);
            if (!$record['featured_image']) {
                $record['featured_image'] = '';
            } else {
                $thumbnail_meta = get_post_meta($thumbnail_id, '_wp_attachment_metadata', true);
                if (isset($thumbnail_meta['sizes'])) {
                    $sizes  = $this->getImageSizes($thumbnail_id, $thumbnail_meta['sizes']);
                    $record = array_merge($record, $sizes);
                }
                $record['alt'] = '';
                $alt           = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                if (isset($alt)) {
                    $record['alt'] = $alt;
                }
            }

            $record['thumbnail'] = $record['image'] = $record['featured_image'];
            $record['url_label'] = 'View';

            $post_meta = get_post_meta($post->ID);
            if (count($post_meta)) {
                foreach ($post_meta AS $key => $value) {
                    foreach ($value AS $v) {
                        if (!empty($v)) {
                            $key = str_replace(array(
                                '_',
                                '-'
                            ), array(
                                '',
                                ''
                            ), $key);
                            if (array_key_exists($key, $record)) {
                                $key = 'meta' . $key;
                            }
                            $record[$key] = $v;
                        }
                    }
                }
            }

            $taxonomies = get_post_taxonomies($post->ID);
            foreach ($taxonomies AS $taxonomy) {
                $post_terms = wp_get_post_terms($post->ID, $taxonomy);
                for ($j = 0; $j < count($post_terms); $j++) {
                    $record[$taxonomy . '_' . ($j + 1)]                  = $post_terms[$j]->name;
                    $record[$taxonomy . '_' . ($j + 1) . '_ID']          = $post_terms[$j]->term_id;
                    $record[$taxonomy . '_' . ($j + 1) . '_description'] = $post_terms[$j]->description;
                }
            }

            if (class_exists('acf')) {
                $fields = get_fields($post->ID);
                if (count($fields) && is_array($fields) && !empty($fields)) {
                    foreach ($fields AS $k => $v) {
                        $type = $this->getACFType($k, $post->ID);
                        $k    = str_replace('-', '', $k);

                        while (isset($record[$k])) {
                            $k = 'acf_' . $k;
                        };
                        if (!is_array($v) && !is_object($v)) {
                            if ($type['type'] == "image" && is_numeric($type["value"])) {
                                $thumbnail_meta = wp_get_attachment_metadata($type["value"]);
                                $src            = wp_get_attachment_image_src($v, $thumbnail_meta['file']);
                                $v              = $src[0];
                            }
                            $record[$k] = $v;
                        } else if (!is_object($v)) {
                            if (isset($v['url'])) {
                                $record[$k] = $v['url'];
                            } else if (is_array($v)) {
                                foreach ($v AS $v_v => $k_k) {
                                    if (is_array($k_k) && isset($k_k['url'])) {
                                        $record[$k . $v_v] = $k_k['url'];
                                    }
                                }
                            }
                        }
                        if ($type['type'] == "image" && (is_numeric($type["value"]) || is_array($type['value']))) {
                            if (is_array($type['value'])) {
                                $sizes = $this->getImageSizes($type["value"]["id"], $type["value"]["sizes"], $k);
                            } else {
                                $thumbnail_meta = wp_get_attachment_metadata($type["value"]);
                                $sizes          = $this->getImageSizes($type["value"], $thumbnail_meta['sizes'], $k);
                            }
                            $record = array_merge($record, $sizes);
                        }
                    }
                }
            }

            $record['excerpt'] = get_the_excerpt();

            $data[$i] = &$record;
            unset($record);
        }
        if ($siteorigin_panels_filter_content) {
            add_filter('the_content', 'siteorigin_panels_filter_content');
        }

        $wp_the_query = $tmpWp_the_query;

        wp_reset_postdata();
        $post = $tmpPost;
        if ($post) setup_postdata($post);

        return $data;
    }

    protected function getImageSizes($thumbnail_id, $sizes, $prefix = false) {
        $data = array();
        if (!$prefix) {
            $prefix = "";
        } else {
            $prefix = $prefix . "_";
        }
        foreach ($sizes AS $size => $image) {
            $imageSrc                                               = wp_get_attachment_image_src($thumbnail_id, $size);
            $data[$prefix . 'image_' . $this->clearSizeName($size)] = $imageSrc[0];
        }

        return $data;
    }

    protected function clearSizeName($size) {
        return preg_replace("/-/", "_", $size);
    }

    protected function getACFType($key, $post_id) {
        $type = get_field_object($key, $post_id);

        return $type;
    }
}

