<?php
N2Loader::import('libraries.form.elements.list');

class N2ElementWordPressAllTaxonomies extends N2ElementList {

	protected $isMultiple = true;

	protected $size = 10;

	protected $postSeparator = '_x_';

	public function __construct($parent, $name = '', $label = '', $default = '', array $parameters = array()) {
		parent::__construct($parent, $name, $label, $default, $parameters);

		$this->options['0'] = n2_('All');

		$taxonomyNames = get_taxonomies();

		foreach ($taxonomyNames as $taxonomyName) {
			$terms = get_terms(array(
				'taxonomy' => $taxonomyName
			));
			if (count($terms)) {
				$taxonomy = get_taxonomy($taxonomyName);
				$options  = array();
				foreach ($terms AS $term) {
					$options[$taxonomy->name . $this->postSeparator . $term->term_id] = '- ' . $term->name;
				}
				$this->optgroup[$taxonomy->label] = $options;
			}
		}
	}

	public function setPostSeparator($postSeparator) {
		$this->postSeparator = $postSeparator;
	}

}

