<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorWebDoradoImages extends N2GeneratorAbstract {

    protected $layout = 'image';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'Filter', n2_('Filter'));

        new N2ElementWebDoradoGalleries($filter, 'webdoradogalleries', n2_('Galleries'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementWebDoradoTags($filter, 'webdoradotags', n2_('Tags'), 0, array(
            'isMultiple' => true
        ));

        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'order', n2_('Order'), 'STR_TO_DATE(a.date, \'%d %m %Y, %h:%i\')|*|desc');
        new N2ElementList($order, 'order-1', n2_('Field'), 'post_date', array(
            'options' => array(
                '0'                                        => n2_('None'),
                'a.id'                                     => 'ID',
                'a.filename'                               => n2_('Filename'),
                'a.alt'                                    => n2_('Alt tag'),
                'STR_TO_DATE(a.date, \'%d %m %Y, %h:%i\')' => n2_('Upload date'),
                'a.order'                                  => n2_('Order'),
                'a.comment_count'                          => n2_('Comment count'),
                'a.avg_rating'                             => n2_('Average rating'),
                'a.rate_count'                             => n2_('Rate count'),
                'a.hit_count'                              => n2_('Hit count')
            )
        ));

        new N2ElementRadio($order, 'order-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));
    }

    protected function _getData($count, $startIndex) {
        global $wpdb;

        $where = array('a.published = 1');

        $galleries = array_map('intval', explode('||', $this->data->get('webdoradogalleries', '')));
        if (!in_array('0', $galleries)) {
            $where[] = "a.gallery_id IN (" . implode(',', $galleries) . ")";
        }

        $tags = array_map('intval', explode('||', $this->data->get('webdoradotags', '')));
        if (!in_array('0', $tags)) {
            $where[] = "a.id IN (SELECT image_id FROM " . $wpdb->base_prefix . "bwg_image_tag WHERE tag_id IN (" . implode(',', $tags) . "))";
        }

        $order    = explode('|*|', $this->data->get('order', '0 asc'));
        $pictures = $wpdb->get_results("SELECT
                                        a.slug AS slug,
                                        a.filename AS filename,
                                        a.image_url AS image_url,
                                        a.thumb_url AS thumb_url,
                                        a.description AS description,
                                        a.alt AS alt, a.date AS date,
                                        a.author AS author,
                                        a.comment_count AS comment_count,
                                        a.avg_rating AS avg_rating,
                                        a.rate_count AS rate_count,
                                        a.hit_count AS hit_count,
                                        a.redirect_url AS redirect_url,
                                        a.filetype AS filetype,
                                        b.name AS gallery_name,
                                        b.slug AS gallery_slug,
                                        b.description AS gallery_description,
                                        b.page_link AS gallery_page_link,
                                        b.preview_image AS gallery_preview_image,
                                        b.random_preview_image AS random_preview_image
                                        FROM " . $wpdb->base_prefix . "bwg_image AS a
                                        LEFT JOIN " . $wpdb->base_prefix . "bwg_gallery AS b
                                        ON a.gallery_id = b.id
                                        WHERE " . implode(' AND ', $where) . "
                                        " . ($order[0] != '0' ? "ORDER BY " . implode(' ', explode('|*|', $this->data->get('order', 'b.id asc'))) : "") . "
                                        LIMIT " . $startIndex . ", " . $count);

        $siteUrl = get_site_url();

        $data = array();
        foreach ($pictures as $p) {
            if (strpos($p->filetype, 'EMBED_OEMBED') !== false) {
                //youtube, vimeo, etc. types
                $r = array(
                    'file_url'  => $p->image_url,
                    'image'     => $p->thumb_url,
                    'thumbnail' => $p->thumb_url
                );
            } else {
                //image types
                $r = array(
                    'image'     => N2ImageHelper::dynamic($siteUrl . "/wp-content/uploads/photo-gallery" . $p->image_url),
                    'thumbnail' => N2ImageHelper::dynamic($siteUrl . "/wp-content/uploads/photo-gallery" . $p->thumb_url)
                );
            }

            $r += array(
                'title'                 => $p->alt,
                'slug'                  => $p->slug,
                'filename'              => $p->filename,
                'date'                  => $p->date,
                'author'                => get_the_author_meta('display_name', $p->author),
                'comment_count'         => $p->comment_count,
                'average_rating'        => $p->avg_rating,
                'rate_count'            => $p->rate_count,
                'hit_count'             => $p->hit_count,
                'redirect_url'          => $p->redirect_url,
                'gallery_name'          => $p->gallery_name,
                'gallery_slug'          => $p->gallery_slug,
                'gallery_description'   => $p->gallery_description,
                'gallery_page_link'     => $p->gallery_page_link,
                'gallery_preview_image' => N2ImageHelper::dynamic($siteUrl . "/wp-content/uploads/photo-gallery" . $p->gallery_preview_image)
            );

            if (strpos($p->random_preview_image, 'http:') !== false && strpos($p->random_preview_image, 'https:') !== false) {
                $r += array(
                    'gallery_random_preview_image' => N2ImageHelper::dynamic($siteUrl . "/wp-content/uploads/photo-gallery" . $p->random_preview_image)
                );
            } else {
                $r += array(
                    'gallery_random_preview_image' => $p->random_preview_image
                );
            }

            $data[] = $r;
        }

        return $data;
    }

}
