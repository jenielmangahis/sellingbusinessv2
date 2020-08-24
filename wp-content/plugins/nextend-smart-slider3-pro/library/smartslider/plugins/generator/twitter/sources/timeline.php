<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorTwitterTimeline extends N2GeneratorAbstract {

    protected $layout = 'social_post';

    private $resultPerPage = 50;
    private $pages = array();
    private $client;

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'album', n2_('Album'));

        new N2ElementOnOff($filter, 'retweets', 'Include retweets', 1);
        new N2ElementOnOff($filter, 'replies', 'Exclude replies', 0);
        new N2ElementOnOff($filter, 'remove_spec_chars', 'Remove special characters', 0);
        new N2ElementText($filter, 'dateformat', 'Date format', 'Y-m-d');

    }

    protected function _getData($count, $startIndex) {
        $this->client = $this->group->getConfiguration()
                                    ->getApi();

        $data = array();
        try {

            $offset            = $startIndex;
            $limit             = $count;
            $remove_spec_chars = $this->data->get('remove_spec_chars', 0);
            for ($i = 0, $j = $offset; $j < $offset + $limit; $i++, $j++) {

                $items = $this->getPage(intval($j / $this->resultPerPage));

                if (isset($items[$j % $this->resultPerPage])) {
                    $item = $items[$j % $this->resultPerPage];
                } else {
					$item = null;
				}

                if (empty($item)) {
                    // There is no more item in the list
                    break;
                }
                $record['author_name']  = $item['user']['screen_name'];
                $record['author_url']   = $item['user']['url'];
                $record['author_image'] = str_replace('_normal.', '.', $item['user']['profile_image_url_https']);
                if ($remove_spec_chars) {
                    $record['message'] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $this->makeClickableLinks($item['full_text']));
                } else {
                    $record['message'] = $this->makeClickableLinks($item['full_text']);
                }

                $item['id']          = number_format($item['id'], 0, '.', '');
                $record['url']       = 'https://twitter.com/statuses/' . $item['id_str'];
                $record['url_label'] = n2_('View tweet');

                if (!empty($item['entities']['media'][0]['media_url'])) {
                    $record['tweet_image'] = $item['entities']['media'][0]['media_url'];
                } else if(isset($item['retweeted_status']['entities']['media'][0]['media_url'])) {
                    $record['tweet_image'] = $item['retweeted_status']['entities']['media'][0]['media_url'];
                }
                if (!empty($item['entities']['media'][0]['media_url_https'])) {
                    $record['tweet_image_https'] = $item['entities']['media'][0]['media_url_https'];
                } else if(isset($item['retweeted_status']['entities']['media'][0]['media_url_https'])) {
                    $record['tweet_image_https'] = $item['retweeted_status']['entities']['media'][0]['media_url_https'];
                }

                $record['userid']           = $item['user']['id'];
                $record['user_name']        = $item['user']['name'];
                $record['user_description'] = $item['user']['description'];
                $record['user_location']    = $item['user']['location'];

                if (isset($item['retweeted_status'])) {
                    $record['tweet_author_name']  = $item['retweeted_status']['user']['screen_name'];
                    $record['tweet_author_image'] = str_replace('_normal.', '.', $item['retweeted_status']['user']['profile_image_url_https']);
                } else {
                    $record['tweet_author_name']  = $record['author_name'];
                    $record['tweet_author_image'] = $record['author_image'];
                }
				
				        $record['created_at'] = date($this->data->get('dateformat', 'Y-m-d'), strtotime($item['created_at']));

                $data[$i] = &$record;
                unset($record);

            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }

        return $data;
    }

    private function getPage($page) {
        if (!isset($this->pages[$page])) {
            $request = array(
                'count'           => $this->resultPerPage,
                'include_rts'     => $this->data->get('retweets', '1'),
                'exclude_replies' => $this->data->get('replies', '0'),
                'tweet_mode' => 'extended'
            );
            if ($page != 0) {
                $previousPage      = $this->getPage($page - 1);
                $request['max_id'] = $previousPage[count($previousPage) - 1]['id'];
            }
            $responseCode = $this->client->request('GET', $this->client->url('1.1/statuses/user_timeline'), $request);
            if ($responseCode == 200) {
                $this->pages[$page] = json_decode($this->client->response['response'], true);
            }
        }

        return $this->pages[$page];
    }
}