<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryList extends N2SSPluginItemFactoryAbstract {

    protected $type = 'list';

    protected $priority = 6;

    protected $layerProperties = array(
        "desktopportraitleft"   => 0,
        "desktopportraittop"    => 0,
        "desktopportraitwidth"  => 400,
        "desktopportraitalign"  => "left",
        "desktopportraitvalign" => "top"
    );

    private $font = 1304;
    private $listStyle = 1801;
    private $itemStyle = '';

    protected $class = 'N2SSItemList';

    public function __construct() {
        $this->title = n2_x('List', 'Slide item');
        $this->group = n2_x('Content', 'Layer group');
    }

    private function initDefaultFont() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-list-font');
            if (is_array($res)) {
                $this->font = $res['value'];
            }
            if (is_numeric($this->font)) {
                N2FontRenderer::preLoad($this->font);
            }
            $inited = true;
        }
    }


    private function initDefaultStyle() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-list-liststyle');
            if (is_array($res)) {
                $this->listStyle = $res['value'];
            }
            if (is_numeric($this->listStyle)) {
                N2StyleRenderer::preLoad($this->listStyle);
            }
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-list-itemstyle');
            if (is_array($res)) {
                $this->itemStyle = $res['value'];
            }
            if (is_numeric($this->itemStyle)) {
                N2StyleRenderer::preLoad($this->itemStyle);
            }
            $inited = true;
        }
    }

    public function onSmartsliderDefaultSettings($fontTab, $styleTab) {
        self::initDefaultFont();

        new N2ElementFont($fontTab, 'item-list-font', n2_('Item') . ' - ' . n2_('List'), $this->font, array(
            'previewMode' => 'list'
        ));

        self::initDefaultStyle();

        new N2ElementStyle($styleTab, 'item-list-liststyle', n2_('Item') . ' - ' . n2_('List'), $this->listStyle, array(
            'previewMode' => 'heading'
        ));

        new N2ElementStyle($styleTab, 'item-list-itemstyle', n2_('Item') . ' - ' . n2_('List') . ' - ' . n2_('Item'), $this->itemStyle, array(
            'previewMode' => 'heading'
        ));
    }

    function getValues() {
        self::initDefaultFont();
        self::initDefaultStyle();

        return array(
            'content'   => n2_("Item 1\nItem 2\nItem 3"),
            'font'      => $this->font,
            'liststyle' => $this->listStyle,
            'itemstyle' => $this->itemStyle,
            'type'      => 'disc'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public static function getFilled($slide, $data) {
        $data->set('content', $slide->fill($data->get('content', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('liststyle'));
        $export->addVisual($data->get('itemstyle'));
    }

    public function prepareImport($import, $data) {
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('liststyle', $import->fixSection($data->get('liststyle')));
        $data->set('itemstyle', $import->fixSection($data->get('itemstyle')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-list');

        new N2ElementTextarea($settings, 'content', n2_('Items'), '', array(
            'fieldStyle' => 'height: 120px; width: 230px;resize: vertical;'
        ));

        new N2ElementList($settings, 'type', n2_('List type'), '', array(
            'options' => array(
                'none'                 => n2_x('None', 'List layer type'),
                'disc'                 => n2_x('Disc', 'List layer type'),
                'square'               => n2_x('Square', 'List layer type'),
                'circle'               => n2_x('Circle', 'List layer type'),
                'decimal'              => n2_x('Decimal', 'List layer type'),
                'armenian'             => n2_x('Armenian', 'List layer type'),
                'cjk-ideographic'      => n2_x('Cjk-ideographic', 'List layer type'),
                'decimal-leading-zero' => n2_x('Decimal-leading-zero', 'List layer type'),
                'georgian'             => n2_x('Georgian', 'List layer type'),
                'hebrew'               => n2_x('Hebrew', 'List layer type'),
                'hiragana'             => n2_x('Hiragana', 'List layer type'),
                'hiragana-iroha'       => n2_x('Hiragana-iroha', 'List layer type'),
                'katakana'             => n2_x('Katakana', 'List layer type'),
                'katakana-iroha'       => n2_x('Katakana-iroha', 'List layer type'),
                'lower-alpha'          => n2_x('Lower-alpha', 'List layer type'),
                'lower-greek'          => n2_x('Lower-greek', 'List layer type'),
                'lower-latin'          => n2_x('Lower-latin', 'List layer type'),
                'lower-roman'          => n2_x('Lower-roman', 'List layer type'),
                'upper-alpha'          => n2_x('Upper-alpha', 'List layer type'),
                'upper-latin'          => n2_x('Upper-latin', 'List layer type'),
                'upper-roman'          => n2_x('Upper-roman', 'List layer type')
            )
        ));
        new N2ElementFont($settings, 'font', n2_('Font') . ' - ' . n2_('List'), '', array(
            'rowClass'    => 'n2-hidden',
            'previewMode' => 'list',
            'style'       => 'item_listliststyle',
            'style2'      => 'item_listitemstyle',
            'preview'     => '<ol style="list-style-type: {$(\'#item_listtype\').val()}" class="{styleClassName} {fontClassName}" style="width:{nextend.activeLayer.width()}px;">
   <li class="{styleClassName2}">Item 1</li>
   <li class="{styleClassName2}">Item 2</li>
   <li class="{styleClassName2}">Item 3</li>
</ol>'
        ));
        new N2ElementStyle($settings, 'liststyle', n2_('Style') . ' - ' . n2_('List'), '', array(
            'rowClass'    => 'n2-hidden',
            'previewMode' => 'heading',
            'font'        => 'item_listfont',
            'style2'      => 'item_listitemstyle',
            'preview'     => '<ol style="list-style-type: {$(\'#item_listtype\').val()}" class="{styleClassName} {fontClassName}" style="width:{nextend.activeLayer.width()}px;">
   <li class="{styleClassName2}">Item 1</li>
   <li class="{styleClassName2}">Item 2</li>
   <li class="{styleClassName2}">Item 3</li>
</ol>'
        ));
        new N2ElementStyle($settings, 'itemstyle', n2_('Style') . ' - ' . n2_('Item'), '', array(
            'rowClass'    => 'n2-hidden',
            'previewMode' => 'heading',
            'font'        => 'item_listfont',
            'style2'      => 'item_listliststyle',
            'preview'     => '<ol style="list-style-type: {$(\'#item_listtype\').val()}" class="{styleClassName2} {fontClassName}" style="width:{nextend.activeLayer.width()}px;">
   <li class="{styleClassName}">Item 1</li>
   <li class="{styleClassName}">Item 2</li>
   <li class="{styleClassName}">Item 3</li>
</ol>'
        ));
    }
}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryList);
